<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserAppointment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-appointment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'token')->textInput() ?>

    <?= $form->field($model, 'booking_type')->dropDownList([ 'online' => 'Online', 'offline' => 'Offline', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'type')->dropDownList([ 'consultation' => 'Consultation', 'emergency' => 'Emergency', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'doctor_id')->textInput() ?>




    <?= $form->field($model, 'book_for')->dropDownList([ 'self' => 'Self', 'other' => 'Other', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_age')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_gender')->textInput() ?>

    <?= $form->field($model, 'payment_type')->dropDownList([ 'cash' => 'Cash', 'already_paid' => 'Already paid', 'paytm' => 'Paytm', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'doctor_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'doctor_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'doctor_fees')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'pending' => 'Pending', 'skip' => 'Skip', 'completed' => 'Completed', 'cancelled' => 'Cancelled', ], ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
