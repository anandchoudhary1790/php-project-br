<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "states".
 *
 * @property int $id
 * @property int $code
 * @property string $name
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class States extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'states';
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
            [['code'], 'required'],
            [['code'], 'integer'],
            [['status'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getIdByName($name){
        $rslt= States::find()->where(['name'=>$name])->select(['code'])->one();
        if($rslt){
            return $rslt->code;
        }
        return 0;
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
