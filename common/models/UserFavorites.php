<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_favorites".
 *
 * @property int $id
 * @property int $user_id
 * @property int $profile_id
 * @property string $status 0:Unfavorite,1:Favorite
 * @property int $created_at
 * @property int $updated_at
 */
class UserFavorites extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    /*
    const STATUS_UNFAVORITE='0';
    const STATUS_FROM_FAVORITE='1';
    const STATUS_TO_FAVORITE='2';
    const STATUS_BOTH_FAVORITE='3'; */

    const STATUS_UNFAVORITE='0';
    const STATUS_FAVORITE='1';

    public static function tableName()
    {
        return 'user_favorites';
    }

    /**
     * @inheritdoc
     */

     /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    public function rules()
    {
        return [
            [['user_id', 'profile_id'], 'required'],
            [['user_id', 'profile_id'], 'integer'],
            [['status'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Request From',
            'profile_id' => 'Request To',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserFavoritesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserFavoritesQuery(get_called_class());
    }
}
