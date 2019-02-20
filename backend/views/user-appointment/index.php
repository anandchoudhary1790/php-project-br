<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserAppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Appointments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-appointment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Appointment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'token',
            'type',
            'appointment_type',
            'user_id',
            //'doctor_id',
            //'appointment_date',
            //'appointment_shift',
            //'appointment_time',
            //'book_for',
            //'user_name',
            //'user_age',
            //'user_phone',
            //'user_address',
            //'user_gender',
            //'payment_type',
            //'doctor_name',
            //'doctor_address',
            //'doctor_fees',
            //'status',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
