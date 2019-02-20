<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_appointment_temp".
 *
 * @property int $id
 * @property string $booking_id
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
 * @property string $doctor_phone
 * @property double $doctor_fees
 * @property int $attender_id
 * @property string $attender_name
 * @property string $attender_phone
 * @property string $date
 * @property string $weekday
 * @property string $shift_label
 * @property int $start_time
 * @property int $end_time
 * @property string $shift_name
 * @property int $schedule_id
 * @property int $slot_id
 * @property string $book_for
 * @property string $payment_type
 * @property double $service_charge
 * @property string $status
 * @property string $payment_status
 * @property int $is_deleted
 * @property string $deleted_by
 * @property int $created_at
 * @property int $updated_at
 * @property string $created_by
 * @property int $created_by_id
 */
class UserAppointmentTemp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_appointment_temp';
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
            [['booking_id', 'token', 'user_name', 'user_age', 'user_phone', 'doctor_id', 'doctor_name', 'doctor_address', 'doctor_address_id', 'doctor_phone', 'doctor_fees', 'date', 'weekday', 'shift_label', 'start_time', 'end_time', 'shift_name', 'schedule_id', 'slot_id'], 'required'],
            [['booking_type', 'type', 'book_for', 'payment_type', 'status', 'payment_status', 'deleted_by', 'created_by'], 'string'],
            [['token', 'user_id', 'user_gender', 'doctor_id', 'doctor_address_id', 'attender_id', 'start_time', 'end_time', 'schedule_id', 'slot_id', 'is_deleted', 'created_by_id'], 'integer'],
            [['doctor_fees', 'service_charge'], 'number'],
            [['date'], 'safe'],
            [['booking_id', 'user_phone', 'attender_phone'], 'string', 'max' => 15],
            [['user_name', 'doctor_name', 'doctor_phone', 'weekday', 'shift_name'], 'string', 'max' => 45],
            [['user_age'], 'string', 'max' => 10],
            [['user_address', 'doctor_address', 'attender_name', 'shift_label'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'Booking ID',
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
            'doctor_phone' => 'Doctor Phone',
            'doctor_fees' => 'Doctor Fees',
            'attender_id' => 'Attender ID',
            'attender_name' => 'Attender Name',
            'attender_phone' => 'Attender Phone',
            'date' => 'Date',
            'weekday' => 'Weekday',
            'shift_label' => 'Shift Label',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'shift_name' => 'Shift Name',
            'schedule_id' => 'Schedule ID',
            'slot_id' => 'Slot ID',
            'book_for' => 'Book For',
            'payment_type' => 'Payment Type',
            'service_charge' => 'Service Charge',
            'status' => 'Status',
            'payment_status' => 'Payment Status',
            'is_deleted' => 'Is Deleted',
            'deleted_by' => 'Deleted By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'created_by_id' => 'Created By ID',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAppointmentTempQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAppointmentTempQuery(get_called_class());
    }
}
