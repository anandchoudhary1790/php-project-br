<?php
namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserEducationsQuery;
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
class UserEducations extends ActiveRecord 
{
    const STATUS_NOT_ACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_educations}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserEducationsQuery(get_called_class());
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
            [['user_id', 'collage_name','start','end','education'], 'required'],
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
            'collage_name' => Yii::t('common', 'Collage Name'),
            'status' => Yii::t('common', 'Status'),
            'is_till_now' => Yii::t('common', 'Till Now'),
            'start' => Yii::t('common', 'Start year'),
            'end' => Yii::t('common', 'End year'),
            'created_at' => Yii::t('common', 'Created at'),
            'updated_at' => Yii::t('common', 'Updated at'),
            'education'=>'Degree/Class'
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
        $edu_id=(isset($post['UserEducations']['id']))?$post['UserEducations']['id']:'';
        $model=UserEducations::findOne($edu_id);
        if(empty($model))
        $model = new UserEducations();
        $model->load($post);

        $model->start = strtotime($post['UserEducations']['start'].'-01-01');
        $model->end = $post['UserEducations']['end'] == 'Till Now' ? strtotime(date('Y-m-d')): strtotime($post['UserEducations']['end'].'-01-01') ;
        $model->is_till_now = $post['UserEducations']['end']== 'Till Now'?1 : 0;
        if($model->save()){
            return $model;
        }else{
            Yii::$app->session->setFlash('error', "'Doctor Education not Updated!'");
            return $model;
        }
        return NULL;
    }



    
}
