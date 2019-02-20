<?php
$baseUrl= Yii::getAlias('@frontendUrl');

$getlist="'".$baseUrl."/attender/booking-confirm'";
$js="


$('.get-slot').on('click',function(){
  id=$(this).attr('id');
  $.ajax({
    method:'POST',
    url: $getlist,
    data: {slot_id:id}
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
<div class="doc-boxespart-book">
    <div class="row">
        <?php if(count($slots)>0){
            foreach ($slots as $key => $slot) {
                if($slot['status'] == 'booked'){
                    $token_class='emergency';
                    $status='Booked';
                    $class_click= 'get-slot-booked';
                }
                else{
                    if($slot['type'] == 'consultation'){
                        $token_class='avail';
                        $status='Available';
                        $class_click= 'get-slot';
                    }else if($slot['type']=='emergency'){
                        $status='Emergency';
                        $token_class='emergency';
                        $class_click= 'get-slot';
                    }else{
                        $token_class='avail';
                        $status='Available';
                        $class_click= 'get-slot';
                    }
                } ?>
                <div class="col-sm-3 <?php echo $class_click; ?>" id="slot-<?php echo $slot['id']; ?>">
                    <div class="token_allover token_allover_book">
                        <div class="token <?php echo $token_class; ?>">
                            <h4> <?php echo $slot['token']; ?> </h4>
                        </div>
                        <div class="token-rightdoctor">
                            <div class="token-timingdoc <?php echo $token_class; ?>">
                                <h3> <?php echo $status; ?> </h3>
                                <span class="time-btnpart"> <?php echo $slot['shift_name']; ?></span> </div>
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