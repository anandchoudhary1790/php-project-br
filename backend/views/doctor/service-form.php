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

$upsertUrl="'experience-upsert'";
$js="

	$( '#edu_form_btn' ).on( 'submit', function( event ) { 
        
		$.ajax({
       	method:'POST',
		url: $upsertUrl,
		data: $('#exp_form').serialize(),
    })
      .done(function( msg ) { 

        
        //$('#edu_list_modal').modal({backdrop: 'static',keyboard: false})

      });
 

	  	return false;
	});

";
$this->registerJs($js,\yii\web\VIEW::POS_END); 

$model->start=($model->start)?date('Y-m-d',$model->start):'';
$model->end=($model->start)?date('Y-m-d',$model->end):'';
?>


<div class="edu-form">
    <?php $form = ActiveForm::begin(['id'=>'exp_form']); ?>
        
            <?php echo $form->field($model, 'hospital_name') ?>

            <?= $form->field($model, 'start')->textInput()->widget(
                            DatePicker::className(), [
                            'convertFormat' => true,
                            'options' => ['placeholder' => 'Start From'],
                            'layout'=>'{input}{picker}',
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-MM-dd',
                                'endDate' => date('Y-m-d'),
                                'todayHighlight' => true
                            ],]); ?>


            <?= $form->field($model, 'end')->textInput()->widget(
                            DatePicker::className(), [
                            'convertFormat' => true,
                            'options' => ['placeholder' => 'End '],
                            'layout'=>'{input}{picker}',
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-MM-dd',
                                'endDate' => date('Y-m-d'),
                                'todayHighlight' => true
                            ],]); ?>

            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
    <?php ActiveForm::end(); ?>

</div>
