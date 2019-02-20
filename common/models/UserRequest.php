<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property int $state_id
 * @property int $code
 * @property string $name
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserRequest extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const Requested=1;
    const Request_Confirmed=2;
    const Request_Cancelled=3;
    
    public static function tableName()
    {
        return 'user_request';
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
        [['request_from','request_to'], 'required'],
        [['status','groupid'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        'id' => 'ID',
        'request_from' => 'Request From User',
        'request_to' => 'Request To User',
        'status' => 'Status',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        ];
    }

    public function checkExists($post)
    {
        return UserRequest::find()->andWhere(['request_to'=>$post['user_id']])->andWhere(['request_from'=>$post['id']])->one();
    }

    public function updateStatus($post,$type=NULL)
    {
        $model=UserRequest::find()->andWhere(['request_to'=>$post['request_to']])->andWhere(['request_from'=>$post['request_from']])->one();
   
        if($type=='Add' && empty($model)){
            $model = new UserRequest();
            
        }else if(!empty($model) && $type=='edit'){
            unset($post['groupid']);
        }
        $model->load(['UserRequest'=>$post]);
        if($model->save()){
            return true;
        }else{
            return false;
        }
    }

    public function requestedUser($searchData=[],$coloum){
        $coloum=($coloum =='request_from')?'request_to':'request_from';
        $query = UserRequest::find()->andWhere($searchData)->all();
        
        if(count($query)>0){
            foreach ($query as $key => $value) {
                $result[]=$value->$coloum;
            }
            //array_push($result,$id);
        }else{
            $result=[];
        }
        return $result;
    }
    
    public function statusValue($index=0){
        $list=[UserRequest::Requested=>'Requested',UserRequest:: Request_Confirmed=>'Confirmed',UserRequest:: Request_Cancelled=>'Cancelled'];

        if($index){  
            return $list[$index];
        }else{
            return $list;
        }
    }




}
