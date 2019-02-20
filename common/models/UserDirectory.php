<?php

namespace common\models;

use Yii;
use common\models\query\UserDirectoryQuery;

/**
 * This is the model class for table "user_directory".
 *
 * @property int $id
 * @property int $groupid
 * @property string $name
 * @property string $email
 * @property int $phone
 * @property int $gender Male=>1, Female=>2
 * @property int $created_at
 * @property int $updated_at
 */
class UserDirectory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_directory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupid', 'name', 'email', 'phone' ], 'required'],
            [['groupid', 'phone', 'gender', 'created_at', 'updated_at','status'], 'integer'],
            [['name', 'email'], 'string', 'max' => 255],
            [['status',], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupid' => 'Groupid',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'gender' => 'Gender',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserDirectoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserDirectoryQuery(get_called_class());
    }
}
