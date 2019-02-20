<?php
namespace backend\models;

use common\models\Groups;
use common\models\User;
use common\models\UserProfile;
use common\models\UserSchedule;
use common\models\HospitalAttender;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class AttenderEditForm extends Model
{
    public $email;
    public $name;
    public $gender;
    public $phone;
    public $parent_id;
    public $address_id;
    public $doctor_id;
    public $dob;
    public $avatar;
    public $avatar_path;
    public $avatar_base_url;
    public $blood_group;
    public $groupid;
    public $countrycode;
    public $shift_id;
    public $created_by;
    public $token;
    public $device_id;
    public $device_type;
    public $id;
    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ['email', 'filter', 'filter' => 'trim'],
        ['email', 'required'],
        ['email', 'email'],
        ['email', 'checkUnique'],
        [['name','phone'], 'required'],
        [['phone','countrycode','blood_group'], 'string', 'max' => 45],
        ['phone','is10NumbersOnly'],
        [['name','avatar','avatar_path','avatar_base_url'], 'string', 'max' => 255],
        [['token','device_id','device_type','shift_id','address_id','id','doctor_id'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        'name' => Yii::t('common', 'Full Name'),
        'email' => Yii::t('common', 'Email'),
        'phone' => Yii::t('common', 'Mobile Number'),
        'gender' => Yii::t('common', 'Gender'),
        'dob' => Yii::t('common', 'Date of Birth'),
        'bloodgroup' => Yii::t('common', 'Blood Group'),


        ];
    }

    /**
     * @param User $model
     * @return mixed
     */
    public function setModel($model)
    {
        $this->username = $model->username;
        $this->email = $model->email;
        $this->status = $model->status;
        $this->model = $model;
        $this->roles = ArrayHelper::getColumn(
            Yii::$app->authManager->getRolesByUser($model->getId()),
            'name'
            );
        return $this->model;
    }

    /**
     * @return User
     */
    public function getModel($id=NULL)
    {
        if (!$this->model) {
            if($id){
                $this->model = User::findOne($id);
                
            }else{
                $this->model = new User();
            }
        }
        return $this->model;
    }

    /**
     * Signs user up.
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    public function update()
    {
        if ($this->validate()) {

            $userprofile= UserProfile::find()->andWhere(['user_id'=>$this->id])->one();
            $user = $this->getModel($this->id);
            $user->isNewRecord=NULL;
            $user->email = $this->email;
            $user->phone=$this->phone;
            if(isset($this->token)){
                $user->token = $this->token;
                $user->device_id = $this->device_id;
                $user->device_type = $this->device_type;
            }
            if (isset($this->password)) {
                $user->setPassword($this->password);
            }
            if(!$user->save()) {
                throw new Exception("User couldn't be  saved");
            }
            /*if(!empty($this->shift_id)){
                $shifts=explode(',', $this->shift_id);
                foreach ($shifts as $key => $shift_id) {
                    $shift=UserSchedule::findOne($shift_id);
                    if($shift){
                        $shift->attender_id=$user->id;
                        $shift->save();
                    }
                }   

            }*/

            // Attender Hospital Update 

            $doctorIds = array();
            if(!empty($this->doctor_id)){
                $doctorIds = implode(',', $this->doctor_id);
            }

            if(isset($this->doctor_id) && !empty($this->doctor_id)){
                $doctors=explode(',', $doctorIds);
                foreach ($doctors as $key => $doctor_id) {
                    $doctor=HospitalAttender::find()->where(['doctor_id'=>$doctor_id])->one();
                    if(!empty($doctor)){
                        HospitalAttender::deleteAll(['attender_id'=>$doctor->attender_id,'hospital_id'=>$doctor->hospital_id,'doctor_id' => $doctor->doctor_id]);
                    }

                }
            }
            
            $profileData['name'] = $this->name;

            if($this->avatar)
            {
                $profileData['avatar'] = $this->avatar;
                $profileData['avatar_base_url'] = $this->avatar_base_url;
                $profileData['avatar_path'] = $this->avatar_path;
            }
            $profileData['gender'] = isset($this->gender)?$this->gender:$userprofile->gender;
            if($this->dob){
                $profileData['dob'] = $this->dob;
            }
            if(isset($this->blood_group)){
                $profileData['blood_group'] = $this->blood_group;
            }
            else{
                $profileData['blood_group'] = '';
            }


            $userprofile->load(['UserProfile'=>$profileData]);
            $userprofile->save();
            return $user;
        }
        return null;
    }

    
    public function is10NumbersOnly()
    {
        if (!preg_match('/^[0-9]{10}$/', $this->phone)) {
            $this->addError('phone', 'phone number exactly 10 digits.');
        }else{
            $id=($this->id)?$this->id:NULL;
            $isExists=User::checkMobileNumber($this->phone,Groups::GROUP_ATTENDER,$id);
            if($isExists){
                $this->addError('phone', 'This phone number has already been taken.');
            }
        }
    }

    public function checkUnique()
    {
        $isExists=User::find()->andWhere(['email'=>$this->email])->andWhere(['!=','id',$this->id])->one();
        if($isExists){
            $this->addError('email', 'This email address has already been taken.');
        }
    }

}
