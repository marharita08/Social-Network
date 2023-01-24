<?php

namespace app\models;

use yii\db\ActiveRecord;

class Likes extends ActiveRecord
{
	public static function tableName() {
        return '{{article_likes}}';
    }
	
	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios['create'] = ['article_id', 'user_id'];
		return $scenarios;
	}
	
	public static function getByArticleId($id) {
		return (new \yii\db\Query())
			->select(['users.user_id', 'users.avatar'])
			->from('article_likes')
			->leftJoin('users', 'users.user_id=article_likes.user_id')
			->where(['article_id' => $id])
			->all();
	}
	
	public static function getAmountByArticleId($id) {
		return (new \yii\db\Query())
			->select('*')
			->from('article_likes')
			->where(['article_id' => $id])
			->count();
	}
	
	public static function primaryKey() {
		return [
			'article_id',
			'user_id',
		];
	}
}
