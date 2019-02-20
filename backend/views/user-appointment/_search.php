<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserAppointmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-appointment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'token') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'appointment_type') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'doctor_id') ?>

    <?php // echo $form->field($model, 'appointment_date') ?>

    <?php // echo $form->field($model, 'appointment_shift') ?>

    <?php // echo $form->field($model, 'appointment_time') ?>

    <?php // echo $form->field($model, 'book_for') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'user_age') ?>

    <?php // echo $form->field($model, 'user_phone') ?>

    <?php // echo $form->field($model, 'user_address') ?>

    <?php // echo $form->field($model, 'user_gender') ?>

    <?php // echo $form->field($model, 'payment_type') ?>

    <?php // echo $form->field($model, 'doctor_name') ?>

    <?php // echo $form->field($model, 'doctor_address') ?>

    <?php // echo $form->field($model, 'doctor_fees') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
