 <?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
 use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
$base_url= Yii::getAlias('@frontendUrl'); 
$action_url = '/doctor/shift-update';
?>
 <!-- Modal -->

   <div class="row accordion-gradient-bcg d-flex justify-content-center">
    <div class="col-sm-12">
      <div class="card">
        <div id="collapse1" class="collapse show" role="tabpanel" aria-labelledby="heading1" data-parent="#accordionEx7">
          <div class="card-body mb-1 rgba-grey-light white-text">
            <?php $form = ActiveForm::begin(['id'=>'shift-update-form','action'=>$base_url.$action_url,'enableAjaxValidation'=>true,'options' => ['enctype'=> 'multipart/form-data']]); ?>
            <?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
            <div class="form-group">
              <?= $form->field($model, 'start_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'form-control editscheduleform-start_time'])->label(false); ?>
            </div>
            <div class="form-group">
              <?= $form->field($model, 'end_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','End'),'readonly'=> false,'class'=>'form-control editscheduleform-end_time'])->label(false); ?>
            </div> 
            <div class="form-group">
              <?php  echo  $form->field($modelAddress, 'address')->dropDownList($address_name,['class' =>'selectpicker form-control','readonly'=>true])->label(false) ?>
            </div>

            <div class="form-group">
              <?php echo $form->field($model, 'patient_limit')->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label(false); ?>
            </div>
            <div class="form-group">
             <!--  <button type="button" class="btn grey_btn">Save now</button> -->
              <?= Html::submitButton(Yii::t('frontend', 'Save'), ['name' => 'attender-save','class' => 'login-sumbit']) ?>
            </div>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
        