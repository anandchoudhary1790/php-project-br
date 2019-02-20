<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\SliderImageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Slider Images';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="slider-image-index">

            <h1><?= Html::encode($this->title) ?></h1>
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <p>
                <?= Html::a('Create Slider Image', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'grid-view table-responsive '
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'title',
                    'sub_title',
                    'pages',
                    'image',
                 //   'description:ntext',
                    //'status',
                    //'created_at',
                    //'updated_at',
                    //'deleted_at',
                    

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>
</div>
