<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use common\components\DrsPanel;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getPublicIdentity();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Daily Patient Limit';
$backend=Yii::getAlias('@backendUrl');
$shiftAddUrl=$backend.'/doctor/add-shift?id='.$model->id; 
$shiftUrl="'single-shift-time'";
$user_id=$model->id;
$shift_delete="'shift-delete'";
$js="
$('.set_in_dailog').on('click', function () {
    id=$(this).attr('id');
    $.ajax({
          method: 'POST',
          url: $shiftUrl,
          data: { id: id,user_id:$user_id}
    })
      .done(function( msg ) { 
        if(msg){
        $('#set-model-content').html('');
        $('#set-model-content').html(msg);
        $('#edit-shift-model').modal({backdrop: 'static',keyboard: false})
        }
      });

   
});

$('.confirm-dailog').on('click', function () {
	id=$(this).attr('data-id');
	del_id=$(this).attr('id');
	var text_msg='<p>Are you sure confirm delete?</p>';
  	$('#ConfirmModalContent').html(text_msg);
	$('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false})
	.one('click', '#confirm_ok', function(e) {
         $.ajax({
         type: 'POST',
          url: $shift_delete,
          data: { id: id,user_id:$user_id},
          success: function(msg) {
            if(msg){
            $('#del_'+del_id).remove()
            }
            return false;
           }
        })
      })    

   
});

";

$this->registerJs($js, \yii\web\VIEW::POS_END); 
?>
<div class="box">
    <div class="box-body">
    <a class="btn btn-success" href="<?php echo $shiftAddUrl?>">Add</a>
        <div class="user-view">

            <div id="ajaxLoadShiftDetails">

               <?php echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'grid-view table-responsive'
                ],
                'columns' => [
                    'id',
                    'weekday',
                    'shift',
                     [
                            'attribute'=>'start_time',
                        'value'=>function($data){
                                    return date('h:i a',$data->start_time);
                        }
                    ],
                    [
                            'attribute'=>'end_time',
                        'value'=>function($data){
                                    return date('h:i a',$data->end_time);
                        }
                    ],
                    [
                        'content' => function ($model, $key, $index, $column) {
                            $link = Html::a('<span class="glyphicon glyphicon-pencil "></span>',null, ['aria-label'=>'View', 'title'=>'View','class'=>'set_in_dailog','id'=>$model->id]);
                            $del = Html::a('<span class="glyphicon glyphicon-trash "></span>',null, ['aria-label'=>'View', 'title'=>'View','class'=>'confirm-dailog','data-id'=>$model->id]);
                            return $link.$del;
                        }
                    ],
                ],
            ]); ?>
               <?php /* $this->render('_editShift', [
                    'model' => $model,'userShift'=>$userShift,'listaddress'=>$listaddress,'week'=>$week,'week_array'=>$week_array
                ]); */ ?>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Model confirm message Sow -->
<div id="edit-shift-model" class="modal fade " role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
  <div class="modal-body" id="set-model-content">

  </div>
  </div>
  </div>
  
</div>