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
$services_list=array();
foreach ($services as $service) {
  $services_list[$service['value']] = $service['label'];
}
$services_list['Other']='Other';
?>
<div class="edu-form">
    <?php 
    if(!empty($servicesList)){
       $model->services = explode(',',$servicesList[0]['services']);
    }
    ?>  
    <?php echo  $form->field($model, 'services')->widget(Select2::classname(), 
        [
        'data' => $services_list,
        'size' => Select2::SMALL,
        'options' => ['placeholder' => 'Services', 'multiple' => true],
        'pluginOptions' => [
        'tags' => true,
        'tokenSeparators' => [',', ' '],
        'maximumInputLength' => 10,
        'allowClear' => true
        ],
        ])->label(false); ?>
        <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
    </div>
