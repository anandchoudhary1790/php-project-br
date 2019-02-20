<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 

$updateAppointmentStatus="'".$baseUrl."/attender/appointment-status-update'";
$updateShift="'".$baseUrl."/attender/current-appointment-shift-update'";
$js="

$('#skip-btn').on('click', function () {
    var res=$('.current-affairs-0').attr('id');
    var token=$('#'+res).attr('data-token');
    var res=res.split('-');
    var current_token=res[1].split('_');
    var current_token=current_token[1];
    $.ajax({
      method:'POST',
      url: $updateAppointmentStatus,
      data: {token_id:current_token,shift:res,token:token,type:'skip'}
    })
    .done(function( json_result ) { 
      if(json_result){
        var obj = jQuery.parseJSON(json_result);

        if(obj.status){
          $('#current-affairs').html('');
          $('#current-affairs').html(obj.data);
        }
      }
    });
}); // next button close close


$('#next-btn').on('click', function () {
   var res=$('.current-affairs-0').attr('id');
   var token=$('#'+res).attr('data-token');
   var res=res.split('-');
   var current_token=res[1].split('_');
   var current_token=current_token[1];
   $.ajax({
     method:'POST',
     url: $updateAppointmentStatus,
     data: {token_id:current_token,shift:res,token:token,type:'next'}
   })
   .done(function( json_result ) { 
     if(json_result){
      var obj = jQuery.parseJSON(json_result);

      if(obj.status){
       $('#current-affairs').html('');
       $('#current-affairs').html(obj.data);
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

                                <li class="<?php echo ($type == 'book')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'book']); ?>">
                                        <?= Yii::t('db','Book Appointment'); ?>
                                    </a>
                                </li>
                                <li class="<?php echo ($type == 'current_shift')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'current_shift']); ?>">
                                        <?= Yii::t('db','Current Appointment Affair'); ?>
                                    </a>
                                </li>
                                <li class="<?php echo ($type == 'current_appointment')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'current_appointment']); ?>">
                                        <?= Yii::t('db','Appointments'); ?>
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div class="doc-timingslot">
                            <ul>
                                <?php echo $this->render('_shifts',['shifts'=>$Shifts,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type]);?>
                            </ul>
                        </div>
                        <?php if(count($Shifts) > 0) { ?>
                            <div class="doc-boxespart-book">
                                <div class="row">
                                    <?php echo $this->render('_token',['tokens'=>$appointments,'type'=>$type]); ?>
                                </div>
                                <?php if($is_started){ ?>
                                    <?php if($is_completed){ ?>
                                        <div class="bookappoiment-btn">
                                            <input value="Shift Completed" class="bookinput" type="button"/>
                                        </div>
                                    <?php } else{ ?>
                                        <div class="bookappoiment-btn">
                                            <input value="Skip" class="bookinput" type="button" id="skip-btn">
                                            <input value="Next" class="bookinput" type="button" id="next-btn">
                                        </div>
                                        <button type="button" class="login-sumbit btn btn-primary" id="">End Shift</button>
                                    <?php } ?>
                                <?php }else { ?>
                                    <div class="bookappoiment-btn">
                                        <input id="start-shift" value="Start Shift" class="bookinput" type="button">                                  </div>
                                <?php } ?>

                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

