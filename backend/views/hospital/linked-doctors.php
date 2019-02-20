<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use common\components\DrsPanel;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Doctors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-index">

            <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'grid-view table-responsive'
                ],
                'columns' => [
                    'id',
                    [
                            'attribute'=>'name',
                        'value'=>function($data){
                                    return DrsPanel::getUserName($data->id);
                        }
                    ],
                    'email:email',
                    'phone',

                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['doctor/detail', 'id' => $model->id], ['aria-label'=>'View', 'title'=>'View']);
                            $link2 = '';//Html::a('<span class="glyphicon glyphicon-user"></span>', ['requested-hospital', 'id' => $model->id], ['aria-label'=>'Requested Hospital', 'title'=>'Requested Hospital']);
                            return $link.$link2;
                        }
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>
