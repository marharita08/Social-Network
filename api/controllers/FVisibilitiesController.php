<?php

namespace app\controllers;

use Yii;

class FVisibilitiesController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\FVisibilities';
	
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
		unset($actions['options']);
        return $actions;
    }
	
	public static function actionOptions() {
		$options = ['GET'];
        $headers = Yii::$app->getResponse()->getHeaders();
        $headers->set('Allow', implode(', ', $options));
        $headers->set('Access-Control-Allow-Methods', implode(', ', $options));
		$headers->set('Access-Control-Allow-Origin', Yii::$app->params['frontUrl']);
		$headers->set('Access-Control-Allow-Credentials', true);
		$headers->set('Access-Control-Allow-Headers', ['*']);
	}
}