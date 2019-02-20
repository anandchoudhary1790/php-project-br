<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\grid\EnumColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\StatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'States';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="states-index">
            <p>
                <?= Html::a('Create States', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'code',
                    'name',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'enum' => \common\models\States::statuses(),
                        'filter' => \common\models\States::statuses()
                    ],
                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link =Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['aria-label'=>'Edit', 'title'=>'Edit']);

                            return $link;
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
