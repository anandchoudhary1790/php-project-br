<?php $baseUrl= Yii::getAlias('@frontendUrl');
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;


$doctorProfile=$profile;

$this->title = Yii::t('frontend','DrsPanel :: '.$doctorProfile->prefix.$doctorProfile->name);
$this->registerJsFile($baseUrl.'/js/popper.min.js', ['depends' => [yii\web\JqueryAsset::className()]]); 
$loginUser=Yii::$app->user->identity;
$doctor_id=$doctorProfile->user_id;
$slug="'".$doctorProfile->slug."'";
$get_shifts="'".$baseUrl."/doctor-shifts'";
$addresList="'".$baseUrl."/search/doctor-address-list'";
$shareProfile="'".$baseUrl."/search/share-profile'";
$js="
$('.get_shift').on('click', function () {
	date=$(this).attr('data-date');
	$.ajax({
		method:'POST',
		url: $get_shifts,
		data: {date:date,doctor_id:$doctor_id}
	})
	.done(function( responce_data ) { 
		$('#slot_update').html('');
		$('#slot_update').html(responce_data);
	})// ajax close
}); //close get shift


$('#doctor-addresss-list').on('click',function(){
	$.ajax({
		method:'POST',
		url: $addresList,
		data: {slug:$slug}
	})
	.done(function( responce_data ) { 
		$('#address-list-modal-content').html('');
		$('#address-list-modal-content').html(responce_data);
		$('#address-list-modal').modal({backdrop: 'static',keyboard: false,show: true})
	})// ajax close		

}); //close addresss List

$('.social_click').on('click',function(){
    type=$(this).attr('data-type');
    doctor_id=$(this).attr('data-doctor_id');
	$.ajax({
		method:'POST',
		url: $shareProfile,
		data: {doctor_id:doctor_id,type:type}
	})
	.done(function( res ) { 
		 var data = jQuery.parseJSON(res);
		 if (data.status == 'success') {
            if(type == 'facebook'){
                var hrefval=data.baseurl+'?u='+data.url;
            }else{
                var hrefval=data.baseurl+'?url='+data.url;
            }
            $('.ssbp-btn').attr('href',hrefval);
            $('.ssbp-btn').click();
                                  
        }
        if (data.status == 'error') {
            location.reload();
        }
	})// ajax close		

}); //close addresss List



$('#select-address').on('click',function(){
	alert('hi');
			

}) //close addresss List
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-9 mx-auto">
                    <div class="pace-part main-tow">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pace-left">
                                    <?php 
                                    $image  = DrsPanel::getUserAvator($doctorProfile->user_id);
                                      echo Lightbox::widget([
                                            'files' => [
                                            [
                                            'thumb' => $image,
                                            'original' => $image,
                                            'title' => $doctorProfile->name,
                                            ],
                                            ]
                                            ]);
                                     ?>
                                </div>
                                <div class="pace-right">
                                    <h4><?= $doctorProfile->name ?></h4>
                                    <p><?= $doctorProfile->speciality ?></p>
                                    <div class="form-group">
                                        <p>Exp: <strong> <?php echo !empty($doctorProfile->experience)?$doctorProfile->experience:'0';?> + </strong> years</p>
                                    </div>
                                    <div class="review_top_details hide">
                                       <div class="rate-ex1-cnt yellow-star">
                                            <?php
                                            $total_rating = Drspanel::getRatingStatus($doctorProfile->user_id);
                                            if(!empty($total_rating)) { ?>
                                                <i class="fa fa-star" aria-hidden="true"></i> <?php echo isset($total_rating['rating'])?$total_rating['rating']:'0';?>
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                    </div>    

                                    <?php if(!empty($loginUser) && $loginUser->groupid == \common\models\Groups::GROUP_PATIENT) { ?>
                                        <div class="row">
                                            <div class="col-sm-12 text-right">
                                                <button type="button" class="book-greenbtn " id="doctor-addresss-list"> Book Appointment </button>
                                            </div>
                                        </div>
                                    <?php } else{ ?>
                                        <div class="row">
                                            <div class="col-sm-12 text-right">
                                                <button type="button" class="book-greenbtn modal-call" id="login-popup"> Book Appointment </button>
                                            </div>
                                        </div>
                                    <?php }?>
                                </div>
                                <div class="clearfix"></div>

                                <div class="likesicon-part">
                                    <div class="add-record record_part text-right">
                                        <ul>
                                            <li>
                                                <?php echo $this->render('_favorite',['doctor_id'=>$doctor_id,'user_type'=>'patient','request_to'=>'doctor'])?>
                                            </li>
                                          
                                            <li class="dropdown"><a href="javascript:void(0)"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-share-alt"></i></a>
                                                <ul class="dropdown-menu">
                                                    <li class="social_click" data-type="facebook" data-doctor_id="<?= $doctor_id; ?>"><a href="javascript:void(0)"><i class="fa fa-facebook"></i> Facebook </a></li>
                                                    <li class="social_click" data-type="twitter" data-doctor_id="<?= $doctor_id; ?>"><a href="javascript:void(0)"><i class="fa fa-twitter"></i> Twitter </a></li>
                                                    <li class="social_click" data-type="google" data-doctor_id="<?= $doctor_id; ?>"><a href="javascript:void(0)"><i class="fa fa-google-plus"></i> Google+ </a></li>

                                                </ul>
                                            </li>
                                              <li class="yellow-star star_rat">
                                                <?php
                                                $total_rating = Drspanel::getRatingStatus($doctorProfile->user_id);
                                                if(!empty($total_rating))
                                                { ?>
                                                    <i class="fa fa-star yellow-star" aria-hidden="true"></i> <?php echo isset($total_rating['rating'])?$total_rating['rating']:'0';?>
                                                    <?php
                                                }
                                                ?>
                                            </li>
                                        </ul>
                                        <a href="#" class="ssbp-btn" style="display:none;"></a>
                                    </div>

                                    
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php
                    $date = date('Y-m-d');
                    $doctorHospital = DrsPanel::getBookingAddressShifts($doctorProfile->user_id, $date);
                    echo $this->render('_hospitals',['hospitals' => $doctorHospital]);
                    ?>
                    <?php echo $this->render('_treatments',['treatments' =>$doctorProfile->treatment]); ?>
                    <?php echo $this->render('_education',['user_id' => $doctorProfile->user_id]); ?>
                    <?php echo $this->render('_experience',['user_id' => $doctorProfile->user_id]); ?>
                    <?php // echo $this->render('feedback'); ?>
                </div>
                <?php echo $this->render('/layouts/rightside'); ?>
            </div>
        </div>
    </div>
</section>

<!-- Model confirm message Sow -->
<div class="login-section ">
    <div id="address-list-modal" class="modal model_opacity" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="addressHeading">Doctor <span> Address list </span></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body addressListHospital" id="address-list-modal-content">

                </div>
            </div>
        </div>
    </div>
</div>