<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 

$updateAppointmentStatus="'".$baseUrl."/doctor/appointment-status-update'";
$updateShift="'".$baseUrl."/doctor/current-appointment-shift-update'";
$js="


$('.start_shift').on('click', function () {
    var schedule_id=$(this).attr('data-value');
    doctorid=$(this).attr('data-doctor_id');
    var status='start';
    $.ajax({
      method:'POST',
      url: $updateShift,
      data: {schedule_id:schedule_id,status:status,doctor_id:doctorid,user_id:doctorid}
    })
    .done(function( json_result ) { 
      if(json_result){
        var obj = jQuery.parseJSON(json_result);
        if(obj.status){
            $('#shiftslot_'+schedule_id+' .get-shift-token').trigger('click');
          //$('#current-affairs').html('');
          //$('#current-affairs').html(obj.data);
        }
      }
    });
}); // next button close close

$('#skip-btn').on('click', function () {
    var schedule_id=$(this).attr('data-value');
   doctorid=$(this).attr('data-doctor_id');
   var status='skip';
    $.ajax({
      method:'POST',
      url: $updateAppointmentStatus,
      data: {schedule_id:schedule_id,status:status,doctor_id:doctorid,user_id:doctorid}
    })
    .done(function( json_result ) { 
      if(json_result){
        var obj = jQuery.parseJSON(json_result);

        if(obj.status){
          $('#shiftslot_'+schedule_id+' .get-shift-token').trigger('click');
        }
      }
    });
}); // next button close close


$('#next-btn').on('click', function () {
   var schedule_id=$(this).attr('data-value');
   doctorid=$(this).attr('data-doctor_id');
   var status='next';
   $.ajax({
     method:'POST',
     url: $updateAppointmentStatus,
     data: {schedule_id:schedule_id,status:status,doctor_id:doctorid,user_id:doctorid}
   })
   .done(function( json_result ) { 
     if(json_result){
      var obj = jQuery.parseJSON(json_result);

      if(obj.status){
       $('#shiftslot_'+schedule_id+' .get-shift-token').trigger('click');
     }
   }

 });
}); // next button close close

$('#end-btn').on('click', function () {
   var schedule_id=$(this).attr('data-value');
   doctorid=$(this).attr('data-doctor_id');
   var status='end';
   $.ajax({
     method:'POST',
     url: $updateAppointmentStatus,
     data: {schedule_id:schedule_id,status:status,doctor_id:doctorid,user_id:doctorid}
   })
   .done(function( json_result ) { 
     if(json_result){
      var obj = jQuery.parseJSON(json_result);

      if(obj.status){
       $('#shiftslot_'+schedule_id+' .get-shift-token').trigger('click');
     }
   }

 });
}); // next button close close



";
$this->registerJs($js,\yii\web\VIEW::POS_END);

?>

<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="appointments_section">
                    <div class="today-appoimentpart">
                        <div id="appointment_date_select" class="appointment_date_select mx-auto calendra_slider">

                            <div class="appointment_calendar clearfix">
                                <ul>
                                    <li>
                                        <div class="day_blk">
                                            <div class="day_name"><h3>Today Appointments</h3></div>
                                            <div class="day_date">
                                                <?php  echo date('d M Y',strtotime($date)); ?>
                                            </div>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>

                    <div class="hospitals-detailspt appointment_list">
                        <div class="docnew-tab">
                            <ul class="resp-tabs-list">

                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'book']); ?>'" class="<?php echo ($type == 'book')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'book']); ?>">
                                        <?= Yii::t('db','Book Appointment'); ?>
                                    </a>
                                </li>
                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'current_shift']); ?>'" class="<?php echo ($type == 'current_shift')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'current_shift']); ?>">
                                        <?= Yii::t('db','Current Appointment Affair'); ?>
                                    </a>
                                </li>
                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'current_appointment']); ?>'" class="<?php echo ($type == 'current_appointment')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'current_appointment']); ?>">
                                        <?= Yii::t('db','Appointments'); ?>
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div class="doc-timingslot">
                            <ul>
                                <?php echo $this->render('/common/_shifts',['shifts'=>$Shifts,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type,'userType'=>'doctor']);?>
                            </ul>
                        </div>

                        <div id="shift-current-appointment-load">
                            <div class="doc-boxespart-book" id="shift-current-appointment">
                                <?php echo $this->render('/common/_current_bookings',['bookings'=>$appointments,'type'=>$type,'userType'=>'doctor','is_started'=>$is_started,'is_completed'=>$is_completed,'schedule_id'=>$schedule_id,'doctor'=>$doctor]); ?>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

