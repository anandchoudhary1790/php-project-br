<?php $baseUrl= Yii::getAlias('@frontendUrl'); ?>
<div id="wrapper" class="">
    <nav class="navbar navbar-inverse navbar-fixed-top" id="sidebar-wrapper" role="navigation">
        <ul class="nav sidebar-nav">

            <li>
                <a href="<?php echo $baseUrl.'/doctor/profile'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/home.png"> </i> Profile</a>
            </li>

            <li>
                <a href="<?php echo $baseUrl.'/doctor/appointments'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/age.png"></i> Appointments</a>
            </li>

            <li>
                <a href="<?php echo $baseUrl.'/doctor/my-shifts'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/shifts_icon.png"></i>  My Shifts</a>
            </li>
            <li>
                <a href="<?php echo $baseUrl.'/doctor/day-shifts'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/timing_icon.png"></i> Today Timing</a>
            </li>

             <li>
                <a href="<?php echo $baseUrl.'/doctor/attenders'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/attender_icon.png"></i> My Attenders</a>
            </li> 


            <li>
                <a href="<?php echo $baseUrl.'/doctor/accept-hospital-request'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/hospital_icon.png"></i> Hospital Request</a>
            </li>


            <li>
                <a href="<?php echo $baseUrl.'/doctor/patient-history'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/history_icon.png"></i> History</a>
            </li>

            <li>
                <a href="<?php echo $baseUrl.'/doctor/user-statistics-data'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/user_statics.png"></i> User Statics Data</a>
            </li>

            <li>
                <a href="<?php echo $baseUrl.'/doctor/customer-care'?>"><i class="left_menu_n"><img src="<?php echo $baseUrl?>/images/menu-icon/customar_care.png"></i> Customer Care</a>
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