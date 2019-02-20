<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;


/**
 * This is the model class for table "patient_members".
 *
 * @property int $id
 * @property string $slug
 * @property int $user_id
 * @property string $name
 * @property string $phone
 * @property int $gender Male=>1, Female=>2
 * @property int $created_at
 * @property int $updated_at
 */
class PatientMembers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_OTHER = 3;
    public static function tableName()
    {
        return 'patient_members';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
             'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'name',
                'ensureUnique' => true,
                'immutable' => true,
            ]

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'user_id', 'name', 'phone'], 'required'],
            [['user_id', 'gender'], 'integer'],
            [['slug', 'name', 'phone'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'user_id' => 'User ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'gender' => 'Gender',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return PatientMembersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PatientMembersQuery(get_called_class());
    }

     public function getPatientmemberfiles()
    {
        return $this->hasOne(PatientMemberFiles::className(), ['member_id' => 'id']);
    }
}
