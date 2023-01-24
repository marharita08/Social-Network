<?php

namespace app\controllers;
use app\models\Articles;
use app\models\Comments;
use app\models\Likes;
use yii\helpers\StringHelper;
use Yii;

class ArticlesController extends \yii\rest\ActiveController
{
    public $modelClass = 'app\models\Articles';
	
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
		unset($actions['view']);
		unset($actions['create']);
		unset($actions['update']);
		unset($actions['delete']);
        return $actions;
    }
	
	private static function uploadFile($fileData) {
		$file = fopen($fileData, "r");
		$ext = StringHelper::explode(StringHelper::explode($fileData, ';')[0], '/')[1];
		$filename = 'images/article/' . uniqid() . '.' . $ext;
		$fp = fopen( $filename, "w" );
		while( $data = fread( $file, 1024 ) ) {
			fwrite( $fp, $data );
		}
		fclose( $fp );
		fclose( $file );
		return $filename;
	}
	
	public static function actionCreate() {
		$request = Yii::$app->request;
		
		$article = new Articles([
			'user_id' => $request->getBodyParam('user_id'),
			'text' => $request->getBodyParam('text'),
			'visibility_id' => $request->getBodyParam('visibility')['value'],
		]);
		$date = new \DateTime('now', new \DateTimeZone('Europe/Kiev'));
		$article->created_at=$date->format('Y-m-d H:i:s');
		
		$fileData = $request->getBodyParam("file");
		if (isset($fileData)) {
			$filename = self::uploadFile($fileData);
			$article->image = '/' . $filename;
		}
		$article->save();
		return Articles::getFullData($article->article_id);
	}
	
	public static function actionUpdate($id) {
		$request = Yii::$app->request;
		
		$article = Articles::findOne($id);
		$article->text=$request->getBodyParam('text');
		$article->visibility_id=$request->getBodyParam('visibility')['value'];
		
		$fileData = $request->getBodyParam("file");
		if (isset($fileData)) {
			$filename = self::uploadFile($fileData);
			if(isset($article->image)) {
				unlink(Yii::$app->basePath . '/web/' . $article->image);
			}
			$article->image = '/' . $filename;
		}
		$article->save();
		return Articles::getFullData($article->article_id);
	}
	
	public static function actionDelete($id) {
		$article=Articles::findOne($id);
		if(isset($article->image)) {
			unlink(Yii::$app->basePath . '/web/' . $article->image);
		}
		$article->delete();
	}
	
	public static function actionView($id) {
		return Articles::getFullData($id);
	}
	
	public static function actionGetLikes($id) {
		return Likes::getByArticleId($id);
	}
	
	public static function actionGetComments($id) {
		return Comments::getByArticleId($id);
	}
	
	public static function actionNews($id) {
        $page = $_GET['page'];
        $limit = $_GET['limit'];
		return Articles::findNews($id, $page, $limit);
	}
	public static function actionAllNews() {
        $page = $_GET['page'];
        $limit = $_GET['limit'];
		return Articles::findAllNews($page, $limit);
	}
	
	public static function actionAllNewsAmount() {
		return Articles::countAllNews();
	}
	
	public static function actionNewsAmount($id) {
		return Articles::countNews($id);
	}
	
	public static function actionLikesCount($id) {
		return Likes::getAmountByArticleId($id);
	}
	
	public static function actionCommentsCount($id) {
		return Comments::getAmountByArticleId($id);
	}
	
	public static function actionOptions() {
		$url= Yii::$app->request->url;
		if (strpos($url, 'news') || strpos($url, 'likes') || strpos($url, 'comments')) {
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