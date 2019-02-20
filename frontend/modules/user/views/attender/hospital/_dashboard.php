<?php
use yii\helpers\Html;
use common\models\MetaKeys;
use common\models\MetaValues;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use kartik\select2\Select2;
?>
<?php $base_url= Yii::getAlias('@frontendUrl'); ?>
<?php $this->title = Yii::t('frontend', 'Doctor::Dashboard', ['modelAddressClass' => 'Doctor']);?>

<div class="inner-banner"> </div>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="appointment_part">
                        <div class="appointment_details">
                            <div class="pace-part main-tow">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="pace-left">
                                            <?php if($userProfile->avatar){  ?>
                                                <img src="<?php echo $userProfile->avatar_base_url.$userProfile->avatar_path.$userProfile->avatar; ?>" alt="image">
                                            <?php } else { ?>
                                                <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="Profile Image">
                                            <?php } ?> </div>
                                        <div class="pace-right">
                                            <h4><?php echo $userProfile->prefix.' '.$userProfile->name; ?> <small> (<?php echo !empty($userProfile->experience)?$userProfile->experience:'0'; ?> years) </small> <a href="#" class="pull-right notification-cionmain"> <i class="fa fa-bell"></i> </a></h4>
                                            <p> <?php echo $userProfile->address1; ?> </p>
                                            <div class="doctor-educaation-details">
                                                <?php if($userProfile->speciality) { ?><p>Specialization : <span> <?php echo $userProfile->speciality; ?> </span> </p> <?php } ?>
                                                <?php if($userProfile->gender==1) { ?> <p> Gender :<span>Male</span> </p> <?php }
                                                else if($userProfile->gender==2){ ?> <p> Gender :<span>Female</span> </p> <?php }
                                                else if($userProfile->gender==3){ ?> <p> Gender :<span>Other</span> </p> <?php }?>
                                                <?php if($userProfile->experience){ ?><p>Experience : <span>+<?php echo empty($userProfile->experience)?$userProfile->experience:'0'; ?> yrs </span> </p> <?php } ?>
                                            </div>
                                            <a href="#" class="range-slider"> <span class="pull-right"> Complete Profile 40%</span><img src="<?php echo $base_url?>/images/range-slider.png"></a> </div>
                                        <?php if($userProfile->created_by=='Doctor') {?>
                                            <div class="repl-foracpart">
                                                <ul>
                                                    <li> <a href="<?php echo $base_url.'/attender/my-patients'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon1.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>My Patients</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="#">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon2.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>My Articles</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/my-shifts'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>My Schedules </p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/shifts'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>My Shifts Time</p>
                                                            </div>
                                                        </a> </li>
                                                    <li> <a href="<?php echo $base_url.'/doctor/patient-history'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>History</p>
                                                            </div>
                                                        </a> </li>
                                                    <li> <a href="<?php echo $base_url.'/doctor/user-statistics-data'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>User Statistics Data</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/attenders'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon1.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>My Attenders</p>
                                                            </div>
                                                        </a> </li>
                                                    <li> <a href="<?php echo $base_url.'/doctor/hospitals'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon3.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Hospitals/Clinics</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/accept-hospital-request'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Accept Hospital Request</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a class="modal-call" href="javascript:void(0)" title="Add More " id="speciality-popup">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon4.png" alt="image">  </div>

                                                            <div class="datacontent-et">
                                                                <p>Speciality</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/experiences'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon5.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Experience</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="<?php echo $base_url.'/doctor/educations'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon6.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Education</p>
                                                            </div>
                                                        </a> </li>
                                                    <li class="hide"> <a href="#">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon7.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Facility</p>
                                                            </div>
                                                        </a> </li>
                                                    <li> <a href="<?php echo $base_url.'/doctor/customer-care'?>">
                                                            <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/img15.png" alt="image"> </div>
                                                            <div class="datacontent-et">
                                                                <p>Customer Care</p>
                                                            </div>
                                                        </a> </li>
                                                </ul>
                                            </div
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>