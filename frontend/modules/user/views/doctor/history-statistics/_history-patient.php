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

<?php if(!empty($appointments)) {  ?>
    <?php  foreach ($appointments as $key => $appointment) { 
      $online_class='';
      if($appointment['booking_type']=='offline'){
        $online_class="avail";
      }
      ?>

      <div class="col-md-4 col-sm-6">
        <span class="token_allover get-booking-detail" data-appointment-id="<?php echo $appointment['id']; ?>" data-type="<?php echo $userType; ?>" data-doctorid="<?php echo $doctor_id; ?>">
          <span class="token <?php echo $online_class; ?>">
            <h4> <?php echo $appointment['token']; ?> </h4>
          </span>
          <span class="token-rightdoctor">
            <span class="tockenimg-right"> <?php if($appointment['patient_image']){ ?>
              <img src="<?php echo $appointment['patient_image']; ?>" alt="image"> <?php } ?> </span>
              <div class="token-timingdoc">
              <h3> <?php echo $appointment['name'];?>  </h3>
                <span class="number-partdoc"> <?php echo $appointment['phone'];?> </span>
                <p><strong>Booking ID:</strong> <?php echo $appointment['booking_id'];?> </p>
                 <p><strong>Status:</strong> <?php echo $appointment['status'];?> </p>
              </div>
            </span>
          </span>
        </div>
<?php }  }else{ ?>
   <div class="col-sm-12">
   <p>You have no any appointment for selected date.</p>
   </div>
  <?php } ?>


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
      

