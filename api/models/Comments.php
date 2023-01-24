<?php

namespace app\models;

use yii\db\ActiveRecord;

class Comments extends ActiveRecord
{
	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios['create'] = ['article_id', 'user_id', 'text', 'level', 'parent_id', 'commented_at', 'path'];
		return $scenarios;
	}
	
	public static function getFullDataById($id) {
		return (new \yii\db\Query())
			->select(['ch.comment_id', 'ch.article_id', 'ch.user_id', 'ch.text', 'ch.parent_id', 'ch.path', 'ch.level', 'ch.commented_at',
				'chu.name','chu.avatar', 'pu.name as to', 'p.user_id as p_user_id', 'p.text as parent_text'])
			->from('comments as ch')
			->leftJoin('comments as p', 'p.comment_id=ch.parent_id')
			->leftJoin('users as pu', 'pu.user_id=p.user_id')
			->leftJoin('users as chu', 'chu.user_id=ch.user_id')
			->where("ch.comment_id=$id")
			->one();
	}
	
	public static function getByArticleId($id) {
		return (new \yii\db\Query())
			->select(['ch.comment_id', 'ch.article_id', 'ch.user_id', 'ch.text', 'ch.parent_id', 'ch.path', 'ch.level', 'ch.commented_at',
				'chu.name','chu.avatar', 'pu.name as to', 'p.user_id as p_user_id', 'p.text as parent_text'])
			->from('comments as ch')
			->leftJoin('comments as p', 'p.comment_id=ch.parent_id')
			->leftJoin('users as pu', 'pu.user_id=p.user_id')
			->leftJoin('users as chu', 'chu.user_id=ch.user_id')
			->where("ch.article_id=$id")
			->orderBy('ch.path')
			->all();
	}
	
	public static function getAmountByArticleId($id) {
		return (new \yii\db\Query())
			->select(['comment_id'])
			->from('comments')
			->where("article_id=$id")
			->count();
	}
}