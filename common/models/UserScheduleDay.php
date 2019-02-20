<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_schedule_day".
 *
 * @property int $id
 * @property int $user_id
 * @property string $date
 * @property string $weekday
 * @property int $start_time
 * @property int $end_time
 * @property string $shift
 * @property int $patient_limit
 * @property int $address_id
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserScheduleDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_schedule_day';
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
            [['user_id', 'date', 'status','schedule_id'], 'required'],
            [['user_id', 'patient_limit', 'address_id','schedule_id'], 'integer'],
            [['date'], 'safe'],
            [['weekday'], 'string'],
            [['status'], 'integer', 'max' => 4],
            [['consultation_fees', 'emergency_fees','consultation_fees_discount','emergency_fees_discount'], 'number'],
            [['consultation_days','emergency_days','consultation_show','emergency_show','booking_closed'], 'integer'],
            [['shift_belongs_to','attender_id','hospital_id','shift'],'safe'],
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
            'date' => 'Date',
            'weekday' => 'Weekday',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'shift' => 'Shift',
            'patient_limit' => 'Patient Limit',
            'address_id' => 'Address ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
