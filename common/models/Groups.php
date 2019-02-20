<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "groups".
 *
 * @property integer $id
 * @property string $name
 * @property string $alias
 * @property integer $show
 * @property string $created
 * @property string $modified
 */
class Groups extends \yii\db\ActiveRecord
{
    const GROUP_ACTIVE = 1;
    const GROUP_INACTIVE = 0;

    const GROUP_ADMIN = 1;
    const GROUP_MANAGER = 2;
    const GROUP_PATIENT = 3;
    const GROUP_DOCTOR = 4;
    const GROUP_HOSPITAL= 5;
    const GROUP_ATTENDER= 6;

    const GROUP_PATIENT_LABEL = 'patient';
    const GROUP_DOCTOR_LABEL = 'doctor';
    const GROUP_HOSPITAL_LABEL= 'hospital';
    const GROUP_ATTENDER_LABEL= 'attender';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groups';
    }

    /**
     * @return array
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
            [['name', 'show'], 'required'],
            [['show','search'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['alias'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'alias' => 'Alias',
            'show' => 'Show',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public static function allgroups($index=NULL)
    {   
        $groups= [
            self::GROUP_PATIENT => Yii::t('db', 'Patient'),
            self::GROUP_DOCTOR => Yii::t('db', 'Doctor'),
            self::GROUP_HOSPITAL => Yii::t('db', 'Hospital'),
            self::GROUP_ATTENDER => Yii::t('db', 'Attender'),
        ];
        return ($index)?$groups[$index]:$groups;
    }


}
