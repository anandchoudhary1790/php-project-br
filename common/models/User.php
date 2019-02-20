<?php
namespace common\models;

use common\commands\AddToTimelineCommand;
use common\components\DrsPanel;
use common\models\query\UserQuery;
use common\models\Groups;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\behaviors\SluggableBehavior;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property string $password write-only password
 *
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    const STATUS_ADMIN_PENDING='pending';
    const STATUS_ADMIN_REQUESTED='requested';
    const STATUS_ADMIN_APPROVED='approved';
    const STATUS_ADMIN_LIVE_APPROVED='live_approved';

    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMINISTRATOR = 'administrator';
    

    const EVENT_AFTER_SIGNUP = 'afterSignup';
    const EVENT_AFTER_LOGIN = 'afterLogin';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value' => Yii::$app->getSecurity()->generateRandomString()
            ],
            'access_token' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'value' => function () {
                    return Yii::$app->getSecurity()->generateRandomString(40);
                }
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
            parent::scenarios(),
            [
                'oauth_create' => [
                    'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                ]
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'unique'],
            ['phone', 'filter', 'filter' => 'trim'],
            ['phone', 'required'],
            ['phone','is10NumbersOnly'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['countrycode','groupid'],'safe'],
            [['token','device_id','device_type','admin_status','address_id'], 'safe'],

        ];
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('common', 'Username'),
            'email' => Yii::t('common', 'E-mail'),
            'countrycode'=> Yii::t('common', 'Code'),
            'phone' => Yii::t('common', 'Mobile Number'),
            'status' => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'logged_at' => Yii::t('common', 'Last login'),
        ];
    }

    
    public function is10NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{10}$/', $this->$attribute)) {
            $this->addError($attribute, 'phone number exactly 10 digits.');
        }
    }

    public function isGroupUnique($attribute)
    {
        if (!preg_match('/^[0-9]{10}$/', $this->$attribute)) {
            $this->addError($attribute, 'phone number exactly 10 digits.');
        }else{
            $isExists=User::checkMobileNumber($this->$attribute,$this->groupid,$this->getId());
            if($isExists){
                $this->addError($attribute, 'This phone number has already been taken.');
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAddress()
    {
        return $this->hasOne(UserAddress::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->active()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findBySlug($slug)
    {
        return static::find()
            ->active()
            ->andWhere(['slug' => $slug])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->active()
            ->andWhere(['access_token' => $token])
            ->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->active()
            ->andWhere(['username' => $username])
            ->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->active()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }


    public function validateType($filedType,$type)
    {
         return static::find()
            ->active()
            ->andWhere([$filedType => $type])
            ->one();

    }
    public function validateUser($identity,$groupid,$otp=NULL){

        $search['groupid']=$groupid;
        if($otp){
            $search['otp']=$otp;
        }
        return static::find()
            ->active()
            ->andWhere(['or',['email' => $identity],['phone'=> $identity]])
            ->andWhere($search)
            ->one();
    }

    public function ajaxUnique($post,$id=NULL){

        $phone=User::find()
            ->andWhere(['phone'=> $post['phone']])
            ->andWhere(['groupid'=> $post['groupid']])
            ->one();
        $email=User::find()
            ->andWhere(['email'=> $post['email']])
            ->one();
            $result['email']=($email)?true:false;
            $result['phone']=($phone)?true:false;
        return $result;
    }



    public function is10NumbersOnlyAccept($mobile)
    {
        if (!preg_match('/^[0-9]{10}$/', $mobile)) {
            return true;
        }
        return fales;
    }

    public function groupUniqueNumber($post){

        $phone=User::find()
            ->andWhere(['phone'=> $post['phone']])
            ->andWhere(['groupid'=> $post['groupid']])
            ->andWhere(['!=','id',$post['id']])
            ->one();
        return ($phone)?true:false;
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        
        
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active')
            //self::STATUS_DELETED => Yii::t('common', 'Deleted')
        ];
    }

    public static function getStatusLabel($id){
        $status=self::statuses();
        return $status[$id];

    }

    /**
     * Returns user admin statuses list
     * @return array|mixed
     */
    public static function admin_statuses()
    {
        return [
            self::STATUS_ADMIN_PENDING => Yii::t('common', 'Pending'),
            self::STATUS_ADMIN_REQUESTED => Yii::t('common', 'Requested for live'),
            self::STATUS_ADMIN_APPROVED => Yii::t('common', 'Profile Approved'),
            self::STATUS_ADMIN_LIVE_APPROVED => Yii::t('common', 'Profile Live'),
        ];
    }

    /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event' => 'signup',
            'data' => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id' => $this->getId(),
                'created_at' => $this->created_at
            ]
        ]));
        $profile = new UserProfile();
        $profile->locale = Yii::$app->language;
        $profile->load(['UserProfile'=>$profileData]);
        $profile->name = $this->clean($profileData['name']);
        $profile->slug = DrsPanel::userslugify($profileData['name']);
        if(isset($profileData['gender']) && !empty($profileData['gender']) && $profileData['gender'] != ''){
            $profile->gender = $profileData['gender'];
        }
        else{
            $profile->gender = 0;
        }
        $this->link('userProfile', $profile);          
        $this->trigger(self::EVENT_AFTER_SIGNUP);
        
        // Default role
        $auth = Yii::$app->authManager;
        $auth->assign($auth->getRole(User::ROLE_USER), $this->getId());
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        return $this->email;
    }

    public static function getEmailExist($email) {
        return self::find()->where(['email' => $email])->count();
    }

    public static function checkMobileNumber($phone,$groupid,$id=NUll){

        if($id){
        return User::find()->andWhere(['phone'=>$phone])->andWhere(['groupid'=>$groupid])->andWhere(['!=','id',$id])->one();
        }else{
        return User::find()->andWhere(['phone'=>$phone])->andWhere(['groupid'=>$groupid])->one();
        }
    }

    public static function checkLoginType($token){
        $user=User::find()->where(['access_token'=>$token])->one();
        if(!empty($user)){
            return User::find()->where(['phone'=>$user->phone])->all(); //->andWhere(['or',['groupid'=>Groups::GROUP_DOCTOR],['groupid'=>Groups::GROUP_PATIENT]])
        }else{
            return NUll;
        }
    }

      public function typeNames($type){
        $query = User::find()->andWhere(['email' => $type])->one();
        if(count($query)>0){
           return $query['userProfile']['name'];
        }else{
            $result = '';
        }
        return $result;
    }

    public function setUserName($username){
        $username = $this->clean($username);
        if($username){
        $user=User::find()->andWhere(['username'=>$username])->one();
        if($user){ 
            $user=User::find()->andWhere(['username'=>$username])->count();

            return $username.'-'.($user+1);
        }else{
            return $username;
        }
        }else{
            return $username;
        }
    }

        public function clean($string) {
        // $string = str_replace(' ', ' ', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
        }
}
