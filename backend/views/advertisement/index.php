<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AdvertisementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Advertisements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="advertisement-index">
            <p>
                <?= Html::a('Create Advertisement', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'id',
                    'title',
                    'link',
                    'start_date',
                    'end_date',
                    //'show_for_seconds',
                    //'image_path',
                    //'image_base_url:url',
                    //'status',
                    //'sequence',
                    //'created_at',
                    //'updated_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>
</div>