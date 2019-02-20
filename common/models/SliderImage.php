<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\SliderImageQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "slider_images".
 *
 * @property int $id
 * @property string $title
 * @property int $sub_title
 * @property string $pages
 * @property string $image
 * @property string $description
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class SliderImage extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'slider_images';
    }

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
            [['title','status'], 'required'],
            [['app_image'], 'file','extensions' => 'jpg, png'],
            [['image'], 'file','extensions' => 'jpg, png'],
            [['id', 'status'], 'integer'],
            [['description'], 'string'],
            ['image','required','on'=>'create'],
            ['app_image','required','on'=>'create'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['title','sub_title'], 'string', 'max' => 200],
            [['base_path','file_path'], 'string', 'max' => 255],
            [['pages'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'sub_title' => 'Sub Title',
            'pages' => 'Pages',
            'image' => 'Image',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * {@inheritdoc}
     * @return SliderImageQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SliderImageQuery(get_called_class());
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['title','status','image','app_image'];//Scenario Values Only Accepted
    return $scenarios;
    }
}
