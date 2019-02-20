<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  common\components\DrsPanel ;
$baseUrl= Yii::getAlias('@frontendUrl');
$getAppointmentConfirm="'".$baseUrl."/$userType/appointment-payment-confirm'";
$getAppointmentCancel="'".$baseUrl."/$userType/ajax-cancel-appointment'";

$js="

    function PrintDiv() {    
       var divToPrint = document.getElementById('patientbookedShowModal');
       var popupWin = window.open('', '_blank', 'width=300,height=300');
       popupWin.document.open();
       popupWin.document.write('<html><body onload=\"window.print()\">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
            
    $(document).on('click','#appointment_confirm_payment', function () {
        doctorid=$(this).attr('data-doctorid');
        datastatus=$(this).attr('data-status');
        $.ajax({
          method:'POST',
          url: $getAppointmentConfirm,
          data: {user_id:doctorid,appointment_id:$(this).attr('data-appointmentid'),booking_type:datastatus}
        })
       .done(function( json_result ) { 
        if(json_result){
            $('#patientbookedShowModal').on('hidden.bs.modal', function (e) {
              setTimeout(function () {
                swal({            
                type:'success',
                title:'Success!',
                text:'Booking Updated',            
                timer:1000,
                confirmButtonColor:'#a42127'
            })},100);
            });
         
        } 
        });   
    });
    $('.cancel_appointment').on('click',function(){
        appointment_id = $(this).attr('data-id');
        var txt_show = '<p>Are you sure want to Cancel this appointment?</p>';
        $('#ConfirmModalHeading').html('<span>Appointment Delete?</span>');        $('#ConfirmModalContent').html(txt_show);
        $('#ConfirmModalShow').modal({backdrop:'static',keyword:false})
        .one('click', '#confirm_ok' , function(e){
        $.ajax({
            url: $getAppointmentCancel,
            dataType:   'html',
            method:     'POST',
            data: { appointment_id: appointment_id},
            success: function(response){
                location.reload();
            }
        });
    });
    });
    
";
$this->registerJs($js,\yii\web\VIEW::POS_END);

?>

<div class="col-md-12 mx-auto">
    <div class="pace-part main-tow mb-2">
        <div class="row">
            <div class="col-sm-12">
                <div class="reminder-left">
                    <p class="text-reminder">To </p>
                    <h4><?= $booking['doctor_name']?></h4>
                    <p> <?= $booking['doctor_speciality']?></p>
                    <p> <?= $booking['doctor_address']?></p>
                </div>
                <div class="reminder-right text-right"> <img src="<?= $booking['doctor_image']?>" alt="image"></div>
            </div>
        </div>
    </div>
    <form class="appoiment-form-part">
        <div class="btdetialpart">
            <div class="pull-left"><?= $booking['patient_name']?></div>
            <div class="pull-right"> <?= $booking['patient_mobile']?> </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Date</p>
                <p><strong><?= date('d M, Y', strtotime($booking['appointment_date']));?></strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Appointment Time</p>
                <p><strong><?= $booking['appointment_time']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Token Number</p>
                <p><strong><?= $booking['token']?></strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Approx consultation Time</p>
                <p><strong><?= $booking['appointment_approx_time']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Fees</p>
                <p><strong><i class="fa fa-rupee" aria-hidden="true"></i> <?= $booking['fees']?>/Session</strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Booking id</p>
                <p><strong><?= $booking['booking_id']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
        <div class="row booking_detail_btn">
        <?php
            if($booking['status']=='available') { 
             ?>
            <div class="pull-left col-sm-4">
                <input type="button" onClick="refreshPage()"  class="confirm-theme" value="OK" data-dismiss="modal">
            </div>
            <?php }  elseif($booking['status']='completed') { ?>
            <div class="pull-left col-sm-6">
                <input type="button" class="confirm-theme" value="OK" data-dismiss="modal">
            </div>

            <?php } ?>
            <?php if($booking_type =='offline') 
            {  
                if($booking['status']='completed') { ?>
                    <div class="text-center col-sm-4">
                        <input type="button" class="confirm-theme cancel_appointment" data-id="<?php echo $booking['id']?>" value="CANCEL">
                    </div> <?php 
                } 
                elseif($booking['status']='cancelled') { ?>
                    <div id="appointment_confirm_div col-sm-4">
                    <input type="button" class="confirm-theme" value="CANCEL">
                    </div>
                        <?php 
                }
                } ?>
                <?php if($booking_type =='offline') 
                { ?>
                    <div class="pull-right text-right col-sm-4">
                        <?php if($booking['status']=='pending') {?>
                        <input type="button" class="confirm-theme" data-status="<?php echo $booking['status']?>" data-doctorid="<?php echo $booking['doctor_id']?>" data-appointmentid ="<?php echo $booking['id']?>" id="appointment_confirm_payment" value="PAID">
                        <?php }
                        elseif($booking['status']='completed') { ?>
                        <div id="appointment_confirm_div">
                            <input type="button" class="confirm-theme" value="CONFIRMED">
                        </div>
                        <?php 
                        } ?>
                    </div>
                <?php }
                else { ?>
                    <div class="pull-right text-right col-sm-8">
                    <input type="button" class="confirm-theme" value="PRINT" onclick="PrintDiv();">
                <?php } ?>
            </div>
            </div>
        </div>
    </form>
</div>
