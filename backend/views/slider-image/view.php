<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SliderImage */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Slider Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="box">
        <div class="box-body">       
            <div class="slider-image-view">

                <h1><?= Html::encode($this->title) ?></h1>

                <p>
                    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Are you sure you want to delete this item?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </p>

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'title',
                        'sub_title',
                        'pages',
                        [
                            'attribute' => 'image',
                            'value'=>function($data){
                                return Yii::getAlias('@storageUrl/source/slider-images/').$data->image;
                            },
                            'format'=>['image',['width'=>'100','height'=>'100']],
                        ],
                        'description:ntext',
                        'status',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        
                    ],
                ]) ?>

            </div>
        </div>
    </div>
