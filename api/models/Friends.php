<?php

namespace app\models;

use yii\db\ActiveRecord;

class Friends extends ActiveRecord
{
	public function scenarios() {
		$scenarios = parent::scenarios();
		$scenarios['create'] = ['from_user_id', 'to_user_id', 'status_id'];
		return $scenarios;
	}
	
	public static function getByUsersId($userID, $currentUserID) {
		return (new \yii\db\Query())
			->select(['*'])
			->from('friends')
			->where(['and', ['from_user_id' => $userID], ['to_user_id' => $currentUserID]])
			->orWhere(['and', ['to_user_id' => $userID], ['from_user_id' => $currentUserID]])
			->one();
	}
	
	public static function getFullData($id) {
		return (new \yii\db\Query())
			->select(['request_id', 'user_id', 'name', 'avatar'])
			->from('friends')
			->leftJoin('users', 'user_id=to_user_id')
			->where(['request_id' => $id])
			->one();
	}
}