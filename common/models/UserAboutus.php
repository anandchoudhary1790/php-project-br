<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_aboutus".
 *
 * @property int $id
 * @property int $user_id
 * @property string $description
 * @property string $vision
 * @property string $mission
 * @property string $timing
 * @property int $created_at
 * @property int $updated_at
 */
class UserAboutus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_aboutus';
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
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['description', 'vision', 'mission', 'timing'], 'string'],
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
            'description' => 'Description',
            'vision' => 'Vision',
            'mission' => 'Mission',
            'timing' => 'Timing',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAboutusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAboutusQuery(get_called_class());
    }
}
