<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property integer $locale
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $name
 * @property string $picture
 * @property integer $groupid
 * @property string $avatar
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property integer $gender
 *
 * @property User $user
 */
class UserProfile extends ActiveRecord
{

    //const SCENARIO_ADMIN_PROFILE='adminprofile';
    //const SCENARIO_DOCTOR_PROFILE='doctorprofile';
    //const SCENARIO_PATIENT_PROFILE='patientprofile';
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;

    /**
     * @var
     */
    public $picture;
    public $inch;


    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
             'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'ensureUnique' => true,
                'immutable' => true,
            ]
            
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','gender','name'], 'required'],
            [['user_id', 'gender','groupid','experience'], 'integer'],
            [['gender'], 'in', 'range' => [NULL, self::GENDER_FEMALE, self::GENDER_MALE, self::GENDER_OTHER]],
             [['description'], 'filter', 'filter' => 'trim'],
            [['name', 'avatar', 'avatar_path', 'avatar_base_url','slug'], 'string', 'max' => 255],
            ['locale', 'default', 'value' => Yii::$app->language],
            ['locale', 'in', 'range' => array_keys(Yii::$app->params['availableLocales'])],
            [['avatar','dob','blood_group','degree','speciality','services','description','height','weight','marital','treatment','location','addres1','address2','prefix','city','state','country','created_by','slug'], 'safe'],
            [['consultation_fees','rating'], 'number'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('common', 'User ID'),
            'groupid' => Yii::t('common', 'Type'),
            'email' => Yii::t('common', 'E-mail'),
            'name' => Yii::t('common', 'Full Name'),
            'locale' => Yii::t('common', 'Locale'),
            'picture' => Yii::t('common', 'Picture'),
            'gender' => Yii::t('common', 'Gender'),
            'degree' => Yii::t('common', 'Degree'),
            'speciality' => Yii::t('common', 'Speciality'),
            'treatment' => Yii::t('common', 'Treatment'),
            'services' => Yii::t('common', 'Facility/Services'),
            'experience' => Yii::t('common', 'Experience'),
            'description' => Yii::t('common', 'Description'),
            'height' => Yii::t('common', 'Height'),
            'weight' => Yii::t('common', 'weight'),

        ];
    }

    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHospitalSpecialityTreatment()
    {
        return $this->hasOne(HospitalSpecialityTreatment::className(), ['hospital_id' => 'user_id']);
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        if ($this->name) {
            return $this->prefix.' '.$this->name;
        }
        return null;
    }

    /**
     * @param null $default
     * @return bool|null|string
     */
    public function getAvatar($default = null)
    {
        return $this->avatar_path
            ? Yii::getAlias($this->avatar_base_url . '/' . $this->avatar_path)
            : $default;
    }

    public function upsert($post,$user_id,$groupid){
      $model=UserProfile::findOne($user_id);

      if(empty($model))
        $model = new UserProfile();
      $model->load($post);
      if($model->save()){
        return $model;
      }
      return NULL;
    }
}


