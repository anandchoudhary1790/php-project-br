<?php
namespace backend\models;

use yii\base\Model;
use Yii;

/**
 * User Schedule form
 */
class DailyPatientLimitForm extends Model{

    const SHIFT_MORNING='morning';
    const SHIFT_AFTERNOON='afternoon';
    const SHIFT_EVENING='evening';

    public $user_id;

    public $shift_one;
    public $shift_one_start;
    public $shift_one_end;
    public $shift_one_address;
    public $shift_one_patient;
    public $shift_one_cfees;
    public $shift_one_efees;
    public $shift_one_cdays;
    public $shift_one_edays;

    public $shift_two;
    public $shift_two_start;
    public $shift_two_end;
    public $shift_two_address;
    public $shift_two_patient;
    public $shift_two_cfees;
    public $shift_two_efees;
    public $shift_two_cdays;
    public $shift_two_edays;

    public $shift_three;
    public $shift_three_start;
    public $shift_three_end;
    public $shift_three_address;
    public $shift_three_patient;
    public $shift_three_cfees;
    public $shift_three_efees;
    public $shift_three_cdays;
    public $shift_three_edays;

    public $date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['shift_two_end', 'shift_one_end', 'shift_one_start', 'shift_two_start','shift_three_start','shift_three_end'], 'safe'],
            [['date'],'required'],
            [['shift_one_address','shift_two_address','shift_three_address'], 'safe'],
            [['shift_one_patient','shift_two_patient','shift_three_patient'], 'safe'],
            [['shift_one_cfees','shift_two_cfees','shift_three_cfees'], 'safe'],
            [['shift_one_cdays','shift_two_cdays','shift_three_cdays'], 'safe'],
            [['shift_one_efees','shift_two_efees','shift_three_efees'], 'safe'],
            [['shift_one_edays','shift_two_edays','shift_three_edays'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'date'=>'Select Date',
            'shift_one_start' => 'From',
            'shift_one_end' => 'To',
            'shift_two_start' => 'From',
            'shift_two_end' => 'To',
            'shift_three_start' => 'From',
            'shift_three_end' => 'To',
            'shift_one_address'=>'Hospitals/Clinics',
            'shift_two_address'=>'Hospitals/Clinics',
            'shift_three_address'=>'Hospitals/Clinics',
            'shift_one_patient'=>'Patient Limit',
            'shift_two_patient'=>'Patient Limit',
            'shift_three_patient'=>'Patient Limit',
            'shift_one_cfees'=>'Consultancy Fee',
            'shift_two_cfees'=>'Consultancy Fee',
            'shift_three_cfees'=>'Consultancy Fee',
            'shift_one_efees'=>'Emergency Fee',
            'shift_two_efees'=>'Emergency Fee',
            'shift_three_efees'=>'Emergency Fee',
            'shift_one_cdays'=>'Valid Days',
            'shift_two_cdays'=>'Valid Days',
            'shift_three_cdays'=>'Valid Days',
            'shift_one_edays'=>'Valid Days',
            'shift_two_edays'=>'Valid Days',
            'shift_three_edays'=>'Valid Days',

        ];
    }
}