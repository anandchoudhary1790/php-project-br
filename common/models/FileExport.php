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
class FileExport extends \yii\db\ActiveRecord
{
    
    public $type;
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
            [['type',], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Select type',
        ];
    }

    
}
