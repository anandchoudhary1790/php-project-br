<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\grid\EnumColumn;
use common\models\MetaKeys;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\MetaKeysSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Meta Data Keys';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-keys-index">
            <p>
                <?= Html::a('Create Keys', ['create'], ['class' => 'btn btn-success']) ?>
            </p>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'key',
                    'label',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'enum' => MetaKeys::statuses(),
                        'filter' => MetaKeys::statuses()
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