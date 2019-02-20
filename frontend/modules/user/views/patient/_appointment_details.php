<?php 
$baseUrl=Yii::getAlias('@frontendUrl');
use common\components\DrsPanel;
use common\models\User;
use common\models\UserAppointment;
use branchonline\lightbox\Lightbox;

$appointment_cancel_url="'".$baseUrl."/patient/ajax-cancel-appointment'";

$js="
    $('.cancel_appointment').on('click',function(){
        appointment_id = $(this).attr('data-id');
        var txt_show = '<p>Are you sure want to Cancel this appointment?</p>';
        $('#ConfirmModalHeading').html('<span>Appointment Delete?</span>');        $('#ConfirmModalContent').html(txt_show);
        $('#ConfirmModalShow').modal({backdrop:'static',keyword:false})
        .one('click', '#confirm_ok' , function(e){
        $.ajax({
            url: $appointment_cancel_url,
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
// pr($appointments);die;
 //echo "<pre>";print_r($appointment_doctorData);die
// pr($appointment_hospitalData);die;
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-7 mx-auto">
          <div class="appointment_part">
            <div class="pace-part main-tow">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="pace-left mb-3"> 
                       <?php $image = DrsPanel::getUserAvator($appointments['doctor_id']);?>

                       <?php 
                            echo Lightbox::widget([
                            'files' => [
                            [
                                'thumb' => $image,
                                'original' => $image,
                                'title' => $appointments['doctor_name'],
                            ],
                            ]
                            ]);
                          ?>
                    </div>
                    <div class="pace-right">
                      <h4><?php echo isset($appointments['doctor_name'])?$appointments['doctor_name']:''?></h4>
                      <p> <?php echo isset($appointment_doctorData['speciality'])?$appointment_doctorData['speciality']:''?> </p>
                      <a href="#" class="call_btn"><img src="<?php echo $baseUrl?>/images/call_icon.png"></a> </div>
                    <div class="doc-listboxes">
                      <div class="pull-left">
                        <p> Date and Time </p>

                        <p><strong> <?php if(isset($appointments['date'])) { echo date('d M Y' , strtotime($appointments['date'])); }?>,  <?php echo isset($appointments['shift_name'])?$appointments['shift_name']:'' ?> </strong> </p>
                         <p><?php echo
                         DrsPanel::getnextDaysCount($appointments['date']); ?></p>
                      </div>
                    </div>
                    <div class="doc-listboxes">
                      <div class="pull-left">
                        <p> Patient Details </p>
                        <?php //  echo '<pre>'; print_r($appointment_doctorData['slug']) ?>
                        <p><strong> <?php echo isset($appointment_hospitalData['name'])?$appointment_hospitalData['name']:'' ?></strong> </p>
                        <p><?php echo isset($appointment_hospitalData['address'])?$appointment_hospitalData['address']:''.isset($appointment_hospitalData['area'])?$appointment_hospitalData['area']:''.isset($appointment_hospitalData['city'])?$appointment_hospitalData['city']:''.isset($appointment_hospitalData['state'])?$appointment_hospitalData['state']:''.isset($appointment_hospitalData['country'])?$appointment_hospitalData['country']:''.isset($appointment_hospitalData['zipcode'])?$appointment_hospitalData['zipcode']:''; ?></p>
                      </div>
                      <div class="pull-right text-right">
                        <p class="locat_text"><a href="#" data-toggle="modal" data-target="#myModal"> <strong>Direction ~ 8.5 KM</strong> <i class="locat_icon" aria-hidden="true"><img src="<?php echo $baseUrl ?>/images/location_icon.png"></i></a></p>
                      </div>
                    </div>
                    <div class="doc-listboxes">
                      <div class="pull-left">
                        <p> Booking for </p>
                        <p><strong> <?php echo isset($appointments['user_name'])?$appointments['user_name']:'' ?></strong> </p>
                      </div>
                      <div class="pull-right text-right">
                        <p> Contact Number </p>
                        <p><strong><?php echo isset($appointments['user_phone'])?$appointments['user_phone']:''?></strong> </p>
                      </div>
                    </div>
                    <div class="doc-listboxes">
                      <div class="pull-left">
                        <p> Consultation charge </p>
                        <p class="price_text"><strong> <i class="fa fa-rupee" aria-hidden="true"></i> <?php echo isset($appointments['doctor_fees'])?$appointments['doctor_fees']:''; ?> </strong> </p>
                      </div>
                    </div>
                    <div class="doc-listboxes token_appointment clearfix">
                      <div class="pull-left token-left">
                        <p> Token Counter </p>
                      </div>
                      <div class="pull-right token-right">
                        <p class="price_text"><strong> <?php echo isset($appointments['token'])?$appointments['token']:'' ?> </strong> </p>
                      </div>
                    </div>
                    <div class="doc-listboxes">
                      <div class="pull-left token-left">
                        <p> Booking ID </p>
                        <p><strong> <?php echo isset($appointments['booking_id'])?$appointments['booking_id']:''?> </strong> </p>
                      </div>
                      <div class="pull-right text-right token-right"> <a href="#" class="check_icon"><img src="<?php echo $baseUrl?>/images/check_icon.png"></a> </div>
                    </div>
                    <div class="row appointment-bookbtn">
                      <div class="col-lg-4 col-sm-12">
                          <?php if($appointments['status'] == UserAppointment::STATUS_CANCELLED || $appointments['status'] == UserAppointment::STATUS_COMPLETED){ } else{
                            // echo $appointment_doctorData['slug'];die;
                            $slug = isset($appointment_doctorData['slug'])?$appointment_doctorData['slug']:'';
                            $doctor_link = $baseUrl.'/doctor/'.$slug;
                           ?>
                            <div class="bookappoiment-btn">
                              <input type="button" value="Rebook" class="bookinput green-btn" onclick="location.href='<?php echo $doctor_link?>'">
                            </div>
                          <?php } ?>
                      </div>
                      <?php if($appointments['status'] == UserAppointment::STATUS_CANCELLED || $appointments['status'] == UserAppointment::STATUS_COMPLETED){ ?>
                      <div class="col-sm-12">
                        <div class="bookappoiment-btn">
                          <input type="button" value="Share with Other" class="bookinput orange-btn">
                        </div>
                      </div>
                      <?php } else { ?>
                         <div class="col-lg-4 col-sm-12">
                        <div class="bookappoiment-btn">
                          <input type="button" value="Share with Other" class="bookinput orange-btn">
                        </div>
                      </div>
                      <?php } ?>
                      <div class="col-lg-4 col-sm-12">
                        <?php if($appointments['status'] == UserAppointment::STATUS_CANCELLED || $appointments['status'] == UserAppointment::STATUS_COMPLETED){ } else{ ?>
                          <div class="bookappoiment-btn">
                            <input type="button" value="Cancel" class="bookinput cancel_appointment" data-id="<?php echo $appointments['id']; ?>">
                          </div>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          </div>
        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
      </div>
    </div>
  </div>
</section>