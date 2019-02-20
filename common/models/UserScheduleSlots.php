<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_schedule_slots".
 *
 * @property int $id
 * @property int $user_id
 * @property int $schedule_id
 * @property string $date
 * @property string $weekday
 * @property string $type
 * @property int $start_time
 * @property int $end_time
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserScheduleSlots extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_schedule_slots';
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
            [['schedule_id', 'date', 'weekday','user_id'], 'required'],
            [['schedule_id', 'start_time', 'end_time'], 'integer'],
            [['date'], 'safe'],
            [['weekday', 'type', 'status','shift_name','shift_label'], 'string'],
            [['fees','fees_discount'],'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'schedule_id' => 'Schedule ID',
            'date' => 'Date',
            'weekday' => 'Weekday',
            'type' => 'Type',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserScheduleSlotsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserScheduleSlotsQuery(get_called_class());
    }
}
