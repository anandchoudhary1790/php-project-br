<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\grid\EnumColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cities';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="cities-index">
            <p>
                <?= Html::a('Create Cities', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'state_id',
                    [
                        'attribute' => 'state_id',
                        'label'=>'State',
                        'format' => 'raw',
                        'value' => function($data) {
                            return \common\models\Cities::getStateName($data->state_id);
                        },
                    ],
                    'code',
                    'name',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'enum' => \common\models\Cities::statuses(),
                        'filter' => \common\models\Cities::statuses()
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
