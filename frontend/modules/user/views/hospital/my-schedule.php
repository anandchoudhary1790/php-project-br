<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 
$updateStatus="'".$baseUrl."/doctor/ajax-shift-details'";
$this->title = Yii::t('frontend','DrsPanel :: Doctor Appoinments'); 
$js="


    $('.get-shift-data').on('click', function () {
      shift_id=$(this).attr('id');

        $.ajax({
            method:'POST',
            url: $updateStatus,
            data: {id:shift_id}
        })
        .done(function( reshtml ) { 

        $('#shift-content').html('');
        $('#shift-content').html(reshtml);
        $('#shift-update-modal').modal({backdrop: 'static',keyboard: false})

        });
    }); 
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
      <div id="shift-add">
            <?php $form = ActiveForm::begin([
                'id' => 'schedule-form-new',
                'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'schedule-form',
                ],
            ]); ?>
              <?php if($model->id){
                  echo $form->field($model,'id')->hiddenInput()->label(false);
                }?>
                    <div class="col-sm-12">
                    <div class="form-group week-list clearfix">
                    <h3><?= Yii::t('db','Availibility Days'); ?></h3>

                    <?php echo $form->field($model, 'weekday')
                        ->checkboxList($weeks, [
                            'item' => function ($index, $label, $name, $checked, $value) {

                                $return = '<div class="chkbox_blk col-sm-2"><span>';
                                $return .= Html::checkbox($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'week_' . $value]);
                                $return .= '<label for="week_' . $value . '" >' . Yii::t('db',ucwords($label)) . '</label>';
                                $return .= '</span></div>';

                                return $return;
                            }
                        ])->label(false) ?>
                </div>
                </div>

                <?php echo $this->render('shift/_newShift',['model'=>$model,'form'=>$form,'listaddress'=>$listaddress]); ?>
                 
                <div class="form-group clearfix">
                    <div class="col-md-12 text-right">
                        <input type="submit" class="show-profile schedule_form_btn" value="<?php echo Yii::t('db','Save'); ?>"/>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>

      </div>

      <div id="week-day-shifts">
      <?php echo $this->render('shift/_shift-days',['user_id'=>$loginUser->id,'scheduleslist'=>$scheduleslist]); ?>
      </div>
      <div class="clearfix" ></div>
         
      </div>
    </div>
  </div>
</section>


<div class="register-section">
<div id="shift-update-modal" class="modal fade model_opacity"  role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
       <h4 class="modal-title" id="myModalContact">Update Shifts</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               
            </div>
            <div class="modal-body" id="shift-content">

            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
</div>

