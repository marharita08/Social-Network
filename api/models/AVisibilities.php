<?php

namespace app\models;

use yii\db\ActiveRecord;

class AVisibilities extends ActiveRecord
{
	public static function find() {
		return (new \yii\db\Query())
			->select(['visibility_id as value', 'visibility as label'])
			->from('article_visibilities')
			->orderBy('visibility_id');
	}
	
	public static function tableName() {
        return '{{article_visibilities}}';
    }
}