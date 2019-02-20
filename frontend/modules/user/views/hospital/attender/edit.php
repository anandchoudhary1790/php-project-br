<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
$base_url= Yii::getAlias('@frontendUrl');
	// pr($model);die;
 ?>
<?php if(!empty($hospitalId))
{
	$action_url = '/hospital/attender-update';
} else{
	$action_url = '/doctor/attender-update';
}?>
<?php $form = ActiveForm::begin(['id'=>'attender-update-form','action'=>$base_url.$action_url,'enableAjaxValidation'=>true,'options' => ['enctype'=> 'multipart/form-data']]); ?>
<?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
<?= $this->render('_form', [
	'model' => $model,
	'form'=>$form,
	'hospitals'=>$hospitals,
	'doctors'=>$doctor_lists,
	]) ?>
	<?php ActiveForm::end(); ?>