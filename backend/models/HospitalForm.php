<?php
namespace backend\models;

use common\models\Groups;
use common\models\User;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Create user form
 */
class HospitalForm extends Model
{
    public $email;
    public $name;
    public $gender;
    public $phone;
    public $dob;
    public $picture;
    public $blood_group;
    public $groupid;
    public $countrycode;
    public $admin_user_id;
    public $token;
    public $device_id;
    public $device_type;

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
            [['name','phone','countrycode'], 'required'],
            [['phone','countrycode'], 'string', 'max' => 45],
            ['phone','is10NumbersOnly'],
            [['name'], 'string', 'max' => 255],
            [['token','device_id','device_type'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('common', 'Hospital Name'),
            'email' => Yii::t('common', 'Email'),
            'phone' => Yii::t('common', 'Mobile Number'),
            'gender' => Yii::t('common', 'Gender'),
            'dob' => Yii::t('common', 'Establishment Date'),


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
        $this->admin_user_id=$model->admin_user_id;
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
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new User();
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
            $user->countrycode=$this->countrycode;
            $user->groupid=Groups::GROUP_HOSPITAL;
            $user->phone=$this->phone;
            $user->status = User::STATUS_ACTIVE;
            $user->admin_status = User::STATUS_ADMIN_PENDING;
            $user->admin_user_id=$this->admin_user_id;
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
            };
            $profileData=array();
            $profileData['name'] = $this->name;
            $profileData['groupid'] = $this->groupid;
            $profileData['gender'] = $this->gender;
            $profileData['email'] = $this->email;
            $profileData['dob'] = $this->dob;
            $profileData['blood_group'] = '';

            $user->afterSignup($profileData);
            /*if (!$shouldBeActivated) {
                $token = UserToken::create(
                    $user->id,
                    UserToken::TYPE_ACTIVATION,
                    Time::SECONDS_IN_A_DAY
                );
                $name   = $this->firstname . ' ' . $this->lastname;
                $mailsend = new MailSend();
                $mailsend->sendMail($name, $this->email, $token->token);
            }*/
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
            $isExists=User::checkMobileNumber($this->$attribute,Groups::GROUP_HOSPITAL);
            if($isExists){
                $this->addError($attribute, 'This phone number has already been taken.');
            }
        }
    }


}
