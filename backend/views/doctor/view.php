<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'View Appointments';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-view">

            <p class="hide">
                <?php echo Html::a(Yii::t('backend', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('backend', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'booking_type',
                    'type',
                    'token',
                    'user_id',
                    'user_name',
                    'user_age',
                    'user_phone',
                    'user_address',
                    'user_gender',
                    'doctor_id',
                    'doctor_name',
                    'doctor_address',
                    'doctor_address_id',
                    'doctor_fees',
                    'date',
                    'weekday',
                    'start_time',
                    'end_time',
                    'shift_name',
                    'schedule_id',
                    'slot_id',
                    'book_for',
                    'payment_type',
                    'service_charge',
                    'status',
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>

        </div>
    </div>
</div>