<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "hospital_attender".
 *
 * @property int $id
 * @property int $attender_id
 * @property int $hospital_id
 * @property int $address_id
 * @property int $doctor_id
 * @property int $created_at
 * @property int $updated_at
 */
class HospitalAttender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hospital_attender';
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
            [['attender_id', 'hospital_id', 'address_id', 'doctor_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'attender_id' => 'Attender ID',
            'hospital_id' => 'Hospital ID',
            'address_id' => 'Address ID',
            'doctor_id' => 'Doctor ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return HospitalAttenderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HospitalAttenderQuery(get_called_class());
    }
}
