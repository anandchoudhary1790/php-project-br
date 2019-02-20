<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\query\AreasQuery;

/**
 * This is the model class for table "areas".
 *
 * @property int $id
 * @property int $state_id
 * @property int $city_id
 * @property string $code
 * @property string $name
 * @property string $status
 * @property string $lat
 * @property string $lng
 * @property int $created_at
 * @property int $updated_at
 */
class Areas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'areas';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['state_id', 'city_id', 'lat', 'lng'], 'required'],
            [['state_id', 'city_id'], 'integer'],
            [['status'], 'string'],
            [['code', 'name'], 'string', 'max' => 255],
            [['lat', 'lng'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'state_id' => 'State ID',
            'city_id' => 'City ID',
            'code' => 'Code',
            'name' => 'Name',
            'status' => 'Status',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return AreasQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AreasQuery(get_called_class());
    }
}
