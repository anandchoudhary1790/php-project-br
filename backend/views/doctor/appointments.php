<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\components\DrsPanel;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserAppointmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Total Appointments for Doctor "'.$model->getPublicIdentity().'"';
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getPublicIdentity(), 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Appointments';
?>
<div class="box">
    <div class="box-body">
        <div class="user-appointment-index">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'grid-view table-responsive'
                ],
                'columns' => [
                    'id',
                    'token',
                    'type',
                    'user_name',
                    'user_phone',
                    'book_for',

                    //'user_age',

                    //'user_address',
                    //'user_gender',
                    'payment_type',

                    //'doctor_address',
                    'doctor_fees',
                    'status',
                    'created_at',
                    //'updated_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>
</div>
