<?php
/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Add Appointment';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPublicIdentity(), 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Add Appointment';
?>
<div class="box">
    <div class="box-body">
        <div class="user-view">

                <div id="ajaxLoadBookingDetails">
                    <?= $this->render('_todayAppointment', [
                        'model' => $model,'userShift'=>$userShift,'date'=>$date,'shifts_available'=>$shifts_available,'addAppointment'=>$addAppointment,'keys_avail'=>$keys_avail
                    ]); ?>

                </div>


        </div>
    </div>
</div>