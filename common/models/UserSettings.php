<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_settings".
 *
 * @property int $id
 * @property int $user_id
 * @property string $key_name
 * @property string $key_value
 * @property int $created_at
 * @property int $updated_at
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_settings';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id','key_value'], 'integer'],
            [['key_name'], 'string'],
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
            'key_name' => 'Key Name',
            'key_value' => 'Key Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
