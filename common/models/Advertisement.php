<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "advertisement".
 *
 * @property int $id
 * @property string $title
 * @property string $link
 * @property string $start_date
 * @property string $end_date
 * @property int $show_for_seconds
 * @property string $image_path
 * @property string $image_base_url
 * @property string $status
 * @property int $sequence
 * @property int $created_at
 * @property int $updated_at
 */
class Advertisement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advertisement';
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
            [['title', 'start_date', 'end_date', 'show_for_seconds', 'sequence'], 'required'],
            [['start_date', 'end_date'], 'safe'],
            [['show_for_seconds', 'sequence'], 'integer'],
            [['status'], 'string'],
            [['title'], 'string', 'max' => 512],
            [['link', 'image_path', 'image_base_url'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'link' => 'Link',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'show_for_seconds' => 'Show For Seconds',
            'image_path' => 'Image Path',
            'image_base_url' => 'Image Base Url',
            'status' => 'Status',
            'sequence' => 'Sequence',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
