<?php
namespace backend\models;

use common\models\Groups;
use common\models\User;
use common\models\UserProfile;
use common\models\UserSchedule;
use common\components\DrsPanel;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class AttenderForm extends Model
{
    public $email;
    public $name;
    public $gender;
    public $phone;
    public $parent_id;
    public $address_id;
    public $dob;
    public $avatar;
    public $avatar_path;
    public $avatar_base_url;
    public $blood_group;
    public $groupid;
    public $countrycode;
    public $doctor_id;
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
        ['email', 'unique', 'targetClass'=> User::className(), 'filter' => function ($query) {
            if (!$this->getModel()->isNewRecord) {
                $query->andWhere(['not', ['id'=>$this->getModel()->id]]);
            }
        }],
        [['name','phone','parent_id','created_by'], 'required'],
        [['phone','countrycode','blood_group'], 'string', 'max' => 45],
        ['phone','is10NumbersOnly'],
        [['name','avatar_path', 'avatar_base_url'], 'string', 'max' => 255],
        [['token','device_id','avatar','device_type','doctor_id','shift_id','address_id','id'], 'safe'],

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
    public function signup()
    {
        if ($this->validate()) {
            $user = $this->getModel();
            $user->username = $user->setUserName($this->name);
            $user->email = $this->email;
            $user->parent_id = $this->parent_id;
            $user->countrycode=(isset($this->countrycode))?$this->countrycode:91;
            $user->groupid=Groups::GROUP_ATTENDER;
            $user->phone=$this->phone;
            $user->status = User::STATUS_ACTIVE;
            $user->admin_status = User::STATUS_ADMIN_PENDING;
            if(isset($this->token)){
                $user->token = $this->token;
                $user->device_id = $this->device_id;
                $user->device_type = $this->device_type;
            }
            if (isset($this->password)) {
                $user->setPassword($this->password);
            }
            if(!$user->save()) {
                //echo "<pre>"; print_r($user->getErrors());die;

                throw new Exception("User couldn't be  saved");
            }
            $profileData=array();
            $profileData['name'] = $this->name;
            $profileData['created_by'] = $this->created_by;
            $profileData['groupid'] = Groups::GROUP_ATTENDER;
            $profileData['gender'] = isset($this->gender)?$this->gender:0;
            $profileData['email'] = $this->email;
            $profileData['avatar'] = $this->avatar;
            $profileData['avatar_path'] = $this->avatar_path;
            $profileData['avatar_base_url'] = $this->avatar_base_url;
            if($this->dob){
                $profileData['dob'] = $this->dob;
            }
            if(isset($this->blood_group)){
                $profileData['blood_group'] = $this->blood_group;
            }
            else{
                $profileData['blood_group'] = '';
            }
            $user->afterSignup($profileData);

            if(isset($this->doctor_id) && !empty($this->doctor_id)){
                $doctors=explode(',', $this->doctor_id);
                $addupdateHospitalDoctors=DrsPanel::addUpdateDoctorsToHospitalAttender($doctors,$user->id,$user->parent_id);
            }
            

            return $user;
        }
        else{
           //echo "<pre>"; print_r($this->getErrors());die;
        }

        return null;
    }



    
    public function is10NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{10}$/', $this->$attribute)) {
            $this->addError($attribute, 'phone number exactly 10 digits.');
        }else{
            $id=($this->id)?$this->id:NULL;
            $isExists=User::checkMobileNumber($this->$attribute,Groups::GROUP_ATTENDER,$id);
            if($isExists){
                $this->addError($attribute, 'This phone number has already been taken.');
            }
        }
    }
}
