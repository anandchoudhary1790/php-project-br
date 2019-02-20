 <?php
 use common\components\DrsPanel;
 use common\models\User;
use common\models\UserAppointment;
use branchonline\lightbox\Lightbox;


 $baseUrl=Yii::getAlias('@frontendUrl');

 $class="cancel_btn";
 $js="$('.cancel_appointment').on('click',function(){
        appointment_id = $(this).attr('data-id');
        var txt_show = '<p>Are you sure want to Cancel this appointment?</p>';
        $('#ConfirmModalHeading').html('<span>Appointment Delete?</span>');        $('#ConfirmModalContent').html(txt_show);
        $('#ConfirmModalShow').modal({backdrop:'static',keyword:false})
        .one('click', '#confirm_ok' , function(e){
        $.ajax({
            url: '".$baseUrl."/patient/ajax-cancel-appointment',
            dataType:   'html',
            method:     'POST',
            data: {appointment_id: appointment_id},
            success: function(response){
               location.reload();
            }
        });
      });
    });

    ";
$this->registerJs($js,\yii\web\VIEW::POS_END);
 ?>
 <?php
  if(!empty($appointments)) {
     foreach ($appointments as $key => $appointment) {
         $doctor=User::findOne($appointment['doctor_id']);
         ?>
         <div class="pace-part main-tow">
             <div class="row">
                 <div class="col-sm-12">
                     <div class="pace-left">
                         <?php $image = DrsPanel::getUserAvator($appointment['doctor_id'],'thumb');?>
                         <?php 
                            echo Lightbox::widget([
                            'files' => [
                            [
                                'thumb' => $image,
                                'original' => $image,
                                'title' => $doctor['userProfile']['name'],
                            ],
                            ]
                            ]);
                          ?>
                     </div>
                     <div class="pace-right">
                         <h4><?=$doctor['userProfile']['prefix']?> <?=$doctor['userProfile']['name']?></h4>
                         <p> <?= $doctor['userProfile']['speciality']; ?></p>
                         <p>Approx Consultation Time</p>
                         <p><strong><?= $appointment['shift_name']; ?></strong></p>
                        <div class="next_appointment">

                                <?php if($appointment['status'] == UserAppointment::STATUS_CANCELLED){
                                        $text_app= "Appointment Cancelled";
                                        $class="app_cancelled";
                                }
                                elseif($appointment['status'] == UserAppointment::STATUS_COMPLETED){
                                    $text_app= "Appointment Completed";
                                    $class="app_completed";
                                }
                                elseif($appointment['status'] == UserAppointment::STATUS_SKIP){
                                    $text_app= "Appointment Skipped";
                                    $class="app_skipped";
                                }else {
                                    $text_app= 'Appointment '. DrsPanel::getnextDaysCount($appointment['date']);
                                    $class="app_next";} ?>
                            <a href="#" class="<?php echo $class; ?>">
                                <?php echo $text_app; ?>
                            </a>
                        </div>
                     </div>
                    <div class="doc-listboxes">
                        <div class="pull-left">
                          <p> Date and Time </p>
                          <p><strong><?= date('d M Y h:i a',$appointment['start_time']); ?></strong> </p>
                        </div>
                        <div class="pull-right text-right">
                          <p> Patient Name </p>
                          <p><strong><?= $appointment['user_name']; ?></strong> </p>
                        </div>
                        <div class="bookappoiment-btn pull-right">
                            <?php if($appointment['status'] == UserAppointment::STATUS_CANCELLED || $appointment['status'] == UserAppointment::STATUS_COMPLETED){ } else{ ?>
                                <input type="button" value="Cancel" class="bookinput cancel_btn pull-left cancel_appointment" data-id="<?php echo $appointment['id']; ?>">
                            <?php } ?>
                          <input type="button" value="View Booked Details" class="bookinput" onclick="location.href='<?php echo $baseUrl.'/patient/appointment-details/'.$appointment['id']; ?>'">
                            <?php if($appointment['status'] == UserAppointment::STATUS_CANCELLED || $appointment['status'] == UserAppointment::STATUS_COMPLETED){ } else {?>
                                <input data-id="<?php echo $appointment['id']?>" type="button" value="Reminder" class="bookinput pull-right check_reminder">
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
         </div>
    <?php }
 } else{ ?>
    <div class="pace-part main-tow no_apt">
      <div class="row">
        <p>You have no any appointments.</p>
      </div>
    </div>
<?php } ?>