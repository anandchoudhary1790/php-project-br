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
class RequestForm extends Model
{
    public $id;
    

    private $model;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id','required'],
            
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'Doctor Name'),
        ];
    }

    /**
     * @param User $model
     * @return mixed
     */
   

    /**
     * @return User
     */
   

    /**
     * Signs user up.
     * @return User|null the saved model or null if saving fails
     * @throws Exception
     */
    

  


}
