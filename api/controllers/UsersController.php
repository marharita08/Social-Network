<?php

namespace app\controllers;

use app\models\Users;
use app\models\Session;
use app\models\Settings;
use yii\web\UploadedFile;
use yii\helpers\StringHelper;
use Yii;

class UsersController extends \yii\rest\ActiveController
{
	public $modelClass = 'app\models\Users';
	
	
	public function behaviors() {
    	$behaviors = parent::behaviors();
		
		$behaviors['authenticator'] = [
			'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
			'except' => [
				'login',
				'refresh',
				'options'
			],
		];

		return $behaviors;
	}
	
	public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
		unset($actions['options']);
		unset($actions['update']);
        return $actions;
    }
	
    private static function getProfile($id) {
		$profile = Users::getProfileById($id);
		if($profile) {
			$profile['email_visibility']['value']=$profile['email_visibility_id'];
			$profile['email_visibility']['label']=$profile['ev_label'];
			$profile['phone_visibility']['value']=$profile['phone_visibility_id'];
			$profile['phone_visibility']['label']=$profile['pv_label'];
			$profile['university_visibility']['value']=$profile['university_visibility_id'];
			$profile['university_visibility']['label']=$profile['uv_label'];
			if(isset($profile['university_id'])) {
				$profile['university']['value']=$profile['university_id'];
				$profile['university']['label']=$profile['university_label'];
			} else {
				$profile['university']=null;
			}
			return $profile;
		}
		return null;
	}
	
	public static function actionView($id) {
		return self::getProfile($id);
	}
	
	public static function actionFriends($id) {
		return Users::getFriends($id);
	}
	
	public static function actionIncomingRequests($id) {
		return Users::getIncomingRequests($id);
	}
	
	public static function actionOutgoingRequests($id) {
		return Users::getOutgoingRequests($id);
	}
	
	public static function actionSearch($id) {
		return Users::getAllForSearch($id);
	}
	
	public static function actionUpdate($id) {
		$request = Yii::$app->request;
		$user = Users::findOne($id);
		$user->name = $request->getBodyParam('name');
		$user->email = $request->getBodyParam('email');
		$user->university_id = $request->getBodyParam('university')['value'];
		
		$settings = Settings::findOne($id);
		$settings->email_visibility_id = $request->getBodyParam('email_visibility')['value'];
		$settings->phone_visibility_id = $request->getBodyParam('phone_visibility')['value'];
		$settings->university_visibility_id = $request->getBodyParam('university_visibility')['value'];
		
		
		$phone = $request->getBodyParam('phone');
		if ($phone === '') {
			$phone = null;
		}
		
		$password = $request->getBodyParam('password');
		if($password != '' && $password != $user->password) {
			$password = Yii::$app->getSecurity()->generatePasswordHash($password);
		} else {
			$password = null;
		}
		
		$fileData = $request->getBodyParam("file");
		if (isset($fileData)) {
			$file = fopen($fileData, "r");
			$ext = StringHelper::explode(StringHelper::explode($fileData, ';')[0], '/')[1];
			$filename = 'images/avatar/' . uniqid() . '.' . $ext;
			$fp = fopen( $filename, "w" );
			while( $data = fread( $file, 1024 ) ) {
				fwrite( $fp, $data );
			}
			fclose( $fp );
			fclose( $file );
			if(isset($user->avatar)) {
				unlink(Yii::$app->basePath . '/web/' . $user->avatar);
			}
		
			$user->avatar = '/' . $filename;
		}
	
		$user->save();
		$settings->save();
		
		
		return self::getProfile($id);
	}
	
	private static function generateJwt(Users $user) {
		$jwt = Yii::$app->jwt;
		$signer = $jwt->supportedAlgs['HS384'];
		$key = $jwt->key;
		$time = time();
		$jwtParams = Yii::$app->params['jwt'];
			
		return $jwt->getBuilder()
			->setIssuer($jwtParams['issuer'])
			->setAudience($jwtParams['audience'])
			->setId($jwtParams['id'], true)
			->setIssuedAt($time)
			->setExpiration($time + $jwtParams['expire'])
			->set('uid', $user->user_id)
			->sign(new $signer, $key)
			->getToken();
	}

	/**
	 * @throws yii\base\Exception
	 */
	private static function generateRefreshToken(Users $user) {
		$refreshToken = Yii::$app->security->generateRandomString(37);

		$session = new Session([
			'user_id' => $user->user_id,
			'token' => $refreshToken,
		]);
		if (!$session->save()) {
			throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. $session->getErrorSummary(true));
		}

		return $refreshToken;
	}
	
	public static function actionLogin() {
		$request = Yii::$app->request;
		$email=$request->getBodyParam('email');
		$password=$request->getBodyParam('password');
		
		$user = Users::findOne(['email'=>$email]);
		
		if(!$user) {
			$name = StringHelper::explode($email, '@')[0];
			$user = new Users([
				'name' => $name,
				'email' => $email,
				'password' => Yii::$app->getSecurity()->generatePasswordHash($password),
				'role' => 'user'
			]);
			$user->save();
			$settings=new Settings(['user_id' => $user->user_id]);
			$settings->save();
		} else if (!$user->validatePassword($password)) {
			throw new yii\web\UnauthorizedHttpException("Wrong password");
		}
		
		$accessToken = self::generateJwt($user);
        $refreshToken = self::generateRefreshToken($user);
		
		$headers = Yii::$app->getResponse()->getHeaders();
		$headers->set('Access-Control-Allow-Origin', Yii::$app->params['frontUrl']);
		
		return [
			'user' => $user,
			'accessToken' => (string) $accessToken,
			'refreshToken' => $refreshToken,
			'success' => true
		];
	}
	
	public static function actionRefresh() {
		$refreshToken = Yii::$app->request->getBodyParam('refreshToken');
		
		if (!$refreshToken) {
			return new \yii\web\UnauthorizedHttpException('No refresh token found.');
		}

		$session = Session::findOne(['token' => $refreshToken]);
		$user = Users::findOne($session->user_id);
		
		if (!$session) {
			return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
		}
		$accessToken = self::generateJwt($user);
		
		return [
			'accessToken' => (string)$accessToken,
			'refreshToken' => $refreshToken,
			'success' => true
		];
	}
	
	public static function actionLogout() {
		$refreshToken = Yii::$app->request->getBodyParam('refreshToken');
		$session = Session::findOne(['token' => $refreshToken]);
		$session->delete();
	}
	
	public static function actionOptions() {
		$url= Yii::$app->request->url;
		if(strpos($url, 'login') || strpos($url, 'logout') || strpos($url, 'refresh')){
			$options = ['POST'];
		} else if (strpos($url, 'friends') || strpos($url, 'incoming-requests') || strpos($url, 'outgoing-requests') || strpos($url, 'search')) {
			$options = ['GET'];
		} else {
			$options = ['GET', 'POST', 'PUT', 'DELETE'];
		}
        $headers = Yii::$app->getResponse()->getHeaders();
        $headers->set('Allow', implode(', ', $options));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $options));
		$headers->set('Access-Control-Allow-Origin', Yii::$app->params['frontUrl']);
		$headers->set('Access-Control-Allow-Credentials', true);
		$headers->set('Access-Control-Allow-Headers', ['*']);
	}
}