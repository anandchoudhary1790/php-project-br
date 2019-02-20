<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserAppointment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-appointment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'token',
            'type',
            'appointment_type',
            'user_id',
            'doctor_id',
            'appointment_date',
            'appointment_shift',
            'appointment_time',
            'book_for',
            'user_name',
            'user_age',
            'user_phone',
            'user_address',
            'user_gender',
            'payment_type',
            'doctor_name',
            'doctor_address',
            'doctor_fees',
            'status',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
