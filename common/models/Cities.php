<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property int $state_id
 * @property int $code
 * @property string $name
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class Cities extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * @return array
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
            [['state_id'], 'required'],
            [['state_id'], 'integer'],
            [['status'], 'string'],
            [['lat','lng'],'safe'],
            [['name','code'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'state_id' => 'State ID',
            'code' => 'Code',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getIdByName($name){
        $rslt= Cities::find()->where(['name'=>$name])->select(['id'])->one();
        if($rslt){
            return $rslt->id;
        }
        return 0;
    }

    public function getStateName($id){
        $state=States::find()->where(['code'=>$id])->one();
        if($state){
            return $state->name;
        }
        return '';
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE => Yii::t('common', 'Active'),
            self::STATUS_NOT_ACTIVE => Yii::t('common', 'Not Active')
        ];
    }
}
