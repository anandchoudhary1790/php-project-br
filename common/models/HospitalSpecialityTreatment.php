<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "hospital_speciality_treatment".
 *
 * @property int $id
 * @property int $hospital_id
 * @property string $speciality
 * @property string $treatment
 * @property int $created_at
 * @property int $updated_at
 */
class HospitalSpecialityTreatment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hospital_speciality_treatment';
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
            [['hospital_id'], 'integer'],
            [['speciality', 'treatment'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_id' => 'Hospital ID',
            'speciality' => 'Speciality',
            'treatment' => 'Treatment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return HospitalSpecialityTreatmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new HospitalSpecialityTreatmentQuery(get_called_class());
    }
}
