<?php
namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginFormType extends Model
{
    public $type;
    public $phone;
    public $otp;
    public $groupid;
    public $fieldType;
    public $rememberMe = true;

    private $user = false;

    /**
     * @inheritdoc
     */
    
    public function __construct($type=null)
        {
            parent::__construct();
            if($type){
                //$this->type=$type;
                $this->fieldType='otp';
            }else{
                $this->fieldType='phone';
            }
        }
    public function rules()
    {
        return [
            // username and password are both required
            [['type'], 'required'],
            [['type'], 'integer'],
            ['groupid','required','on'=>'login'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
           ['type', 'validateType'],
        ];
    }


    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['user'] = ['groupid','type'];//Scenario Values Only Accepted
    return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'type'=>Yii::t('frontend', ($this->fieldType=='otp')?'Otp':'Phone'),
            'groupid'=>Yii::t('frontend', 'Login with '),
           // 'rememberMe'=>Yii::t('frontend', 'Remember Me'),
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateType()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateType($this->fieldType,$this->type)) { 
                $msg=($fieldType='otp')?'Please Enter Valid OTP.':'Mobile Number Invalid';
                $this->addError($this->type, Yii::t('frontend', $msg));
            } 
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            if (Yii::$app->user->login($this->getUser(), $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0)) {
                return true;
            }
        }
        return false;
    }

    
    public function OtpScreen(){

        return User::find()
                ->andWhere( ['phone'=>$this->type])
                ->andWhere( ['groupid'=>$this->groupid])
                ->one();
    }

    public function loginBy($token)
    {  
  
       if ($this->validate() ) { 
                if (Yii::$app->user->login($this->loginUser($token), $this->rememberMe ? Time::SECONDS_IN_A_MONTH : 0)) {
                   $model=User::find()->andWhere(['access_token'=>$token])->one();
                   $model->mobile_verified=1;
                   if($model->save()){
                    return true;
                   }

                }
            }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser($token=null)
    {   
        if ($this->user === false) {
                $this->user = User::find()
                ->active()
                ->andWhere(['or',['phone'=>$this->type],['otp'=>$this->type]])
                ->one();    
        }
        return $this->user;
    }

    public function loginUser($token=null)
    {   
        return  $this->user = User::find()
                ->active()
                ->andWhere(['access_token'=>$token])
                ->one();
    }
}
