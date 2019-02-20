<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  common\components\DrsPanel ;
$baseUrl= Yii::getAlias('@frontendUrl');


?>

<div class="col-md-12 mx-auto">

    <div class="success-reminderpart">
        <div class="reminderic-success"> <i class="fa fa-check"></i> </div>
        <div class="reminder-ctpart">
            <h4> Thank You! </h4>
        </div>
    </div>

    <div class="pace-part main-tow mb-2">
        <div class="row">
            <div class="col-sm-12">
                <div class="reminder-left">
                    <p class="text-reminder">To </p>
                    <h4><?= $booking['doctor_name']?></h4>
                    <p> <?= $booking['doctor_speciality']?></p>
                    <p> <?= $booking['doctor_address']?></p>
                </div>
                <div class="reminder-right text-right"> <img src="<?= $booking['doctor_image']?>" alt="image"></div>
            </div>
        </div>
    </div>
    <form class="appoiment-form-part">
        <div class="btdetialpart">
            <div class="pull-left"><?= $booking['patient_name']?></div>
            <div class="pull-right"> <?= $booking['patient_mobile']?> </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Date</p>
                <p><strong><?= date('d M, Y',strtotime($booking['appointment_date']));?></strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Appointment Time</p>
                <p><strong><?= $booking['appointment_time']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Token Number</p>
                <p><strong><?= $booking['token']?></strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Approx consultation Time</p>
                <p><strong><?= $booking['appointment_approx_time']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <p>Fees</p>
                <p><strong><i class="fa fa-rupee" aria-hidden="true"></i> <?= $booking['fees']?>/Session</strong></p>
            </div>
            <div class="pull-right text-right">
                <p>Booking id</p>
                <p><strong><?= $booking['booking_id']?></strong></p>
            </div>
        </div>
        <div class="btdetialpart">
            <div class="pull-left">
                <input type="button" class="confirm-theme" value="Ok" onClick="refreshPage()">
            </div>
            <div class="pull-right text-right">
                <input type="button" class="confirm-theme" value="Print ">
            </div>
        </div>
    </form>
</div>
