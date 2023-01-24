<?php

namespace app\controllers;
use app\models\Likes;
use Yii;

class LikesController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Likes';
	
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
		unset($actions['delete']);
		unset($actions['options']);
        return $actions;
    }
	
	public static function actionCreate(){
		$like=new Likes();
		$like->setScenario('create');
		$request = Yii::$app->request;
		$like->article_id=$request->getBodyParam('article_id');
		$like->user_id=$request->getBodyParam('user_id');
		$like->save();
	}
	
	public static function actionDelete($id) {
		$like=Likes::findOne(['article_id'=>$id, 'user_id'=>Yii::$app->user->identity->user_id]);
		$like->delete();
	}
	
	public static function actionIsLiked($id) {
		$like=Likes::findOne(['article_id'=>$id, 'user_id'=>Yii::$app->user->identity->user_id]);
		if(isset($like)) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function actionOptions() {
		$url= Yii::$app->request->url;
		if (strpos($url, 'is-liked')){
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