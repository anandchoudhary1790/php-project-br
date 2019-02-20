<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Create user form
 */
class PopularForm extends Model{

    public $speciality;
    public $treatment;
    public $hospital;
    public $value;
    public $key;

    

    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['speciality','treatment','hospital'],'safe'],
            
       ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'speciality' => Yii::t('common', 'Popular Speciality'),
            'treatment' => Yii::t('common', 'Popular Treatment'),
            'hospital' => Yii::t('common', 'Popular Hospital'),
        ];
    }
}