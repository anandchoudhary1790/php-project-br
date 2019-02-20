<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use common\components\DrsPanel;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Hospitals');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-index">

            <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

            <p>
                <?php echo Html::a(Yii::t('backend', 'Add new {modelClass}', [
            'modelClass' => 'Hospital',
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
                            'attribute'=>'name',
                        'value'=>function($data){
                                    return DrsPanel::getUserName($data->id);
                        }
                    ],
                    'email:email',
                    'phone',
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'status',
                        'label'=>'Login Status',
                        'enum' => User::statuses(),
                        'filter' => User::statuses()
                    ],
                    [
                        'class' => EnumColumn::className(),
                        'attribute' => 'admin_status',
                        'label'=>'Profile Status',
                        'enum' => User::statuses(),
                        'filter' => User::statuses()
                    ],
                    'created_at:datetime',
                    'logged_at:datetime',

                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['detail', 'id' => $model->id], ['aria-label'=>'View', 'title'=>'View']);
                          
                            $requestToDoctor = Html::a('<span class="fa fa-address-book-o"></span>', ['request-to-doctors', 'id' => $model->id], ['aria-label'=>'Request to Doctors', 'title'=>'Request to Doctors']);
                             $link3 = ''; //Html::a('<span class="fa fa-user-md"></span>', ['attender-list', 'id' => $model->id], ['aria-label'=>'Attender List', 'title'=>'Attender List']);
                            return $link.$requestToDoctor.$link3;
                        }
                    ],
                ],
            ]); ?>

        </div>
    </div>
</div>
