<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_rating".
 *
 * @property int $id
 * @property int $user_id
 * @property string $show_rating
 * @property double $admin_rating
 * @property double $users_rating
 * @property int $created_at
 * @property int $updated_at
 */
class UserRating extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_rating';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['show_rating'], 'string'],
            [['admin_rating', 'users_rating'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'show_rating' => 'Show Rating',
            'admin_rating' => 'Admin Rating',
            'users_rating' => 'Users Rating',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
