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
$status=array('1'=>'Requested','2'=>'Confirmed');


?>

<div class="edu-form">


         <?php echo $form->field($model, 'attribute')
        ->dropDownList(
            $status,           // Flat array ('id'=>'label')
            ['prompt'=>'']    // options
        );?>


        <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
    </div>
