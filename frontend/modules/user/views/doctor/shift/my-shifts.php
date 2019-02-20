<?php

use common\components\DrsPanel;
$this->title = 'Drspanel :: My Shifts';
$base_url= Yii::getAlias('@frontendUrl');

$shiftDelete="'".$base_url."/doctor/delete-shift-with-address'";


$this->registerJs("
$(document).on('click','.call-delete-modal', function () {
    id=$(this).attr('data-id');
    address_id=$(this).attr('data-address_id');
    $('#ConfirmModalHeading').html('Confirm Address Delete');
    $('#ConfirmModalContent').html('<p>Are You sure to delete</p>');
    $('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false,show: true})
    
     .one('click', '#confirm_ok', function(e) {
             $.ajax({
                method:'POST',
                url: $shiftDelete,
                data: { user_id: id,address_id:address_id}
          })
            .done(function( msg ) { 
              $('#ConfirmModalShow').modal('hide');
              $('#SuccessModalHeading').html('Address Delete');
              $('#SuccessModalContent').html(msg);
              $('#success-modal').modal({show: true});


            });
            //ajax close
         }) //confirm alert box close

   
       });

", \yii\web\VIEW::POS_END);
?>
<section class="mid-content-part">

    <div class="container">
        <div class="row">
            <div class="col-md-10 mx-auto">

                <div class="today-appoimentpart">
                    <div class="col-md-12 calendra_slider">
                        <h3> My Shifts </h3>
                        <div class="calender_icon_main location pull-right ">
                            <a class="modal-call" href="<?php echo yii\helpers\Url::to(['doctor/add-shift']); ?>" title="Add Shift">
                                <i class="fa fa-plus-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>


                <?php
                if(!empty($address_list))
                {
                    foreach($address_list as $key=>$list) {
                        echo $this->render('_shift-block',['list' => $list,'doctor_id'=>$doctor_id]);
                    }
                } else {  ?>
                    <div class="col-md-12 text-center">Shifts not available.</div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

