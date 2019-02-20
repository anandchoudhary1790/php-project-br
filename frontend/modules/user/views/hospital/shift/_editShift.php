<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$week_array=DrsPanel::getWeekArray();
$frontendUrl=Yii::getAlias('@frontendUrl');
$attenderUrl="'".$frontendUrl."/site/attender-list'";

$this->registerJs("

    $('.addscheduleform-start_time').timepicker({defaultTime: '08:00 A'});
    $('.addscheduleform-end_time').timepicker({defaultTime: '12:00 P'});

    $('#hospital_id').on('change', function () {
        id=".$userShift->user_id.";
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

//
?>

<?php $form = ActiveForm::begin([
    'action'=>["/doctor/my-shifts",'id'=>$userShift->id],
    'id' => 'schedule-form-new',
    'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
    'options' => [
    'enctype' => 'multipart/form-data',
    'class' => 'schedule-form',
    ]
    ]); ?>
    <div class="col-sm-12">
        <?php echo $form->field($userShift,'id')->hiddenInput()->label(false);?>
        <?php echo $form->field($userShift, 'weekday',['options'=>['class'=>
        'col-sm-6']])->dropDownList($week_array,['disabled'=>true,])->label('Shift Day'); ?>
        <?php echo $form->field($userShift,'weekday')->hiddenInput()->label(false);?>
    </div>

    <div class="col-sm-12 section_top">

     <div class="form-group clearfix">
        <?= $form->field($userShift, 'start_time',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Start'),  'readonly'=> false,'class'=>'addscheduleform-start_time form-control'])->label('From'); ?>
        <?= $form->field($userShift, 'end_time',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'addscheduleform-end_time form-control'])->label('To'); ?>

        <?php echo $form->field($userShift, 'patient_limit',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label('Patient Limit'); ?>

        <?php echo $form->field($userShift, 'consultation_fees',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Consultancy Fees'), 'readonly'=>false]); ?>

        <?php echo $form->field($userShift, 'emergency_fees',['options'=>['class'=>
        'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Emergency Fees'), 'readonly'=>false]); ?>

    </div>
</div>
<div class="form-group clearfix">
    <div class="col-md-6 text-right">
       <?= Html::submitButton('Update', ['class' => 'login-sumbit']) ?>
   </div>
   <div class="col-md-6 text-right">
       <?= Html::submitButton('Cancel', ['class' => 'login-sumbit','data-dismiss'=> 'modal']) ?>
   </div>
</div>
<?php ActiveForm::end(); ?>
