<?php

use common\models\UserExperience;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;


/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
$specialities_list=$treatment_list=array();
foreach ($speciality as $special) {
  $specialities_list[$special->value] = $special->label;
}
$specialities_list['Other']='Other';
foreach ($treatments as $treatment) {
  $treatment_list[$treatment->value] = $treatment->label;
}
$treatment_list['Other']='Other';
?>

<div class="edu-form">




    <?php 
    $model->speciality = explode(',',$specialityList[0]['speciality']);
    ?>  
    <?php echo  $form->field($model, 'speciality')->widget(Select2::classname(), 
        [
        'data' => $specialities_list,
        'size' => Select2::SMALL,
        'options' => ['placeholder' => 'Services', 'multiple' => true],
        'pluginOptions' => [
        'allowClear' => true
        ],
        ]); ?>  
        


    <?php 
   
    $model->treatment = explode(',',$treatmentList[0]['treatment']);
    ?>  
    <?php echo  $form->field($model, 'treatment')->widget(Select2::classname(), 
        [
        'data' => $treatment_list,
        'size' => Select2::SMALL,
        'options' => ['placeholder' => 'treatment', 'multiple' => true],
        'pluginOptions' => [
        'allowClear' => true
        ],
        ]); ?>
        <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
    </div>
