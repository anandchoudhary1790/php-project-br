<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "meta_values".
 *
 * @property int $id
 * @property int $key
 * @property string $value
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class MetaValues extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const Image_Upload_Key_id=[5,9];//Speciality,Treatment
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meta_values';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'status','parent_key'], 'integer'],
            [['value','key','label','status'], 'required'],
            [['value','label'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'label' => 'Label',
            'value' => 'Value',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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

    public function getKeyName($key){
        $key=MetaKeys::findOne($key);
        return $key->label;
    }

    public function getValues($key,$parent_key=NULL){ 
        if($parent_key){
            return MetaValues::find()->orderBy('id asc')->andWhere(['or',['value' => $parent_key],['parent_key'=>$parent_key]])->andWhere(['key'=>$key])->all();
        }else{
            return MetaValues::find()->orderBy('id asc')->where(['key'=>$key])->all();
        }
    }

    public function getSpecialityName($key,$parent_key){
        $key=MetaValues::find()->orderBy('id asc')->andWhere(['parent_key'=>$parent_key])->andWhere(['key'=>$key])->one();
        return $key->label;
    }

    public function socialLinks()
    {
       return MetaValues::find()->orderBy('id asc')->andWhere(['key'=>3])->all();
    }

    public function copyright()
    {
        $copyrightText = MetaValues::find()->where(['label'=>'copyright'])->one();
        return  $copyrightText->value;

    }
}
