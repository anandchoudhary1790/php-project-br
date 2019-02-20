<?php
namespace backend\models;

use yii\base\Model;
use Yii;

/**
 * User Booking form
 */
class AddAppointmentForm extends Model{

    public $user_id;
    public $name;
    public $age;
    public $phone;
    public $address;
    public $gender;
    public $payment_type;
    public $date;
    public $slot;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','name','age','phone','gender','payment_type','date','slot'], 'required'],
            [['user_id'], 'integer'],
            ['phone','number'],
            [['name','age','address'], 'string'],
            [['address'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'name'=>'Full name',
            'age' => 'Age',
            'phone' => 'Mobile Number',
            'address' => 'Address',
            'gender' => 'Gender',
            'payment_type' => 'Payment Type',
        ];
    }
}
