<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('backend', 'My Shifts Timing');
$this->params['breadcrumbs'][] = $this->title;

/*<div class="row" id="useraddress">

    <div class="col-md-8">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Shift Timing & Fees</h3>
            </div>
            <div class="panel-body">
                 <?php
                if(!empty($address_list))
                {
                    foreach($address_list as $key=>$list) {
                        echo $this->render('_shifts',['list' => $list,'doctor_id'=>$doctor_id]);
                    }
                } else {  ?>
                    <div class="col-md-12 text-center">Shifts not available.</div>
                <?php } ?>
            </div>
        </div>
    </div> 
onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/add-shift?id='.$userProfile->user_id]); ?>';"

    */?>

    <section class="mid-content-part">
        <div class="container">
                <div class="row">
                    <div class="col-md-10 mx-auto">

                        <div class="today-appoimentpart">
                            <div class="col-md-12 calendra_slider">
                                <h3> My Shifts </h3>
                                <div class="calender_icon_main location pull-right ">
                                    <a class="modal-call" href="javascript:void(0)" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/add-shift?id='.$doctor_id]); ?>';" title="Add Shift">
                                        <i class="fa fa-plus-circle"></i>
                                    </a>
                                </div>
                            </div>
                        </div>


                        <?php
                        if(!empty($address_list))
                        {
                            foreach($address_list as $key=>$list) {
                                echo $this->render('_shifts',['list' => $list,'doctor_id'=>$doctor_id]);
                            }
                        } else {  ?>
                        <div class="col-md-12 text-center">Shifts not available.</div>
                        <?php } ?>
                    </div>
                </div>
        </div>
    </section>