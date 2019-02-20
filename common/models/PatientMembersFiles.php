<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\PatientMembersFilesQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "patient_members_files".
 *
 * @property int $id
 * @property string $slug
 * @property int $user_id
 * @property int $member_id
 * @property string $file_base_path
 * @property string $file_path
 * @property string $file_type
 * @property string $file
 * @property int $created_at
 * @property int $update_at
 */
class PatientMembersFiles extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient_members_files';
    }

    
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
            [['user_id', 'member_id','file_base_path', 'file_path', 'file_type'], 'required'],
            [['user_id', 'member_id', 'created_at','updated_at'], 'integer'],
            [['file_base_path', 'file_path', 'file_type','file_name'], 'string', 'max' => 255],
            [['file'], 'file', 'extensions' => ['png', 'jpg', 'gif','jpeg'], 'maxFiles' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'user_id' => 'User ID',
            'member_id' => 'Member ID',
            'file_base_path' => 'File Base Path',
            'file_path' => 'File Path',
            'file_type' => 'File Type',
            'file' => 'File',
            'created_at' => 'Created At',
            'updated_at' => 'Update At',
        ];
    }

    /**
     * @inheritdoc
     * @return PatientMembersFilesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PatientMembersFilesQuery(get_called_class());
    }
}
