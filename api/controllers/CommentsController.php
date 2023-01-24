<?php

namespace app\controllers;

use app\models\Comments;
use Yii;

class CommentsController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Comments';
	
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
		unset($actions['options']);
        return $actions;
    }
	
	public static function actionCreate(){
		$date = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
		$comment = new Comments();
		$comment->setScenario('create');
		$comment->setAttributes(Yii::$app->request->getBodyParams());
		$comment->commented_at=$date->format('Y-m-d H:i:s');
		$comment->save();
		if (!isset($comment->path) || $comment->path == "") {
			$comment->path = $comment->comment_id;
		} else {
			$comment->path .= ".$comment->comment_id";
		}
		$comment->save();
		$fullComment = Comments::getFullDataById($comment->comment_id);
		return array('comment' => $fullComment);
	}
	
	public static function actionUpdate($id){
		$request = Yii::$app->request;
		$comment = Comments::findOne($id);
		$comment->text=$request->getBodyParam('text');
		$comment->save();
		return array('comment' => $comment);
	}
	
	
	public static function actionOptions() {
		$options = ['GET', 'POST', 'PUT', 'DELETE'];
        $headers = Yii::$app->getResponse()->getHeaders();
        $headers->set('Allow', implode(', ', $options));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $options));
		$headers->set('Access-Control-Allow-Origin', Yii::$app->params['frontUrl']);
		$headers->set('Access-Control-Allow-Credentials', true);
		$headers->set('Access-Control-Allow-Headers', ['*']);
	}
	
}