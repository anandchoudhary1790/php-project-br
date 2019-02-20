<?php


$baseUrl= Yii::getAlias('@frontendUrl');

$getAppointment="'".$baseUrl."/$userType/get-appointment-detail'";
$js="
    $(document).on('click','.get-booking-detail', function () {
        doctorid=$(this).attr('data-doctorid');
        $.ajax({
          method:'POST',
          url: $getAppointment,
          data: {user_id:doctorid,appointment_id:$(this).attr('data-appointment-id')}
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

<div class="tablist-diserp">
    <ul class="resp-tabs-list">

        <li type="online" class="get-appointments onlineselect <?php echo ($typeselected == 'online')?'resp-tab-active':'resp-tab-inactive'; ?>" data-type="online" data-doctorid="<?php echo isset($doctor->id)?$doctor->id:''; ?>">
            <a href="#" class="" data-type="online" data-doctorid="<?php echo isset($doctor->id)?$doctor->id:''; ?>">
                <?= Yii::t('db','Online Appointment'); ?> <span id="online-count" ><?php echo ($typeCount)?'('.$typeCount['online'].')':'' ?></span>
            </a>
        </li>
        <li type="offline" class="get-appointments offlineselect <?php echo ($typeselected == 'offline')?'resp-tab-active':'resp-tab-inactive'; ?>" data-type="offline" data-doctorid="<?php echo isset($doctor->id)?$doctor->id:''; ?>">
            <a href="javascript:void(0)" class="" data-type="offline" data-doctorid="<?php echo isset($doctor->id)?$doctor->id:''; ?>">
                <?= Yii::t('db','Offline Appointment'); ?> <span id="offline-count" ><?php echo ($typeCount)?'('.$typeCount['offline'].')':'' ?></span>
            </a>
        </li>

    </ul>
</div>

<div class="doc-boxespart-book">
    <div class="row">
        <?php if(!empty($appointments)) {  ?>
            <?php  foreach ($appointments as $key => $appointment) {
                $offline_class='';
                if($appointment['booking_type']=='offline'){
                    $offline_class="green_bg";
                }
                ?>
                <div class="col-sm-12">
                    <div class="token_allover get-booking-detail" data-appointment-id="<?php echo $appointment['id']; ?>" data-type="<?php echo $userType; ?>" data-doctorid="<?php echo $appointment['doctor_id']; ?>">
                        <div class="token1 avail <?php echo $offline_class;?>">
                            <label class="token_text">Token</label>
                            <h4><?php echo $appointment['token']; ?> </h4>
                        </div>
                        <div class="token-rightdoctor1">
                            <div class="tockenimg-right">
                                <?php if($appointment['patient_image']){ ?>
                                    <img src="<?php echo $appointment['patient_image']; ?>" alt="image"> <?php } ?> </div>
                        <div class="token-timingdoc1 avail <?php echo $offline_class;?>">
                        <h3> <?php echo $appointment['name'];?>  </h3>
                        <span class="number-partdoc"> <?php echo $appointment['phone'];?> </span>
                        <p><strong>Booking ID:</strong> <?php echo $appointment['booking_id'];?> </p>
                        <p><strong>Status: </strong><?php echo $appointment['status']; ?></p>
                        </div>
                        </div>
                    </div>
                </div>
            <?php }  }else{ ?>
            <div class="col-sm-12">
                <p>You have no appointment.</p>
            </div>
        <?php } ?>
    </div>
</div>