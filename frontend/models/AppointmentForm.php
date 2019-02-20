<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class AppointmentForm extends Model
{
    public $user_name;
    public $user_phone;
    public $user_gender;
    public $doctor_id;
    public $slot_id;
    public $shift_id;
    public $schedule_id;
    public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['user_name', 'user_phone','user_gender'], 'required'],
            ['phone','integer'],
            [['phone','name'], 'required'],
            

        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'user_name' => Yii::t('frontend', 'Name'),
            'user_phone' => Yii::t('frontend', 'Phone Number'),
            'user_gender' => Yii::t('frontend', 'Gender'),
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string  $email the target email address
     * @return boolean whether the model passes validation
     */

}
