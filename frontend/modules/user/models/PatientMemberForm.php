<?php
namespace frontend\modules\user\models;

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
class PatientMemberForm extends Model
{

    public $name;
    public $gender;
    public $phone;

    public $file;

    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['name','gender','phone'], 'required'],
            [['file'], 'file', 'extensions' => ['png', 'jpg', 'gif','jpeg'], 'maxFiles' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('common', 'Full Name'),
            'phone' => Yii::t('common', 'Mobile Number'),
            'gender' => Yii::t('common', 'Gender')
        ];
    }
}
