<?php 

use yii\helpers\Html;
use common\models\Groups;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use backend\modelAddresss\AddScheduleForm;
use common\components\DrsPanel;
use common\models\UserProfile;
use common\models\User;
use kartik\select2\Select2;
$userProfile = new UserProfile();
$baseUrl= Yii::getAlias('@frontendUrl'); ?>
<?php if(Yii::$app->user->id) 
  { $login_user_data=Yii::$app->user->identity;?>
    <div class="signup-part">
      <?php if($login_user_data->groupid==Groups::GROUP_HOSPITAL) { ?>
        <div id="wrapper" class="">
          <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
            <ul class="nav sidebar-nav">
                <li>
                    <a href="<?php echo $baseUrl.'/hospital/profile'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/home.png"> </i> Profile</a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl.'/hospital/appointments'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/age.png"></i> Appointments</a>
                </li> 
                <li>
                    <a href="<?php echo $baseUrl.'/hospital/my-doctors'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/my_doctor.png"></i> My Doctors</a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl.'/hospital/find-doctors'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/find_doctors.png"></i> Find Doctors</a>
                </li>
                <li>
                    <a href="<?php echo $baseUrl.'/hospital/attenders'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/attender_icon.png"></i> My Attenders</a>
                </li>
              <li class="hide">
                <a href=""><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/timing_icon.png"></i> Today Timing</a>
              </li>
              <li>
                <a href="<?php echo $baseUrl.'/hospital/patient-history'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/hospital_icon.png"></i> History</a>
              </li>
              <li>
                <a href="<?php echo $baseUrl.'/hospital/user-statistics-data'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/user_statics.png"></i> User Statics Data</a>
              </li>
              <li>
                <a href="<?php echo $baseUrl.'/hospital/customer-care'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/customar_care.png"></i> Customer Care</a>
              </li>
              <li class="hide">
                <a href=""><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/time-wach.png"></i> Help & Support</a>
              </li>
              <li class="hide">
                <a href=""><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/time-wach.png"></i> Settings</a>
              </li>

              <li>
                <a href="<?php echo $baseUrl; ?>/logout" data-method='post'><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/logout.png"></i> Logout</a>
              </li>
            </ul>
          </nav>
          <div id="page-content-wrapper">
            <button type="button" id="sidebar_btn" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
              <span class="hamb-top"></span>
              <span class="hamb-middle"></span>
              <span class="hamb-bottom"></span>
            </button>
          </div>
        </div>
        <?php }
        elseif($login_user_data->groupid==Groups::GROUP_PATIENT) {?>
        <div id="wrapper" class="">
          <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
            <ul class="nav sidebar-nav">
              <li>
                <a href="<?php echo $baseUrl?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/home.png"></i> Home</a>
              </li>
              <li class="hide">
                  <a href="<?php echo $baseUrl.'/patient/dashboard'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/home.png"> </i> Dashboard</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl.'/patient/profile'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/customar_care.png"></i> Profile Update</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl.'/patient/my-doctors'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/my_doctor.png"></i> My Doctors</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl.'/patient/customer-care'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/customar_care.png"></i> Customer Care</a>
              </li>
              <li class="">
                  <a href="<?php echo $baseUrl.'/patient/appointments'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/age.png"></i> Appointment</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl.'/patient/reminder'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/time-wach.png"></i> Reminder Option</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl.'/patient/records'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/record.png"></i> Patient Record</a>
              </li> 
              <li>
                  <a href="<?php echo $baseUrl.'/patient/my-payments'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/my-payment.png"></i> My Payments</a>
              </li>
              <li>
                  <a href="<?php echo $baseUrl; ?>/logout" data-method='post'><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/logout.png"></i> Logout</a>
              </li>
            </ul>
          </nav>
          <div id="page-content-wrapper">
            <button type="button" id="sidebar_btn" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
              <span class="hamb-top"></span>
              <span class="hamb-middle"></span>
              <span class="hamb-bottom"></span>
            </button>
          </div>
        </div>
        <?php }
        elseif($login_user_data->groupid==Groups::GROUP_DOCTOR) { ?>
            <?= $this->render('_doctormenu.php') ?>
        <?php } elseif($login_user_data->groupid==Groups::GROUP_ATTENDER) {?>
          <div id="wrapper" class="">
              <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
                  <ul class="nav sidebar-nav">
                    <li>
                    <a href="<?php echo $baseUrl.'/attender/edit-profile'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/home.png"></i> Profile Update</a>
                    </li>
                     <li>
                      <a href="<?php echo $baseUrl.'/attender/appointments'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/age.png"></i> Appointments</a>
                    </li>
                      <li>
                          <a href="<?php echo $baseUrl.'/attender/my-shifts'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/shifts_icon.png"></i>  My Shifts</a>
                      </li>
                      <li>
                          <a href="<?php echo $baseUrl.'/attender/day-shifts'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/timing_icon.png"></i> Today Timing</a>
                      </li>

                      <li>
                          <a href="<?php echo $baseUrl.'/attender/user-statistics-data'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/user_statics.png"></i> User Statics Data</a>
                      </li>
                    <li>
                        <a href="<?php echo $baseUrl.'/attender/customer-care'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/customar_care.png"></i> Customer Care</a>
                    </li>

                    <li>
                    <a href="<?php echo $baseUrl; ?>/logout" data-method='post'><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/logout.png"></i> Logout</a>
                    </li>
                  </ul>
              </nav>
              <div id="page-content-wrapper">
                  <button type="button" id="sidebar_btn" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
                      <span class="hamb-top"></span>
                      <span class="hamb-middle"></span>
                      <span class="hamb-bottom"></span>
                  </button>
              </div>
          </div>
      <?php } ?>
    </div>
  <?php } ?>