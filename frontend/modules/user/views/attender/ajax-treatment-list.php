<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;

if($userProfile->treatment)
	$userProfile->treatment = explode(',',$userProfile->treatment); 
//else
	//$userProfile->treatment='';

echo  $form->field($userProfile, 'treatment')->widget(Select2::classname(), 
	[
	'data' => $treatment_list,
	'size' => Select2::SMALL,
	'options' => ['placeholder' => 'Select an treatment ...', 'multiple' => true],
	'pluginOptions' => [
	'tags' => true,
	'tokenSeparators' => [',', ' '],
	'maximumInputLength' => 10,
	'allowClear' => true
	],
	])->label(false); ?>