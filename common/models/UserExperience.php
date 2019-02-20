<?php
namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserExperienceQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
class UserExperience extends ActiveRecord 
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_experience}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserExperienceQuery(get_called_class());
    }

    /**
     * @inheritdoc
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
            [['user_id', 'hospital_name','start','end'], 'required'],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['start','end','is_till_now'],'safe'],
            [['end'], 'compare', 'compareAttribute'=>'start', 'operator'=>'>=', 'skipOnEmpty'=>true, 'message'=>'{attribute} must be greater than {compareValue} '],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('common', 'Username'),
            'hospital_name' => Yii::t('common', 'Hospital Name'),
            'status' => Yii::t('common', 'Status'),
            'is_till_now' => Yii::t('common', 'Till Now'),
            'start' => Yii::t('common', 'Start year'),
            'end' => Yii::t('common', 'End year'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
        ];
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active'),
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_DELETED => Yii::t('common', 'Deleted')
        ];
    }

    public function upsert($post){
        $edu_id=(isset($post['UserExperience']['id']))?$post['UserExperience']['id']:'';
        $model=UserExperience::findOne($edu_id);
        if(empty($model))
                $model = new UserExperience();
        $model->load($post);
     
       
        $model->start = strtotime($post['UserExperience']['start'].'-01-01');
        $model->end = $post['UserExperience']['end'] == 'Till Now' ? strtotime(date('Y-m-d')): strtotime($post['UserExperience']['end'].'-01-01') ;
     
        $model->is_till_now = $post['UserExperience']['end']>date('Y')?1 : 0;
       
        if($model->save()){
            return $model;
        }
        else {
            echo '<pre>';
            print_r($model->getErrors());die;
        }

        return NULL;
    }

    
}
