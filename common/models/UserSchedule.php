<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_schedule".
 *
 * @property int $id
 * @property int $user_id
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
class UserSchedule extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const SHIFT_MORNING='morning';
    const SHIFT_AFTERNOON='afternoon';
    const SHIFT_EVENING='evening';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_schedule';
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
            [['user_id', 'patient_limit',], 'required'],
            [['user_id', 'patient_limit', 'address_id','shift'], 'integer'],
            [['weekday', ], 'string'],
            [['status'], 'integer', 'max' => 4],
            [['consultation_fees', 'emergency_fees','appointment_time_duration','consultation_fees_discount','emergency_fees_discount','start_time','end_time'], 'number'],
            [['consultation_days','emergency_days','consultation_show','emergency_show'], 'integer'],
            [['shift_belongs_to','attender_id','hospital_id'],'safe'],

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

    public function shiftNumberCount($user_id,$week_day){
        $result=UserSchedule::find()->andWhere(['user_id'=>$user_id])->andWhere(['weekday'=>$week_day])->count();
        if(count($result)>0){
             $result=$result+1; 
        }else{
             $result=1;
        }
        return $result; 
    }

    public function dayShiftTotal($user_id,$week_day){
        return UserSchedule::find()->andWhere(['user_id'=>$user_id])->andWhere(['weekday'=>$week_day])->count();
         
    }

    /*
    ## return true/false
    */
    public function validShiftTime($model){

        $start_time=strtotime($model->start_time);
        $end_time=strtotime($model->end_time);
        $result = UserSchedule::find()->andWhere(['user_id'=>$model->user_id])
                                      ->andWhere(['weekday'=>$model->weekday])
                                      ->andWhere(['not in','id',$model->id])
                                      ->all();
        if($result){
            foreach ($result as $key => $row) {
                $start_time=strtotime($row->start_time);
                $end_time=strtotime($row->end_time);
            if($shiftExits){ 
                 $model->addError('start_time','please select valid start time');
                $model->addError('end_time','please select valid end time');
                return $model; exit;
            }

            }
        }else{
            return 0;
        }
    }

    public function addedShift($user_id,$week_day=null){
        if($week_day){
            return UserSchedule::find()
                                ->andWhere(['user_id'=>$user_id])
                                ->andWhere(['weekday'=>$week_day])->all();
        }else{
        return UserSchedule::find()
                            ->andWhere(['user_id'=>$user_id])
                            ->groupBy(['weekday'])
                            ->all();
        }
    }

    public function setSingleShiftData($post){
        return UserSchedule::find()
                            ->andWhere(['user_id'=>$post['user_id']])
                            ->andWhere(['id'=>$post['id']])
                            ->one();
    }


    public function shiftTimingSave()
    {
        if ($this->validate()) {
            $userschedule = UserSchedule::findOne(Yii::$app->user->id);
            $userschedule->user_id = $this->user_id;
            $userschedule->start_time = $this->start_time;
            $userschedule->end_time = $this->end_time;
            if ($userschedule->save()) {
                return $userschedule;
            }
        }
        return null;
    }

    public function getTripNightCount ($starttime,$endtime,$nytStartTime,$nytEndTime) {
        $nytCount=0;

        $nytStartTimeSec=strtotime("01-01-1970 ".$nytStartTime);
        $nytEndTimeSec2=strtotime("01-01-1970 ".$nytEndTime);

        if($nytCount==0){
            $timeonly= date('H:i:s',$starttime);
            $timeonlySec=strtotime("01-01-1970 ".$timeonly);
            if($timeonlySec>=$nytStartTimeSec){
                $nytCount=1;
            }else if($timeonlySec<=$nytEndTimeSec2){
                $nytCount=1;
            }

        }

        if($nytCount==0){
            $timeonly= date('H:i:s',$endtime);
            $timeonlySec=strtotime("01-01-1970 ".$timeonly);
            if($timeonlySec>=$nytStartTimeSec){
                $nytCount=1;
            }else if($timeonlySec<=$nytEndTimeSec2){
                $nytCount=1;
            }
        }

        $duration=$endtime-$starttime;
        $diff=($duration/(24*60*60));
        $nytCount=$nytCount+intval($diff);

        return $nytCount;
    }

    
}
