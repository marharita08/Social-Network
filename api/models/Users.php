<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Session;
use Yii;

class Users extends ActiveRecord implements \yii\web\IdentityInterface
{	
	public static function getProfileById($id) {
		return (new \yii\db\Query())
			->select(['u.*', 'us.*', 'un.name as university_label', 'ev.visibility as ev_label', 'pv.visibility as pv_label', 'uv.visibility as uv_label'])
			->from('users u')
			->leftJoin('user_settings us', 'u.user_id=us.user_id')
			->leftJoin('field_visibilities ev', 'email_visibility_id=ev.visibility_id')
			->leftJoin('field_visibilities pv', 'phone_visibility_id=pv.visibility_id')
			->leftJoin('field_visibilities uv', 'university_visibility_id=uv.visibility_id')
			->leftJoin('universities un', 'u.university_id=un.university_id')
			->where(['u.user_id' => $id])
			->one();
	}
	
	public static function getFriends($id) {
		return (new \yii\db\Query())
			->select(['request_id', 'user_id', 'name', 'avatar'])
			->from('users u')
			->innerJoin('friends f', "u.user_id=from_user_id and to_user_id=$id or u.user_id=to_user_id and from_user_id=$id")
			->innerJoin('status s', 's.status_id=f.status_id and s.status=\'Accepted\'')
			->orderBy('name')
			->all();
	}
	
	public static function getIncomingRequests($id) {
		return (new \yii\db\Query())
			->select(['request_id', 'user_id', 'name', 'avatar'])
			->from('users u')
			->innerJoin('friends f', "u.user_id=from_user_id and to_user_id=$id")
			->innerJoin('status s', 's.status_id=f.status_id and s.status=\'Under consideration\'')
			->orderBy('name')
			->all();
	}
	
	public static function getOutgoingRequests($id) {
		return (new \yii\db\Query())
			->select(['request_id', 'user_id', 'name', 'avatar'])
			->from('users u')
			->innerJoin('friends f', "u.user_id=to_user_id and from_user_id=$id")
			->innerJoin('status s', 's.status_id=f.status_id and s.status=\'Under consideration\'')
			->orderBy('name')
			->all();
	}
	
	public static function getAllForSearch($id) {
		return (new \yii\db\Query())
			->select(['u.user_id', 'u.name', 'u.email', 'u.avatar', 'f.request_id',
				'is_not_friends' => new \yii\db\Expression('CASE WHEN status is null then true else false end'),
				'is_friends' => new \yii\db\Expression('case when s.status = \'Accepted\' then true else false end'),
				'is_outgoing_request' => new \yii\db\Expression("case when f.from_user_id=$id and s.status != 'Accepted' then true else false end"),
				'is_incoming_request' => new \yii\db\Expression("case when f.to_user_id=$id and s.status != 'Accepted' then true else false end")])
			->from('users u')
			->leftJoin('friends f', "u.user_id=from_user_id and to_user_id=$id or u.user_id=to_user_id and from_user_id=$id")
			->leftjoin('status s', 's.status_id=f.status_id')
			->where("u.user_id!=$id")
			->orderBy('u.name')
			->all();
	}
	
	public function loginByAccessToken($token, $type = null) {
		$user = self::findOne($token->getClaim('uid'));
		$user->university_id = 2;
		$user->save();
		return $user;
	}
	
	/**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $user = self::findOne($token->getClaim('uid'));
		$user->university_id = 2;
		$user->save();
		return $user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return self::findOne(['name' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
		$session = Session::findOne(['user_id' => $this->user_id]);
        return $session->token;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
		$session = Session::findOne(['user_id' => $this->user_id, 'token' => $authKey]);
		if ($session) {
			return true;
		}
		return false;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
	
}