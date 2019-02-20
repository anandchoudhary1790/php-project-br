<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "tempuser".
 *
 * @property int $id
 * @property string $phone
 * @property string $otp
 * @property int $dailcode
 * @property int $created_at
 * @property int $updated_at
 */
class Tempuser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tempuser';
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
            [['phone','groupid'], 'required'],
            [['countrycode'], 'string'],
            [['phone'], 'string', 'max' => 15],
            [['otp'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'otp' => 'Otp',
            'countrycode' => 'Country Code',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
