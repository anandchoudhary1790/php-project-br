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
                                                <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="">
                                            <?php } ?> </div>
                                        <div class="pace-right">
                                            <h4><?php echo $userProfile->prefix.' '.$userProfile->name; ?> <small> (<?php echo $userProfile->experience; ?> yrs) </small> <a href="#" class="pull-right notification-cionmain"> <i class="fa fa-bell"></i> </a></h4>
                                            <p> <?php echo $userProfile->address1; ?> </p>

                                            <a href="#" class="range-slider"> <span class="pull-right"> Complete Profile 40%</span><img src="<?php echo $base_url?>/images/range-slider.png"></a> </div>
                                        <div class="repl-foracpart">
                                            <ul>
                                                <li> <a href="<?php echo $base_url.'/attender/my-schedule'?>">
                                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                        <div class="datacontent-et">
                                                            <p>My Schedules </p>
                                                        </div>
                                                    </a> </li>
                                                <li> <a href="<?php echo $base_url.'/attender/shifts'?>">
                                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                        <div class="datacontent-et">
                                                            <p>My Shifts Time</p>
                                                        </div>
                                                    </a> </li>
                                                <li> <a href="<?php echo $base_url.'/attender/patient-history'?>">
                                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                        <div class="datacontent-et">
                                                            <p>History</p>
                                                        </div>
                                                    </a> </li>
                                                <li> <a href="<?php echo $base_url.'/attender/user-statistics-data'?>">
                                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-clock-icon.png" alt="image"> </div>
                                                        <div class="datacontent-et">
                                                            <p>User Statistics Data</p>
                                                        </div>
                                                    </a> </li>
                                               
                                                <li> <a href="<?php echo $base_url.'/attender/customer-care'?>">
                                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/img15.png" alt="image"> </div>
                                                        <div class="datacontent-et">
                                                            <p>Customer Care</p>
                                                        </div>
                                                    </a> </li>
                                            </ul>
                                        </div>
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