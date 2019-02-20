<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Patients');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-index">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <p>
                <?php echo Html::a(Yii::t('backend', 'Add New {modelClass}', [
            'modelClass' => 'Patient',
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
                    [
                        'attribute' => 'username',
                        'label'=>'Name',
                        'format' => 'raw',
                        'value' => function($data) {
                            return $data->username;
                        },
                    ],
                    'email:email',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'enum' => User::statuses(),
                        'filter' => User::statuses()
                    ],

                    [
                        'attribute' => 'created_at',
                        'filter' => \yii\jui\DatePicker::widget(['dateFormat' => 'yyyy-MM-dd','name'=>'PatientSearch[created_at]']),
                        'value' => function($data) {
                            if($data->created_at !==NULL){
                                return Yii::$app->formatter->asDate($data->created_at, 'php:Y M d H:i:s');
                            }else{
                                return $data->created_at;
                            }
                        },
                    ],

                    [
                        'attribute' => 'logged_at',
                        'filter' => \yii\jui\DatePicker::widget(['dateFormat' => 'yyyy-MM-dd','name'=>'PatientSearch[logged_at]']),
                        'value' => function($data) {
                            if($data->logged_at !==NULL){
                                return Yii::$app->formatter->asDate($data->logged_at, 'php:Y M d H:i:s');
                            }else{
                                return $data->logged_at;
                            }
                        },
                    ],
                    // 'updated_at',

                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $model->id], ['data-method'=> 'post','aria-label'=>'View', 'title'=>'View']);
                            $link .='  ';
                            $link .=Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id], ['aria-label'=>'Edit', 'title'=>'Edit']);

                            return $link;
                        }
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>