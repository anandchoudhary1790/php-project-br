<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property int $state_id
 * @property int $code
 * @property string $name
 * @property string $status
 * @property int $created_at
 * @property int $updated_at
 */
class FileImport extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * @inheritdoc
     */
    public $table;
    function __construct($table=NULL)
    {
        $this->table=$table;
    }  
    public static function tableName()
    {
        return $this->table;
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
            [['file',], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions'=>['xls', 'csv'], 'checkExtensionByMimeType'=>false, 'maxSize'=>1024 * 1024 * 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Please Select type',
        ];
    }

}
