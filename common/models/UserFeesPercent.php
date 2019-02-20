<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_fees_percent".
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property double $admin
 * @property double $user_provider
 * @property double $user_patient
 * @property int $created_at
 * @property int $updated_at
 */
class UserFeesPercent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_fees_percent';
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
            [['type'], 'string'],
            [['admin', 'user_provider', 'user_patient'], 'number'],
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
            'type' => 'Type',
            'admin' => 'Admin',
            'user_provider' => 'User Provider',
            'user_patient' => 'User Patient',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
