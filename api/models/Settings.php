<?php

namespace app\models;

use yii\db\ActiveRecord;

class Settings extends ActiveRecord
{
	public static function tableName() {
        return '{{user_settings}}';
    }
}