<?php
namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\models\User;
use common\models\Groups;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $identity;
    public $groupid;
    public $otp;
    public $rememberMe = true;

    private $user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['identity', 'groupid'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['identity', 'integer'],
            ['identity', 'validateUser'],
            ['otp','required','on'=>'otp'],
            ['otp', 'validateOtp'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'identity'=>Yii::t('frontend', 'Mobile Number'),
            'groupid'=>Yii::t('frontend', 'Login with'),
            'rememberMe'=>Yii::t('frontend', 'Remember Me'),
        ];
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateOtp()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateUser($this->identity,$this->groupid,$this->otp)) {
                    $this->addError('otp', Yii::t('frontend', 'Please Enter Valid OTP'));
               
            }
        }
    }

    public function validateUser()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateUser($this->identity,$this->groupid)) {
                $this->addError('identity', Yii::t('frontend', 'Mobile number not register with '.Groups::allgroups($this->groupid)));
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

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::find()
                ->active()
                ->andWhere(['groupid'=>$this->groupid])
                ->andWhere(['or', ['phone'=>$this->identity], ['email'=>$this->identity]])
                ->one();
        }

        return $this->user;
    }
}
