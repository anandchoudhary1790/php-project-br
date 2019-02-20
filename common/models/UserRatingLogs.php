<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_rating_logs".
 *
 * @property int $id
 * @property int $user_id
 * @property int $doctor_id
 * @property double $rating
 * @property string $review
 * @property int $created_at
 * @property int $updated_at
 */
class UserRatingLogs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_rating_logs';
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
            [['user_id', 'doctor_id','rating'], 'required'],
            [['user_id', 'doctor_id'], 'integer'],
            [['rating'], 'number'],
            [['review'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'doctor_id' => 'Doctor ID',
            'rating' => 'Rating',
            'review' => 'Review',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
