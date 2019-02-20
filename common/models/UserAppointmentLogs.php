<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_appointment_logs".
 *
 * @property int $id
 * @property int $appointment_id
 * @property string $booking_type
 * @property string $type
 * @property int $token
 * @property int $user_id
 * @property string $user_name
 * @property string $user_age
 * @property string $user_phone
 * @property string $user_address
 * @property int $user_gender
 * @property int $doctor_id
 * @property string $doctor_name
 * @property string $doctor_address
 * @property int $doctor_address_id
 * @property double $doctor_fees
 * @property string $date
 * @property string $weekday
 * @property int $start_time
 * @property int $end_time
 * @property string $shift_name
 * @property int $schedule_id
 * @property int $slot_id
 * @property string $book_for
 * @property string $payment_type
 * @property double $service_charge
 * @property string $status
 * @property int $comment
 * @property int $created_at
 * @property int $updated_at
 */
class UserAppointmentLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_appointment_logs';
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
            [['appointment_id', 'token', 'user_name', 'user_age', 'user_phone', 'doctor_id', 'doctor_name', 'doctor_address', 'doctor_address_id', 'doctor_fees', 'date', 'weekday', 'start_time', 'end_time', 'shift_name', 'schedule_id', 'slot_id', 'comment'], 'required'],
            [['appointment_id', 'token', 'user_id', 'user_gender', 'doctor_id', 'doctor_address_id', 'start_time', 'end_time', 'schedule_id', 'slot_id', 'comment'], 'integer'],
            [['booking_type', 'type', 'book_for', 'payment_type', 'status'], 'string'],
            [['doctor_fees', 'service_charge'], 'number'],
            [['date'], 'safe'],
            [['user_name', 'doctor_name', 'weekday', 'shift_name'], 'string', 'max' => 45],
            [['user_age'], 'string', 'max' => 10],
            [['user_phone','doctor_phone','booking_id'], 'string', 'max' => 15],
            [['user_address', 'doctor_address','shift_label'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appointment_id' => 'Appointment ID',
            'booking_type' => 'Booking Type',
            'type' => 'Type',
            'token' => 'Token',
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_age' => 'User Age',
            'user_phone' => 'User Phone',
            'user_address' => 'User Address',
            'user_gender' => 'User Gender',
            'doctor_id' => 'Doctor ID',
            'doctor_name' => 'Doctor Name',
            'doctor_address' => 'Doctor Address',
            'doctor_address_id' => 'Doctor Address ID',
            'doctor_fees' => 'Doctor Fees',
            'date' => 'Date',
            'weekday' => 'Weekday',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'shift_name' => 'Shift Name',
            'schedule_id' => 'Schedule ID',
            'slot_id' => 'Slot ID',
            'book_for' => 'Book For',
            'payment_type' => 'Payment Type',
            'service_charge' => 'Service Charge',
            'status' => 'Status',
            'comment' => 'Comment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAppointmentLogsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAppointmentLogsQuery(get_called_class());
    }
}
