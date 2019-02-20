<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \backend\models\search\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Popular Speclization , Treatment and hospital');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="page-index">
            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
            <p>
                <?php echo Html::a(Yii::t('backend', 'Create {modelClass}', [
            'modelClass' => 'Page',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'grid-view table-responsive'
                ],
                'columns' => [
                    'id',
                    'title',
                    'slug',
                    'status',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template'=>'{update} {delete}'
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
