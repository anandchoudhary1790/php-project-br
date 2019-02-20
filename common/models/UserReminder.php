<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_reminder".
 *
 * @property int $id
 * @property int $user_id
 * @property int $appointment_id
 * @property int $reminder_datetime
 * @property int $reminder_date
 * @property int $reminder_time
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserReminder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_reminder';
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
            [['appointment_id', 'reminder_datetime','reminder_date','reminder_time'], 'required'],
            [['appointment_id', 'user_id'], 'integer'],
            [['status'], 'string'],
            [['reminder_datetime','reminder_date','reminder_time'], 'safe'],
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
            'appointment_id' => 'Appointment ID',
            'reminder_date' => 'Reminder Date',
            'reminder_time' => 'Reminder Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
