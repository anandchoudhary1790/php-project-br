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
$baseUrl=Yii::getAlias('@frontendUrl');
$userProfile = new UserProfile();
?>

<div class="main-navbarpart">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $baseUrl?>">
                <img src="<?php echo $baseUrl; ?>/images/logo.png"/>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="down-arrow"><i class="fa fa-angle-down"></i></span>
            </button>
            <?php echo $this->render('../../views/search/_searchbox')?>
     
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <?php if(Yii::$app->user->isGuest){ ?>
                        <li class="nav-item">
                          <a class="nav-link" href="#"><i><img src="<?php echo $baseUrl; ?>/images/call-icon.png"></i> Nearby</a>
                        </li>
                        <li class="download nav-item">
                          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Jaipur</a>
                          <ul class="dropdown-menu dropdown-primary top-userpart ">
                            <li><a href="#">Jaipur</a></li>
                            <li><a href="#">Raja park</a></li>
                            <li><a href="#">Jaipur</a></li>
                            <li><a href="#">Jaipur</a></li>
                          </ul>
                          <?php } ?>
                        </li>
                        <?php
                        if(Yii::$app->user->id){
                            $login_user_data=Yii::$app->user->identity;
                            if(isset($login_user_data->groupid) && $login_user_data->groupid=='3'){
                                $action_url  = '/patient/profile'; ?>
                                <li class="nav-item">
                                                          <a class="nav-link" href="#"><i><img src="<?php echo $baseUrl; ?>/images/call-icon.png"></i> Nearby</a>
                                                        </li>
                                                        <li class="download nav-item">
                                                          <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Jaipur</a>
                                                          <ul class="dropdown-menu dropdown-primary top-userpart ">
                                                            <li><a href="#">Jaipur</a></li>
                                                            <li><a href="#">Raja park</a></li>
                                                            <li><a href="#">Jaipur</a></li>
                                                            <li><a href="#">Jaipur</a></li>
                                                          </ul></li>

                            <?php }
                            elseif(isset($login_user_data->groupid) && $login_user_data->groupid=='4') {
                                $action_url  = '/doctor/profile';
                            }
                            elseif(isset($login_user_data->groupid) && $login_user_data->groupid=='5') {
                                $action_url  = '/hospital/profile';
                            } 
                            elseif(isset($login_user_data->groupid) && $login_user_data->groupid=='6') {
                                $action_url  = '/attender/profile';
                            }
                            ?>
                            <li class="download nav-item">
                            <a class="nav-link " href="<?php echo $baseUrl.$action_url?>">
                                <span class="nm"><?php echo isset($login_user_data['userProfile']['name'])?$login_user_data['userProfile']['name']:''; ?></span>
                                     &nbsp;<i><img src="<?php echo $baseUrl; ?>/images/profile-icon.png"></i>
                            </a>
                            </li>
                        <?php }
                        if(Yii::$app->user->isGuest){ ?>
                            <li class="download nav-item guest_user">
                            <a class="nav-link " href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i><img src="<?php echo $baseUrl?>/images/profile-icon.png"></i> </a>
                            <ul class="dropdown-menu top-userpart login-menu">
                              <li>
                                <h5>Your Account</h5>
                                <p>Access account and manage orders</p>
                                <a class="login-btn modal-call" href="javascript:void(0)" id="login-popup"><i class="fa fa-user"></i> Login</a>
                                <a class="login-btn modal-call" href="javascript:void(0)" id="signup-popup"><i class="fa fa-user"></i> Register</a>
                              </li>
                            </ul>
                            </li>
                        <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</div>
<div class="inner-banner"> </div>
<?php if(Yii::$app->user->id) { echo $this->render('menus.php');} ?>
<?php ?>
