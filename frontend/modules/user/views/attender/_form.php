<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

$doctorslist = array();
if(!empty($doctor_lists)){
    foreach($doctor_lists as $doctor_list){
        $doctorslist[$doctor_list['user_id']]=$doctor_list['name'];
    }
}
?>
<div class="row">
    <div class="col-sm-6">
        <?php echo $form->field($model, 'name') ?>
    </div>
    <div class="col-sm-6">
        <?php echo $form->field($model, 'email') ?>
    </div>

</div>
<div class="row">
    <div class="col-sm-12" >
        <?php echo  $form->field($model, 'shift_id')->widget(Select2::classname(), 
            [
            'data' => $doctorslist,
            'size' => Select2::MEDIUM,
            'options' => ['placeholder' => '', 'multiple' => true],
            'pluginOptions' => [
            'allowClear' => true
            ],
            ])->label('Select Multiple Doctors'); ?>
        </div>
    </div>
    <div class="row">

        <div class="col-sm-12">

            <?php echo $form->field($model, 'phone') ?>

        </div>

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="file btn btn-lg ">
              <span style="text-align: right;float: left;padding-right: 17px;">Upload Prifile Image</span>
              <?= $form->field($model, 'avatar')->fileInput([
                'options' => ['accept' => 'image/*'],
                'maxFileSize' => 5000000
                ])->label(false);   ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Save'), ['name' => 'attender-save','class' => 'login-sumbit']) ?>
    </div>

