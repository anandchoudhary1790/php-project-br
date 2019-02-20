<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\MetaValues;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MetaValuesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blood Group';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-index">
            <p>
                <?= Html::a('Create Blood Group', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'label',
                    'value:ntext',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'enum' => MetaValues::statuses(),
                        'filter' => MetaValues::statuses()
                    ],
                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link =Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['aria-label'=>'Edit', 'title'=>'Edit']);

                            return $link;
                        }
                    ],        ],
            ]); ?>
        </div>
    </div>
</div>