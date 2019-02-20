<?php
use common\components\DrsPanel;

$this->title = Yii::t('frontend','DrsPanel :: Doctor Appointment');
// echo '<pre>';
// print_r($getshiftaddressdays);die;
$baseUrl= Yii::getAlias('@frontendUrl');
$loginUser=Yii::$app->user->identity;
$slug=$doctorProfile->slug;
$getlist="'".$baseUrl."/search/booking-confirm'";
$js="
$('.get-slot').on('click',function(){
  id=$(this).attr('id');
  $.ajax({
    method:'POST',
    url: $getlist,
    data: {slug:$slug,slot_id:id}
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
// echo '<pre>';
// print_r($getshiftaddressdays);die;
?>

<section class="mid-content-part">
    <div class="signup-part">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9 mx-auto">
                    <div class="youare-text"> You are Booking an appointment with </div>
                    <div class="hospitals-detailspt">
                        <div class="pace-part main-tow">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="pace-left">
                                        <?php $image = DrsPanel::getUserAvator($doctorProfile['user_id']);?>
                                        <img src="<?php echo $image; ?>" alt="image"/>
                                    </div>

                                    <div class="pace-right">
                                        <h4>
                                            <?= $doctorProfile['prefix'].' '.$doctorProfile['name'] ?>
                                        </h4>
                                        <p> <?= $doctorProfile['speciality']; ?> </p>
                                        <p>
                                            <i class="fa fa-calendar"></i>
                                            <span id="doctor-date"> <?php  //echo date('d M Y',$date); ?> </span>
                                            <span class="pull-right">
                                                <img src="<?php echo $baseUrl; ?>/images/dollor-wallet.png" alt="image"/>
                                                <strong>$<?php// echo $appointment['consultation_fees'];?></strong>
                                            </span>
                                        </p>
                                        <p>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <?php // echo date('h:i a',$appointment['start_time']); ?> - <?php // echo date('h:i a',$appointment['end_time']); ?>
                                        </p>
                                        <div class="pull-left">
                                            <p>
                                                <strong><?php //echo DrsPanel::getHospitalName($appointment['address_id'])?></strong>
                                                <small><br><?php //echo DrsPanel::getUserAddress($appointment['address_id']);?></small>
                                                <?php //echo isset($doctor['userAddress']['name'])?$doctor['userAddress']['name']:''?> <?php //echo isset($doctor['userAddress']['city'])?$doctor['userAddress']['city']:''?>
                                            </p>
                                        </div>
                                        <div class="pull-right">
                                            <a href="#" data-toggle="modal" data-target="#myModal">0.8 km<i class="fa fa-location-arrow"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        //echo '<pre>';
                        //print_r($slots);die;
                         ?>
                        <div class="week_sectionmain">
                            <ul>
                                <li class="active">
                                    <div class="weekDays-selector"> 
                                        <input type="checkbox" id="Sun" class="weekday" />
                                        <label for="Sun">S</label>
                                    </div>
                                </li>

                                <li>
                                    <div class="weekDays-selector">
                                        <input type="checkbox" id="Mon" class="weekday" />
                                        <label for="Mon">M</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="weekDays-selector">
                                        <input type="checkbox" id="01" class="weekday" />
                                        <label for="01">T</label>
                                    </div>
                                </li>
                                <li class="active">
                                    <div class="weekDays-selector">
                                        <input type="checkbox" id="02" class="weekday" />
                                        <label for="02">W</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="weekDays-selector">
                                        <input type="checkbox" id="03" class="weekday" />
                                        <label for="03">T</label>
                                    </div>
                                </li>
                                <li class="active">
                                    <div class="weekDays-selector"> 
                                        <input type="checkbox" id="04" class="weekday" />
                                        <label for="04">F</label>
                                    </div>
                                </li>
                                <li class="active">
                                    <div class="weekDays-selector">
                                        <input type="checkbox" id="05" class="weekday" />
                                        <label for="05">S</label>
                                    </div>
                                </li>
                            </ul>
                        </div> 
                        <div class="doc-boxespart-book">
                            <div class="row">
                                <?php //echo "<pre>"; print_r($slots);?>
                                <?php /*if(count($slots)>0){ foreach ($slots as $key => $slot) {
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
                                    }*/

                                    ?>
                                    <div class="col-sm-3 <?php //echo $class_click; ?>" id="slot-<?php //echo $slot['id']; ?>">
                                        <div class="token_allover token_allover_book">
                                            <div class="token <?php // echo $token_class; ?>">
                                                <h4> <?php //echo $slot['token']; ?> </h4>
                                            </div>
                                            <div class="token-rightdoctor">
                                                <div class="token-timingdoc <?php// echo $token_class; ?>">
                                                    <h3> <?php //echo $status; ?> </h3>
                                                    <span class="time-btnpart"> <?php //echo $slot['shift_name']; ?></span> </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php //} } ?>
                            </div>
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