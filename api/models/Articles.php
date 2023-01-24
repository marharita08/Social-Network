<?php

namespace app\models;

use yii\db\ActiveRecord;

class Articles extends ActiveRecord
{
	public static function getFullData($id) {
		$article = (new \yii\db\Query())
			->select(['articles.article_id', 'articles.text', 'image', "to_char(created_at, 'DD.MM.YYYY HH24:MI:SS') as created_at", 
						'users.user_id', 'users.name', 'users.avatar', 'article_visibilities.visibility as v_name', 'articles.visibility_id'])
			->from('articles')
			->leftJoin('users', 'articles.user_id=users.user_id')
			->leftJoin('article_visibilities', 'article_visibilities.visibility_id=articles.visibility_id')
			->where(['article_id' => $id])
			->one();
		$article["visibility"]["label"]=$article["v_name"];
		$article["visibility"]["value"]=$article["visibility_id"];
		return $article;
	}
	
	public static function findAllNews($page, $limit) {
		return (new \yii\db\Query())
			->select(['articles.article_id', 'articles.text', 'image', "to_char(created_at, 'DD.MM.YYYY HH24:MI:SS') as created_at", 'users.user_id',
				'users.name', 'users.avatar', 'article_visibilities.visibility'])
			->from('articles')
			->innerJoin('users', 'articles.user_id=users.user_id')
			->innerJoin('article_visibilities', 'article_visibilities.visibility_id=articles.visibility_id')
			->orderBy('article_id DESC')
			->limit($limit)
			->offset($page * $limit - $limit)
			->all();
	}
	
	public static function countAllNews() {
		return (new \yii\db\Query())
			->select(['count(article_id)'])
			->from('articles')
			->one();
	}
	
	public static function findNews($userId, $page, $limit) {
		$query1 = (new \yii\db\Query())
			->select(['a.article_id', 'a.text', 'image', 'u.user_id', "to_char(created_at, 'DD.MM.YYYY HH24:MI:SS') as created_at", 'u.name',
				'u.avatar', 'v.visibility as v_name', 'a.visibility_id'])
			->from('articles as a')
			->innerJoin('users as u', 'a.user_id=u.user_id')
			->innerJoin('friends as f', "u.user_id = from_user_id and to_user_id=$userId or u.user_id = to_user_id and from_user_id=$userId")
			->innerJoin('status as s', 's.status_id=f.status_id and s.status=\'Accepted\'')
			->innerJoin('article_visibilities as v', 'v.visibility_id=a.visibility_id and v.visibility in(\'All\', \'Friends\')');
			
		$query2 = (new \yii\db\Query())
			->select(['a.article_id', 'a.text', 'image', 'u.user_id', "to_char(created_at, 'DD.MM.YYYY HH24:MI:SS') as created_at", 'u.name',
				'u.avatar', 'v.visibility as v_name', 'a.visibility_id'])
			->from('articles as a')
			->innerJoin('users as u', "a.user_id=u.user_id and u.user_id=$userId")
			->innerJoin('article_visibilities as v', 'v.visibility_id=a.visibility_id');
		$articles = (new \yii\db\Query())
			->from($query1->union($query2))
			->orderBy('article_id desc')
			->limit($limit)
			->offset($page * $limit - $limit)
			->all();
		
		foreach($articles as $article){
			$article["visibility"]["label"]=$article["v_name"];
			$article["visibility"]["value"]=$article["visibility_id"];
		}
		return $articles;
	}
	
	public static function countNews($id) {
		
		$query1 = (new \yii\db\Query())
			->select(['a.article_id'])
			->from('articles as a')
			->innerJoin('friends as f', "a.user_id = from_user_id and to_user_id=$id or a.user_id = to_user_id and from_user_id=$id")
			->innerJoin('status as s', 's.status_id=f.status_id and s.status=\'Accepted\'')
			->innerJoin('article_visibilities as v', 'v.visibility_id=a.visibility_id and v.visibility in(\'All\', \'Friends\')');
			
		$query2 = (new \yii\db\Query())
			->select(['a.article_id'])
			->from('articles as a')
			->where("user_id=$id");
		
		
		return (new \yii\db\Query())->from($query1->union($query2))->count();
	}
	
}