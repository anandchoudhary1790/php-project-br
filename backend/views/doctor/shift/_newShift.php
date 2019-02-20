<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$backend=Yii::getAlias('@backendUrl');
$attenderUrl="'".$backend."/site/attender-list'";

$this->registerJs("
$('#hospital_id').on('change', function () {
    id=".$model->user_id.";
    address_id=$(this).val();
    $.ajax({
          method: 'POST',
          url: $attenderUrl,
          data: { doctor_id: id,address_id:address_id}
    })
      .done(function( msg ) { 
        if(msg){
        $('#attender_id').html('');
        $('#attender_id').html(msg);
        }
      });

   
       });

", \yii\web\VIEW::POS_END); 

?>


    
    <?= $form->field($model, 'start_time',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Start'), 'onchange' => 'shiftOneValue("schedule-form-new","start_time");', 'readonly'=> false,'class'=>'shift-time-check addscheduleform-shift_one_start form-control'])->label('From'); ?>
    <?= $form->field($model, 'end_time',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','To'), 'onchange' => 'shiftOneValue("schedule-form-new","end_time");', 'readonly'=>false,'class'=>'shift-time-check addscheduleform-shift_one_end form-control'])->label('To'); ?> 
    <?php echo $form->field($model, 'address_id',['options'=>['class'=>
        'col-sm-6']])->dropDownList($listaddress,['id'=>'hospital_id','prompt' => 'Select Hospital/Clininc'])->label('Hospital/Clininc'); ?>

     <?php /* echo $form->field($model, 'attender_id',['options'=>['class'=>
        'col-sm-6']])->dropDownList([],['id'=>'attender_id','prompt' => 'Select Attender'])->label('Select Attender'); */ ?>

    <?php echo $form->field($model, 'patient_limit',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label('Patient Limit'); ?>
  <?php /*   <?php echo $form->field($model, 'appointment_time_duration',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Duration'), 'readonly'=>false])->label('Duration'); ?>   */ ?> 
    <?php echo $form->field($model, 'consultation_fees',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Consultancy Fees'), 'readonly'=>false]); ?>
    <?php /* echo $form->field($model, 'consultation_days',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); */ ?>
    <?php echo $form->field($model, 'emergency_fees',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Emergency Fees'), 'readonly'=>false]); ?>
    <?php /* echo $form->field($model, 'emergency_days',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); */ ?>

    <div id="addFormItem"></div>