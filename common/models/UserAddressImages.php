<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_address_images".
 *
 * @property int $id
 * @property int $address_id
 * @property string $image_base_url
 * @property string $image_path
 * @property string $image
 * @property int $created_at
 * @property int $updated_at
 */
class UserAddressImages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_address_images';
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
            [['address_id'], 'required'],
            [['address_id'], 'integer'],
            [['image_base_url', 'image_path', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_id' => 'Address ID',
            'image_base_url' => 'Image Base Url',
            'image_path' => 'Image Path',
            'image' => 'Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return UserAddressImagesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAddressImagesQuery(get_called_class());
    }
}
