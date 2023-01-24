<?php

namespace app\models;

use yii\db\ActiveRecord;

class FVisibilities extends ActiveRecord
{
	public static function find() {
		return (new \yii\db\Query())
			->select(['visibility_id as value', 'visibility as label'])
			->from('field_visibilities')
			->orderBy('visibility_id');
	}
	
	public static function tableName() {
        return '{{field_visibilities}}';
    }
}