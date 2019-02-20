<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_schedule_group".
 *
 * @property int $id
 * @property int $user_id
 * @property int $schedule_id
 * @property int $shift
 * @property string $shift_label
 * @property string $date
 * @property string $weekday
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserScheduleGroup extends \yii\db\ActiveRecord
{


    const BOOKING_CLOSED_FALSE = 0;
    const BOOKING_CLOSED_TRUE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_schedule_group';
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
            [['user_id', 'schedule_id', 'shift_label', 'date'], 'required'],
            [['user_id', 'schedule_id'], 'integer'],
            [['date'], 'safe'],
            [['weekday', 'status'], 'string'],
            [['shift_label'], 'string', 'max' => 255],
            [['shift_belongs_to','attender_id','hospital_id','shift','address_id','start_time','end_time'],'safe'],

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
            'schedule_id' => 'Schedule ID',
            'shift' => 'Shift',
            'shift_label' => 'Shift Label',
            'date' => 'Date',
            'weekday' => 'Weekday',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserScheduleGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserScheduleGroupQuery(get_called_class());
    }
}
