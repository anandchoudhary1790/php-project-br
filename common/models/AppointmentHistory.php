<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "appointment_history".
 *
 * @property int $id
 * @property int $user_id
 * @property string $sheet_path
 * @property string $sheet_base_url
 * @property int $created_at
 * @property int $updated_at
 */
class AppointmentHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appointment_history';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['sheet_path', 'sheet_base_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'sheet_path' => 'Sheet Path',
            'sheet_base_url' => 'Sheet Base Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
