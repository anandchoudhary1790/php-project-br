<?php
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');

$getAppointment="'".$baseUrl."/$userType/get-appointment-detail'";
$js="
    $(document).on('click','.get-booking-detail', function () {
        doctorid=$(this).attr('data-doctorid');
        datastatus=$(this).attr('data-status');
        $.ajax({
          method:'POST',
          url: $getAppointment,
          data: {user_id:doctorid,appointment_id:$(this).attr('data-appointment-id'),booking_type:datastatus}
        })
       .done(function( json_result ) { 
        if(json_result){
          $('#pslotTokenContent').html('');
          $('#pslotTokenContent').html(json_result); 
          $('#patientbookedShowModal').modal({backdrop: 'static',keyboard: false});        
        } 
        });   
    });
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>
<div class="doc-boxespart-book">
    <div class="row">
        <?php if(count($bookings)>0){
            foreach ($bookings as $key => $booking) { ?>
                <?php
                if($booking['booking_type'] == 'offline'){
                    $token_class='avail';
                } else{
                    $token_class='';
                }
                ?>
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="token_allover get-booking-detail" data-appointment-id="<?php echo $booking['id']; ?>" data-type="<?php echo $userType; ?>" data-doctorid="<?php echo $doctor_id; ?>" data-status="<?php echo $booking['booking_type']?>">
                        <div class="token <?php echo $token_class; ?>">
                            <h4> <?php echo $booking['token']; ?> </h4>
                        </div>
                        <div class="token-rightdoctor">
                            <div class="tockenimg-right">
                                <?php $imageUrl=DrsPanel::getUserAvator($booking['user_id']); ?>
                                <img src="<?php echo $imageUrl; ?>" alt="image">
                            </div>
                            <div class="token-timingdoc">
                                <h3> <?php echo $booking['name']; ?> </h3>
                                <span class="number-partdoc"> <?php echo $booking['phone']; ?> </span>
                                <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?> </p>
                                <p><strong>Status :</strong> <?php echo $booking['status']; ?> </p>
                                
                            </div>
                        </div>
                    </div>
                </div>


            <?php }
        } ?>
    </div>
</div>


<div class="login-section ">
    <div class="modal fade model_opacity" id="patientbookedShowModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 >Booking <span>Detail</span></h3>
                </div>
                <div class="modal-body" id="pslotTokenContent">

                </div>
                <div class="modal-footer ">

                </div>
            </div>
        </div>
    </div>
</div>