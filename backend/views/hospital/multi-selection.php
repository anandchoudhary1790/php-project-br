<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;
$speciality_list=array(); $services_list=array(); $treatment_list=array();
foreach ($specialities as $speciality) {
	$speciality_list[$speciality->value] = $speciality->label;
}
foreach ($services as $service) {
	$services_list[$service->value] = $service->label;
}
$services_list['Other']='Other';

foreach ($treatments as $treatment) {
	$treatment_list[$treatment->value] = $treatment->label;
}
$treatment_list['Other']='Other';


$form = ActiveForm::begin(['id' => 'profile-form']); ?>

<?php /*
<div  class="col-sm-12">
<div class="seprator_box">
<h4>Speciality:</h4>
<?php $userProfile->speciality = explode(',',$userProfile->speciality); ?>

<?php echo  $form->field($userProfile, 'speciality')->widget(Select2::classname(), 
[
'data' => $speciality_list,
'size' => Select2::SMALL,
'options' => ['placeholder' => 'Select an speciality ...', 'multiple' => true],
'pluginOptions' => [
'allowClear' => true
],
])->label(false); ?>
</div>
</div>

<div  class="col-sm-12">
<div class="seprator_box">
<h4>Treatments:</h4>
<?php $userProfile->treatment = explode(',',$userProfile->treatment); ?>
<?php echo  $form->field($userProfile, 'treatment')->widget(Select2::classname(), 
[
'data' => $treatment_list,
'size' => Select2::SMALL,
'options' => ['placeholder' => 'Select an treatment ...', 'multiple' => true],
'pluginOptions' => [
'allowClear' => true
],
])->label(false); ?>
</div>
</div> <?php */ ?>

			<div  class="col-sm-12">
				<div class="seprator_box">
					<h4>Services:</h4>
					<?php $userProfile->services = explode(',',$userProfile->services); ?>
					<?php echo  $form->field($userProfile, 'services')->widget(Select2::classname(), 
						[
						'data' => $services_list,
						'size' => Select2::SMALL,
						'options' => ['placeholder' => 'Select an services_ ...', 'multiple' => true],
						'pluginOptions' => [
						'allowClear' => true
						],
						])->label(false); ?>
					</div>
				</div>
				<div class="form-group clearfix col-sm-12">
					<?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
				</div>
				<?php ActiveForm::end(); ?>