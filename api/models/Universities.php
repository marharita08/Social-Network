<?php

namespace app\models;

use yii\db\ActiveRecord;

class Universities extends ActiveRecord
{
	public static function find() {
		return (new \yii\db\Query())
			->select(['university_id as value', 'name as label'])
			->from('universities')
			->orderBy('name');
	}
	
}