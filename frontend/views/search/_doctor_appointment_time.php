<?php
use common\components\DrsPanel;

$this->title = Yii::t('frontend','DrsPanel :: Doctor Appointment');

$baseUrl= Yii::getAlias('@frontendUrl');
$loginUser=Yii::$app->user->identity;
$slug="'$doctorProfile->slug'";
$getlist="'".$baseUrl."/search/booking-confirm'";
$gettokenDetails="'".$baseUrl."/search/get-date-tokens'";
$js="

$(document).on('click','.fetch_date_schedule',function(){
    date = $(this).attr('data-next-date');
    doctor_id = $(this).attr('data-doctor_id');
    schedule_id = $(this).attr('data-schedule_id');
    $.ajax({
        method:'POST',
        url: $gettokenDetails,
        data: {doctor_id:doctor_id,nextdate:'$date',schedule_id:schedule_id}
    })
  .done(function( msg ) { 
    if(msg){
      $('#ajaxLoadDetailDiv').html('');
      $('#ajaxLoadDetailDiv').html(msg); 
    }
  });
});
$('.get-slot').on('click',function(){
  id=$(this).attr('id');
  $.ajax({
    method:'POST',
    url: $getlist,
    data: {slug:$slug,slot_id:id,date:'$date'}
  })
  .done(function( msg ) { 
    if(msg){
      $('#pslotTokenContent').html('');
      $('#pslotTokenContent').html(msg); 
      $('#patientbookedShowModal').modal({backdrop: 'static',keyboard: false});
    }

  });

});

";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>

<section class="mid-content-part">
    <div class="signup-part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 mx-auto">
                    <div class="refresh_notice">Note: Please do not reload or refresh the page.</div>
                    <div class="youare-text"> You are Booking an appointment with </div>
                    <div class="hospitals-detailspt">

                        <div id="ajaxLoadDetailDiv">
                            <?php
                            echo $this->render('_booking_detail_list',['date'=>$date,'doctor'=>$doctor,'scheduleDay'=>$scheduleDay,'schedule'=>$schedule,'slots'=>$slots,'doctorProfile'=>$doctorProfile]);
                            ?>
                        </div>

                    </div>
                </div>
                <div class="col-md-2 mx-auto">
                    <div class="Ads_part">
                        <div class="ads_box">
                            <img src="<?php echo $baseUrl?>/images/ads_img1.jpg">
                        </div>
                        <div class="ads_box">
                            <img src="<?php echo $baseUrl?>/images/ads_img1.jpg">
                        </div>
                        <div class="ads_box">
                            <img src="<?php echo $baseUrl?>/images/ads_img1.jpg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<div class="signup-part">
    <div class="modal fade model_opacity" id="patientbookedShowModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 >Confirm <span>Booking</span></h3>
                </div>
                <div class="modal-body" id="pslotTokenContent">

                </div>
                <div class="modal-footer ">

                </div>
            </div>
        </div>
    </div>
</div>