<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;

$this->title = Yii::t('backend', 'Add New Shift', [
    'modelClass' => 'Doctor',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box">
    <div class="box-body">
        <div class="user-create">
            <div class="user-form">
            <?php $form = ActiveForm::begin([
                'id' => 'schedule-form-new',
                'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
                'options' => [
                    'enctype' => 'multipart/form-data',
                    'class' => 'schedule-form',
                ],
            ]); ?>

                    <div class="col-sm-12">
                    <div class="form-group clearfix">
                    <h3><?= Yii::t('db','Availibility Days'); ?></h3>

                    <?php echo $form->field($model, 'weekday')
                        ->checkboxList($weeks, [
                            'item' => function ($index, $label, $name, $checked, $value) {

                                $return = '<div class="chkbox_blk"><span>';
                                $return .= Html::checkbox($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'week_' . $value]);
                                $return .= '<label for="week_' . $value . '" >' . Yii::t('db',ucwords($label)) . '</label>';
                                $return .= '</span></div>';

                                return $return;
                            }
                        ])->label(false) ?>
                </div>
                </div>
                <div class="col-sm-12">

                    <div class="form-group clearfix">
                             <?php /*echo $form->field($model, 'availibility_days',['options'=>['class'=>
                                'col-sm-12']])->dropDownList($weeks)->label('Availibility Days');
                             */ ?>
                            <?php echo $this->render('_newShift',['model'=>$model,'form'=>$form,'listaddress'=>$listaddress]); ?>

                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="col-md-12 text-right">
                   <?php /* <input type="button" id="new-shift-ajax" class="" value="<?php echo Yii::t('db','New Shift'); ?>" /> */ ?>
                        <input type="submit" class="show-profile schedule_form_btn" value="<?php echo Yii::t('db','Save'); ?>"/>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php /* if(count($addedShift)>0){ ?>
<div class="box">
    <div class="box-body">
        <div class="user-create">
            
        </div>
    </div>
</div>
<?php } */ ?>

<?php $this->registerJs("
$('#new-shift-ajax').on('click', function () {
    id=".$model->user_id."
    $.ajax({
          method: 'POST',
          url: 'ajax-new-shift',
          data: { id: id,}
    })
      .done(function( msg ) { 
        if(msg){
        $('#addFormItem').html('');
        $('#addFormItem').html(msg);
        }
      });

   
       });

", \yii\web\VIEW::POS_END); 
?>