<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\UserSchedule;

/**
 * User Schedule form
 */
class AddScheduleForm extends Model{

    const SHIFT_MORNING='morning';
    const SHIFT_AFTERNOON='afternoon';
    const SHIFT_EVENING='evening';

    public $id;
    public $user_id;
    public $shift_belongs_to;

    public $attender_id;
    public $hospital_id;

    public $patient_limit;
    public $start_time;
    public $end_time;
    public $address_id;
    public $shift;
    public $weekday;
    public $status;
    public $consultation_fees;
    public $consultation_fees_discount;
    public $emergency_fees;
    public $emergency_fees_discount;
    public $appointment_time_duration;
    public $emergency_days;
    public $consultation_days;
    public $consultation_show;
    public $emergency_show;
    public $is_edit;

    public $model;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'start_time','end_time','appointment_time_duration','address_id', 'status'], 'required'],
            [['consultation_fees','weekday','emergency_fees'],'required'],
            [['user_id', 'patient_limit', 'address_id','shift'], 'integer'],
            [['weekday', ], 'string'],
            [['status'], 'integer', 'max' => 4],
            [['consultation_fees'], 'integer','min'=>0],
            [['emergency_fees'], 'integer','min'=>0],
            [['appointment_time_duration','patient_limit'], 'integer','min'=>0],
            [['consultation_fees_discount'], 'integer','min'=>0],
            [['emergency_fees_discount'], 'integer','min'=>0],
            // ['consultation_fees_discount', 'compare','compareAttribute'=>'consultation_fees','operator'=>'<',
            // 'message'=>'Consultation discount fees should be less than consultation fees', 'type' => 'number'],
            // ['emergency_fees_discount', 'compare','compareAttribute'=>'emergency_fees','operator'=>'<',
            // 'message'=>'Emergency discount fees should be less than emergency fees', 'type' => 'number'],
            
            [['consultation_days','emergency_days','consultation_show','emergency_show'], 'integer'],
            [['shift_belongs_to','hospital_id','is_edit','id','attender_id'],'safe']

        ];

        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'attender_id'=> 'Attender User',
            'weekday'=>'Select Days',
            'start_time' => 'From',
            'end_time' => 'To',
            'address_id'=>'Hospitals/Clinics',
            'patient_limit'=>'Patient Limit',
            'consultation_fees'=>'Consultancy Fee',
            'consultation_fees_discount'=>'Discounted Consultancy Fee',
            'emergency_fees'=>'Emergency Fee',
            'emergency_fees_discount'=>'Discounted Emergency Fee',

        ];
    }

    public function getModel()
    {
        return $model= new UserSchedule();
    }

    public function validShiftTime($model,$schedule=[]){

        /*
        $result=UserSchedule::validShiftTime($start_time,$end_time);
        return (count($result)>0)?0:1;
        */
       
        $start_time=strtotime($model->start_time);
        $end_time=strtotime($model->end_time);
        if($end_time<$start_time){ 
            $model->addError('start_time','please select valid start time');
            $model->addError('end_time','please select valid end time');
            //return $model;
        }else{
                $error=false;
                $weekday=[]; 
                if(isset($model->id) && !empty($model->id)) { 
                     $dbSTime = UserSchedule::find()->andWhere(['user_id'=>$model->user_id])
                                          ->andWhere(['weekday'=>$model->weekday[0]])
                                          ->andWhere(['not in','id',$model->id])
                                          ->orderBy(['start_time'=>SORT_DESC])
                                          ->one();
                    $dbETime = UserSchedule::find()->andWhere(['user_id'=>$model->user_id])
                                          ->andWhere(['weekday'=>$model->weekday[0]])
                                          ->andWhere(['not in','id',$model->id])
                                          ->orderBy(['end_time'=>SORT_DESC])
                                          ->one();


                        if($dbSTime && $dbETime){ 
                                $dbStime=date('h:i a',$dbSTime->start_time); 
                                $dbStime=strtotime(date('Y-m-d ').$dbStime); 
                                $dbEtime=date('h:i a',$dbETime->end_time); 
                                $dbEtime=strtotime(date('Y-m-d ').$dbEtime); 
                                $postStime=strtotime(date('Y-m-d ').$model->start_time); 
                                $postEtime=strtotime(date('Y-m-d ').$model->end_time); 

                                if($postStime<$dbEtime || $postEtime<$dbEtime ){ 
                                    $error=true;
                                    $weekday=$model->weekday;
                                }
                            }
                        if($error){ 
                            $model->addError('start_time','Shift Time not valid for '.implode(',',$weekday));
                            $model->addError('end_time','Shift Time not valid for '.implode(',',$weekday));
                            return $model; 
                        }else{ 
                            return 0;
                        }
                    }
                else{ 

                    foreach ($model->weekday as $key => $day) { 
                       
                    $dbSTime = UserSchedule::find()->andWhere(['user_id'=>$model->user_id])
                                          ->andWhere(['weekday'=>$day])
                                          ->orderBy(['start_time'=>SORT_DESC])
                                          ->one();
                    $dbETime = UserSchedule::find()->andWhere(['user_id'=>$model->user_id])
                                          ->andWhere(['weekday'=>$day])
                                          ->orderBy(['end_time'=>SORT_DESC])
                                          ->one();


                        if($dbSTime && $dbETime){ 

                            /*echo $dbStime=date('h:i a',$dbSTime->start_time); echo '<pre>';
                            echo $dbStime=strtotime(date('Y-m-d ').$dbStime); echo '<pre>';
                            echo $dbEtime=date('h:i a',$dbETime->end_time); echo '<pre>';
                            echo $dbEtime=strtotime(date('Y-m-d ').$dbEtime); echo '<pre>';
                            echo $postStime=strtotime(date('Y-m-d ').$model->start_time); echo '<pre>';
                            echo $postEtime=strtotime(date('Y-m-d ').$model->end_time);  echo '<pre>';*/
                            $dbStime=date('h:i a',$dbSTime->start_time); 
                            $dbStime=strtotime(date('Y-m-d ').$dbStime); 
                            $dbEtime=date('h:i a',$dbETime->end_time); 
                            $dbEtime=strtotime(date('Y-m-d ').$dbEtime); 
                            $postStime=strtotime(date('Y-m-d ').$model->start_time); 
                            $postEtime=strtotime(date('Y-m-d ').$model->end_time);  
                            if($postStime<$dbEtime || $postEtime<$dbEtime ){ 
                                $error=true;
                                $weekday[]=$day;    
                            }
                        }
                    } 
                    if($error){
                        $model->addError('start_time','Shift Time not valid for '.implode(',',$weekday));
                        $model->addError('end_time','Shift Time not valid for '.implode(',',$weekday));
                        return $model; 
                    }else{
                        return 0;
                    }
                
        }
    }
        return 0;
    }

    public function setSingleShiftData($UserSchedule){
        $this->user_id=$UserSchedule->user_id;
        $this->id=$UserSchedule->id;
        $this->shift_belongs_to=$UserSchedule->shift_belongs_to;
        $this->attender_id=$UserSchedule->attender_id;
        $this->hospital_id=$UserSchedule->hospital_id;
        $this->address_id=$UserSchedule->address_id;
        $this->shift=$UserSchedule->shift;
        $this->weekday=$UserSchedule->weekday;
        $this->start_time=date('h:i a',$UserSchedule->start_time);
        $this->end_time=date('h:i a',$UserSchedule->end_time);
        $this->status=$UserSchedule->status;
        return true;
    }
    // frontend
    public function setShiftData($userSchedule){
        $this->user_id=$userSchedule->user_id;
        $this->id=$userSchedule->id;
        $this->shift_belongs_to=$userSchedule->shift_belongs_to;
        $this->attender_id=$userSchedule->attender_id;
        $this->hospital_id=$userSchedule->hospital_id;
        $this->address_id=$userSchedule->address_id;
        $this->shift=$userSchedule->shift;
        $this->weekday=$userSchedule->weekday;
        $this->start_time=date('h:i a',$userSchedule->start_time);
        $this->end_time=date('h:i a',$userSchedule->end_time);
        $this->patient_limit=$userSchedule->patient_limit;
        $this->appointment_time_duration=$userSchedule->appointment_time_duration;
        $this->consultation_fees=$userSchedule->consultation_fees;
        $this->consultation_fees_discount=$userSchedule->consultation_fees_discount;
        $this->consultation_days=$userSchedule->consultation_days;
        $this->emergency_fees=$userSchedule->emergency_fees;
        $this->emergency_fees_discount=$userSchedule->emergency_fees_discount;
        $this->emergency_days=$userSchedule->emergency_days;
        $this->status=$userSchedule->status;
        $this->is_edit=$userSchedule->is_edit;
        return true;
    }
    // backend 
    public function setShiftDataAdmin($userSchedule){
      
        $this->user_id=$userSchedule->user_id;
        $this->id=$userSchedule->id;
        $this->shift_belongs_to=$userSchedule->shift_belongs_to;
        $this->attender_id=$userSchedule->attender_id;
        $this->hospital_id=$userSchedule->hospital_id;
        $this->address_id=$userSchedule->address_id;
        $this->shift=$userSchedule->shift;
        $this->weekday=$userSchedule->weekday;
        $this->start_time=date('h:i a',$userSchedule->start_time);
        $this->end_time=date('h:i a',$userSchedule->end_time);
        $this->patient_limit=$userSchedule->patient_limit;
        $this->appointment_time_duration=$userSchedule->appointment_time_duration;
        $this->consultation_fees=$userSchedule->consultation_fees;
        $this->consultation_fees_discount=$userSchedule->consultation_fees_discount;
        $this->consultation_days=$userSchedule->consultation_days;
        $this->emergency_fees=$userSchedule->emergency_fees;
        $this->emergency_fees_discount=$userSchedule->emergency_fees_discount;
        $this->emergency_days=$userSchedule->emergency_days;
        $this->status=$userSchedule->status;
        $this->is_edit=$userSchedule->is_edit;
        return true;
    }

    public function setPostData($postData){
        $this->weekday=$postData['AddScheduleForm']['weekday'];
        $this->start_time=$postData['AddScheduleForm']['start_time'][0];
        $this->end_time=$postData['AddScheduleForm']['end_time'][0];
        $this->appointment_time_duration=$postData['AddScheduleForm']['appointment_time_duration'][0];
        $this->consultation_fees=$postData['AddScheduleForm']['consultation_fees'][0];
        $this->consultation_fees_discount=$postData['AddScheduleForm']['consultation_fees_discount'][0];
        $this->emergency_fees=$postData['AddScheduleForm']['emergency_fees'][0];
        $this->emergency_fees_discount=$postData['AddScheduleForm']['emergency_fees_discount'][0];
        return true;
    }
}
