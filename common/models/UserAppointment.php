<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\query\UserAppointmentQuery;

/**
 * This is the model class for table "user_appointment".
 *
 * @property int $id
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
 * @property int $created_at
 * @property int $updated_at
 */
class UserAppointment extends \yii\db\ActiveRecord
{

    const BOOKING_TYPE_ONLINE = 'online';
    const BOOKING_TYPE_OFFLINE = 'offline';

    const TYPE_CONSULTATION = 'consultation';
    const TYPE_EMERGENCY = 'emergency';

    const BOOK_FOR_SELF='self';
    const BOOK_FOR_OTHER='other';

    const STATUS_PENDING = 'pending';
    const STATUS_AVAILABLE = 'available';
    const STATUS_ACTIVE = 'active';
    const STATUS_SKIP = 'skip';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const PAYMENT_PENDING = 'pending';
    const PAYMENT_COMPLETED = 'completed';

    const PAYMENT_TYPE_CASH = 'cash';
    const PAYMENT_TYPE_ALREADYPAID = 'already_paid';
    const PAYMENT_TYPE_PAYTM = 'paytm';


    const GROUP_ADMIN = 'admin';
    const GROUP_PATIENT = 'patient';
    const GROUP_DOCTOR = 'doctor';
    const GROUP_HOSPITAL= 'hospital';
    const GROUP_ATTENDER= 'attender';
    const GROUP_HOSPITAL_ATTENDER= 'hospital_attender';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_appointment}}';
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
            [['booking_type', 'type', 'book_for', 'payment_type', 'status','payment_status','created_by'], 'string'],
            [['token', 'user_name', 'user_phone', 'doctor_id', 'doctor_name', 'doctor_address', 'doctor_address_id', 'doctor_fees', 'date', 'weekday', 'start_time', 'end_time', 'shift_name', 'schedule_id', 'slot_id'], 'required'],
            [['token', 'user_id', 'user_gender', 'doctor_id', 'doctor_address_id', 'start_time', 'end_time', 'schedule_id', 'slot_id','attender_id','created_by_id'], 'integer'],
            [['doctor_fees', 'service_charge'], 'number'],
            [['date'], 'safe'],
            [['user_name', 'doctor_name', 'weekday', 'shift_name'], 'string', 'max' => 45],
            [['user_age'], 'string', 'max' => 10],
            [['user_phone','doctor_phone','attender_phone','booking_id'], 'string', 'max' => 15],
            [['user_address', 'doctor_address','attender_name','shift_label'], 'string', 'max' => 255],
            ['user_age','safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAppointmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAppointmentQuery(get_called_class());
    }


}
