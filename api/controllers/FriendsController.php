<?php

namespace app\controllers;
use app\models\Friends;
use Yii;

class FriendsController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Friends';
	
	public function behaviors() {
    	$behaviors = parent::behaviors();

		$behaviors['authenticator'] = [
			'class' => \sizeg\jwt\JwtHttpBearerAuth::class,
			'except' => [
				'options'
			],
		];

		return $behaviors;
	}
	
	public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
		unset($actions['update']);
		unset($actions['delete']);
		unset($actions['options']);
        return $actions;
    }
	
	public static function actionCreate(){
		$request = Yii::$app->request;
		$friends=new Friends();
		$friends->setScenario('create');
		$friends->setAttributes($request->getBodyParams());
		$friends->status_id=1;
		$friends->save();
		return array('request' => Friends::getFullData($friends->request_id));
	}
	
	public static function actionUpdate($id){
		$request = Yii::$app->request;
		$friends = Friends::findOne($id);
		$friends->status_id=$request->getBodyParam('status_id');
		$friends->save();
		return array('id' => $id);
	}
	
	public static function actionDelete($id){
		$friends = Friends::findOne($id);
		if($friends->status_id==2) {
			$is_friends = true;
		} else {
			$is_friends = false;
		}
		$friends->delete();
		return array('id' => $id, 'is_friends' => $is_friends);
	}
	
	public static function actionRequest($id) {
		$current_user_id = Yii::$app->user->identity->user_id;
		$friends=Friends::getByUsersId($id, $current_user_id);
		if($friends) {
			if($friends['status_id']==2) {
				$friends['is_friends']=true;
			} else if ($friends['from_user_id']=$current_user_id) {
				$friends['is_outgoing_request']=true;
			} else {
				$friends['is_incoming_request']=true;
			}
			return $friends;
		} else {
			return array('is_not_friends' => true);
		}
		return $friends;
	}
	
	public static function actionOptions() {
		$url= Yii::$app->request->url;
		if (strpos($url, 'request')){
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