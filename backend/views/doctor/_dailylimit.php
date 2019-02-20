<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$form = ActiveForm::begin([
    'action'=>['daily-patient-limit','id'=>$userShift->user_id],
    'id' => 'schedule-form-new',
    'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
    'options' => [
        'enctype' => 'multipart/form-data',
        'class' => 'schedule-form',
    ],
]);  ?>

    <div class="col-sm-12">
        <?php
        if(isset($date)){
            $userShift->date=$date;
        }
        ?>
        <div class="form-group">
            <?= $form->field($userShift, 'date')->textInput()->widget(
                DatePicker::className(), [
                'convertFormat' => true,
                'options' => ['placeholder' => 'Shift Date*'],
                'layout'=>'{input}{picker}',
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-MM-dd',
                    'startDate' => date('Y-m-d'),
                ],]); ?>
        </div>
    </div>

    <div class="col-sm-12 section_top">

        <div class="form-group">
                    <span>
                        <?php
                        echo $form->field($userShift, 'shift_one',['template'=>'<div class="checkbox-btn">{input}{label}<div class="help-block"></div></div>'])->checkbox(['label' => null,'class'=>'add_shift_list','value'=>"morning"])->label('Add Morning Shift'); ?>
                    </span>
        </div>

        <div class="form-group clearfix">
            <?= $form->field($userShift, 'shift_one_start',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Start'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_one");', 'readonly'=> false,'class'=>'addscheduleform-shift_one_start form-control'])->label('From'); ?>
            <?= $form->field($userShift, 'shift_one_end',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','To'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_one");', 'readonly'=>false,'class'=>'addscheduleform-shift_one_end form-control'])->label('To'); ?>
            <?php echo $form->field($userShift, 'shift_one_address',['options'=>['class'=>
                'col-sm-6']])->dropDownList($listaddress)->label('Hospital/Clininc'); ?>
            <?php echo $form->field($userShift, 'shift_one_patient',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label('Patient Limit'); ?>
            <?php echo $form->field($userShift, 'shift_one_cfees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Consultancy Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_one_cdays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_one_efees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Emergency Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_one_edays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
        </div>
    </div>

    <div class="col-sm-12 section_top">
        <div class="form-group">
                    <span>
                        <?php
                        echo $form->field($userShift, 'shift_two',['template'=>'<div class="checkbox-btn">{input}{label}<div class="help-block"></div></div>'])->checkbox(['label' => null,'class'=>'add_shift_list','value'=>"afternoon"])->label('Add Afternoon Shift'); ?>
                    </span>
        </div>

        <div class="form-group clearfix">

            <?= $form->field($userShift, 'shift_two_start',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Start'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_two");', 'readonly'=>false,'class'=>'addscheduleform-shift_two_start form-control'])->label('From'); ?>
            <?= $form->field($userShift, 'shift_two_end',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','To'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_two");', 'readonly'=>false,'class'=>'addscheduleform-shift_two_end form-control'])->label('To'); ?>

            <?php echo $form->field($userShift, 'shift_two_address',['options'=>['class'=>
                'col-sm-6']])->dropDownList($listaddress)->label('Hospital/Clininc'); ?>

            <?php echo $form->field($userShift, 'shift_two_patient',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label('Patient Limit'); ?>

            <?php echo $form->field($userShift, 'shift_two_cfees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Consultancy Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_two_cdays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_two_efees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Emergency Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_two_edays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
        </div>
    </div>

    <div class="col-sm-12 section_top">
        <div class="form-group">
                    <span>
                        <?php
                        echo $form->field($userShift, 'shift_three',['template'=>'<div class="checkbox-btn">{input}{label}<div class="help-block"></div></div>'])->checkbox(['label' => null,'class'=>'add_shift_list','value'=>"evening"])->label('Add Evening Shift'); ?>
                    </span>
        </div>
        <div class="form-group clearfix">


            <?= $form->field($userShift, 'shift_three_start',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Start'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_three");', 'readonly'=>false,'class'=>'addscheduleform-shift_three_start form-control'])->label('From'); ?>
            <?= $form->field($userShift, 'shift_three_end',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','To'), 'onchange' => 'shiftOneValue("schedule-form-new","shift_three");', 'readonly'=>false,'class'=>'addscheduleform-shift_three_end form-control'])->label('To'); ?>

            <?php echo $form->field($userShift, 'shift_three_address',['options'=>['class'=>
                'col-sm-6']])->dropDownList($listaddress)->label('Hospital/Clininc'); ?>

            <?php echo $form->field($userShift, 'shift_three_patient',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Patient Limit'), 'readonly'=>false])->label('Patient Limit'); ?>

            <?php echo $form->field($userShift, 'shift_three_cfees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Consultancy Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_three_cdays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_three_efees',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Emergency Fees'), 'readonly'=>false]); ?>
            <?php echo $form->field($userShift, 'shift_three_edays',['options'=>['class'=>
                'col-sm-6']])->textInput(['placeholder' => Yii::t('db','Valid Days'), 'readonly'=>false]); ?>
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-md-12 text-right">
            <input type="submit" class="show-profile schedule_form_btn" value="<?php echo Yii::t('db','Save'); ?>">
        </div>
    </div>

<?php ActiveForm::end(); ?>