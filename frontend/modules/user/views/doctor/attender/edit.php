<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$base_url= Yii::getAlias('@frontendUrl'); ?>
<?php 
	$action_url = '/doctor/attender-update';
?>
<?php $form = ActiveForm::begin(['id'=>'attender-update-form','action'=>$base_url.$action_url,'enableAjaxValidation'=>true,'options' => ['enctype'=> 'multipart/form-data']]); ?>
<?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
<?= $this->render('_form', [
	'model' => $model,
	'form'=>$form,
	'hospitals'=>$hospitals,
	'shifts'=>$shifts,
	]) ?>
	<?php ActiveForm::end(); ?>