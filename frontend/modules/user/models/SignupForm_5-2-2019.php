<?php
namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\User;
use common\models\Groups;
use common\models\UserToken;
use common\models\UserProfile;
use frontend\modules\user\Module;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;

/**
 * Signup form
 */
class SignupForm extends Model
{
    /**
     * @var
     */
    public $firstname;
    public $lastname;
    public $speciality;
    public $email;
    public $prefix;
    public $city;
    public $state;
    public $country;
    public $password;
    public $confirm_password;
    public $phone;
    public $address1;
    public $address2;
    public $username;
    public $practice_year;
    public $license_number;
    public $groupid;
    public $gender;
    public $dob;
    public $name;
    public $otp;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','groupid'], 'required'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass'=> '\common\models\User',
                'message' => Yii::t('frontend', 'This email address has already been taken.')
            ],
            ['phone', 'filter', 'filter' => 'trim'],
            ['phone', 'required'],
            ['phone','is10NumbersOnly'],
          /*  ['phone', 'integer'],
            ['phone', 'unique',
                'targetClass'=> '\common\models\User',
                'message' => Yii::t('frontend', 'This phone number has already been taken.')
            ],*/
            [['dob','prefix','gender'],'required','on'=>'user'],
            /*
            [['password', 'confirm_password',], 'required'],
            [['password', 'confirm_password'], 'string', 'min'=>6, 'max'=>40],
            ['password', 'compare', 'compareAttribute'=>'confirm_password','skipOnEmpty' => false], */
            
        ];
    }

    public function is10NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{10}$/', $this->$attribute)) {
            $this->addError($attribute, 'phone number exactly 10 digits.');
        }else{
            if($this->groupid){
                $isExists=User::checkMobileNumber($this->$attribute,$this->groupid);
                if($isExists){
                    $this->addError($attribute, 'This phone number has already been taken.');
                }
            }
            else{

                $this->addError($attribute, 'Register Type cannot be blank.');
            }
        }
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['user'] = ['dob','prefix','gender','name','groupid','email','phone'];//Scenario Values Only Accepted
    return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'address1'=>Yii::t('frontend', 'Address'),//Street or Landmarks
            'firstname'=>Yii::t('frontend', 'Name'),
            'email'=>Yii::t('frontend', 'E-mail'),
            'password'=>Yii::t('frontend', 'Password'),
            //'speciality'=>Yii::t('frontend', 'Speciality'),
            'prefix'=>Yii::t('frontend', 'Prefix Title'),
            'groupid'=>Yii::t('frontend','Register Type'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup($postData)
    {
               
        if ($this->validate()) { 
            $shouldBeActivated = $this->shouldBeActivated();
            $user = new User();
            $user->username = $user->setUserName($postData['SignupForm']['name']);
            $user->email = $this->email;
            $user->groupid = $this->groupid;
            if($user->groupid==Groups::GROUP_PATIENT)
                $user->admin_status='approved';
            $user->countrycode = '91';
            $user->phone = $this->phone;
            $user->otp = 1234;
            $user->status = $shouldBeActivated ? User::STATUS_NOT_ACTIVE : User::STATUS_ACTIVE;
            //$user->setPassword($this->password);
            if(!$user->save()) {  
                throw new Exception("User couldn't be  saved");
            }
            $profileData=$postData['SignupForm'];
            $user->afterSignup($profileData);
            if ($shouldBeActivated) {
                $token = UserToken::create(
                    $user->id,
                    UserToken::TYPE_ACTIVATION,
                    Time::SECONDS_IN_A_DAY
                );
                Yii::$app->commandBus->handle(new SendEmailCommand([
                    'subject' => Yii::t('frontend', 'Activation email'),
                    'view' => 'activation',
                    'to' => $this->email,
                    'params' => [
                        'url' => Url::to(['/user/sign-in/activation', 'token' => $token->token], true)
                    ]
                ]));
            }

            return $user;
        }
        return null;
    }

    
    /**
     * @return bool
     */
    public function shouldBeActivated()
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');
        if (!$userModule) {
            return false;
        } elseif ($userModule->shouldBeActivated) {
            return true;
        } else {
            return false;
        }
    }
}
