<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
$doctorslist = array();
// pr($doctor_lists);die;
/*if(!empty($doctor_lists)){
    foreach($doctor_lists as $doctor_list){
    	
        $doctorslist[$doctor_list['user_id']]=$doctor_list['name'];
    }
}*/
$base_url= Yii::getAlias('@frontendUrl'); ?>
<?php if(!empty($hospitalId))
{
	$action_url = '/hospital/attender-update';
} else{
	$action_url = '/doctor/attender-update';
}?>
<?php $form = ActiveForm::begin(['id'=>'attender-update-form','action'=>$base_url.$action_url,'enableAjaxValidation'=>true,]); ?>
<?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
<?= $this->render('_form', [
	'model' => $model,
	'form'=>$form,
	'hospitals'=>$hospitals,
	'doctor_lists'=>$doctor_lists,
	]) ?>
	<?php ActiveForm::end(); ?>