<?php

use common\models\UserEducations;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$upsertUrl="'education-upsert'";
$js="

	$( '#edu_form_btn' ).on( 'submit', function( event ) { 

		$.ajax({
       	method:'POST',
		url: $upsertUrl,
		data: $('#edu_form').serialize(),
    })
      .done(function( msg ) { 

        
        //$('#edu_list_modal').modal({backdrop: 'static',keyboard: false})

      });
 

	  	return false;
	});



       
   



";
$this->registerJs($js,\yii\web\VIEW::POS_END); 

$model->start=($model->start)?$model->start:'';
$model->end=($model->start)?$model->end:'';
$years = array_combine(range(date("Y"), 1910), range(date("Y"), 1910));
?>


<div class="edu-form">
    <?php $form = ActiveForm::begin(['id'=>'edu_form']); ?>

    <?php echo $form->field($model, 'collage_name') ?>

    <?php echo $form->field($model, 'education')->label('Degree/Class'); ?>

    <?= $form->field($model, 'start')->dropDownList($years,['id'=>'year','prompt'=>'Select Start Year','class' => 'selectpicker form-control','placeholder'=> 'Start Year'])->label(false);  ?>  

    <?= $form->field($model, 'end')->dropDownList($years,['id'=>'year','prompt'=>'Select End Year','class' => 'selectpicker form-control','placeholder'=> 'End Year'])->label(false);  ?>

    <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
    <?php ActiveForm::end(); ?>

</div>
