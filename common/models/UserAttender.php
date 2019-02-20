<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\query\UserAttenderQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "user_attender".
 *
 * @property int $id
 * @property int $hospital_id
 * @property int $doctor_id
 * @property string $email
 * @property int $mobile
 * @property string $password
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class UserAttender extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_attender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hospital_id', 'email', 'mobile', 'password', 'created_at', 'updated_at'], 'required'],
            [['id', 'hospital_id', 'doctor_id', 'mobile', 'status', 'created_at', 'updated_at'], 'integer'],
            [['email', 'password'], 'string', 'max' => 255],
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
            'doctor_id' => 'Doctor ID',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAttenderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAttenderQuery(get_called_class());
    }
}
