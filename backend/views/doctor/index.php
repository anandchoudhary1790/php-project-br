<?php

use common\grid\EnumColumn;
use common\models\User;
use common\models\Groups;
use yii\helpers\Html;
use yii\grid\GridView;
use common\components\DrsPanel;
use yii\widgets\Pjax;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Doctors');
$this->params['breadcrumbs'][] = $this->title;
$baseUrl=Yii::getAlias('@backendUrl');
$model_name="'User'";
$type=Groups::GROUP_DOCTOR;

$this->registerJs("
    $('select').change(function(){
       $('.form-group button.submit').click();
    });

     $('.user-search form').submit(function(){
        $.pjax({container:'#users-grid',push:false,replace:false,data:$(this).serialize()});
        return false;
    }); 
            
",View::POS_END);

$this->registerJs("      
    
    function modal_edit_actions(id){   
        $.ajax({
            url: 'get-edit-livemodal',
            dataType:   'html',
            method:     'POST',
            data: { id: id},
            success: function(response){                
                $('#open_model_live').empty();
                $('#open_model_live').append(response);
                $('#editlivestatusmodal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
                setTimeout(function(){
                    $('body').addClass('modal-open');
                }, 400);
            }
        });
    }
    
    
",View::POS_END);

?>
<div class="box">
    <div class="box-body">
        <div class="user-index">

            <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

            <p>
                <?php echo Html::a(Yii::t('backend', 'Add new {modelClass}', [
            'modelClass' => 'Doctor',
        ]), ['create'], ['class' => 'btn btn-success']) ?>
            </p>


            <?php Pjax::begin([ 'id'=>'users-grid','enablePushState'=>false, ]); ?>

                <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
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
                        'enum' => User::admin_statuses(),
                        'filter' => User::admin_statuses(),
                        'content' => function ($data) {
                            $adminStatus =  User::admin_statuses();

                              $link = Html::a($adminStatus[$data->admin_status], 'javascript:void(0)', ['onclick'=>"return modal_edit_actions($data->id);",'aria-label'=>'Edit', 'title'=>'Edit']);
                                return $link;
                        }
                    ],
                    'created_at:datetime',
                    'logged_at:datetime',

                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['detail', 'id' => $model->id], ['aria-label'=>'View', 'title'=>'View']);
                            $link2 = Html::a('<span class="fa fa-hospital-o"></span>', ['requested-hospital', 'id' => $model->id], ['aria-label'=>'Requested Hospital', 'title'=>'Requested Hospital']); 
                            $link3 = Html::a('<span class="fa fa-user-md"></span>', ['attender-list', 'id' => $model->id], ['aria-label'=>'Attender List', 'title'=>'Attender List']);
                            return $link.'&nbsp;'.$link2.'&nbsp;'.$link3;
                        }
                    ],
                ],
            ]); ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>

<div class="modal fade" id="editlivestatusmodal" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalContact">Update Profile/Live Status</h4>
            </div>
            <div class="modal-body" id="open_model_live">


            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
