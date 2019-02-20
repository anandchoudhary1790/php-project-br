<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$base_url= Yii::getAlias('@frontendUrl');
$action_url = '/attender/shift-update';
?>
<!-- Modal -->
<?php $form = ActiveForm::begin(['id'=>'shift-update-form',
    'action'=>$base_url.$action_url,'enableAjaxValidation'=>true,
    'options' => ['enctype'=> 'multipart/form-data']]); ?>
    <div class="row accordion-gradient-bcg d-flex justify-content-center">
        <?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'user_id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'shift_belongs_to')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'attender_id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'hospital_id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'address_id')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'weekday')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'consultation_days')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'emergency_days')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'is_edit')->hiddenInput()->label(false); ?>
        <?php echo $form->field($model,'status')->hiddenInput()->label(false); ?>
        <input type="hidden" value="<?php echo $date?>" name="date_dayschedule" id="date_dayschedule"/>
        <input type="hidden" value="<?php echo $schedule_id?>" name="schedule_id" id="schedule_id" />

        <div class="row">
            <div class="col-md-12">
                <div class="col-lg-6 col-sm-12">
                    <?= $form->field($model, 'start_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'form-control editscheduleform-start_time','onchange' => 'shiftOneValue("shift-update-form",0,"today_timing");'])->label('From'); ?>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <?= $form->field($model, 'end_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','End'),'readonly'=> false,'class'=>'form-control editscheduleform-end_time','onchange' => 'shiftOneValue("shift-update-form",0,"today_timing");'])->label('To'); ?>
                </div>
            </div>

            <div class="col-md-12">
                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'appointment_time_duration')->Input('number',['min'=>'1.00','max'=>'240','value'=>$model->appointment_time_duration,'placeholder' => Yii::t('db','Duration (Minutes)'), 'class'=>'form-control editscheduleform-appointment_time_duration','onchange' => 'maxvalidation("shift-update-form","appointment_time_duration",0,"today_timing");','readonly'=>false])->label('Duration'); ?>
                </div>
                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'patient_limit')->Input('number',['min'=>'1.00','max'=>'240','value'=>$model->patient_limit,'placeholder' => Yii::t('db','Patient Limit'), 'class'=>'form-control editscheduleform-patient_limit','onchange' => 'patientcount("shift-update-form","patient_limit",0,"today_timing");','readonly'=>false])->label('Patient Limit'); ?>

                </div>
            </div>

            <div class="col-md-12">
                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'consultation_fees')->Input('number',['min'=>'0.00','value'=>$model->consultation_fees,'placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shift-update-form","consultation_fees",0,this.value,"today_timing");', 'readonly'=>false,'class'=>'form-control editscheduleform-consultation_fees']); ?>

                </div>

                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'emergency_fees')->Input('number',['min'=>'0.00','value'=>$model->emergency_fees,'placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shift-update-form","emergency_fees",0,this.value,"today_timing");', 'readonly'=>false,'class'=>'form-control editscheduleform-emergency_fees']); ?>

                </div>
            </div>

            <div class="col-md-12">
                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'consultation_fees_discount')->Input('number',['max'=>$model->consultation_fees,'value'=>($model->consultation_fees_discount > 0)?$model->consultation_fees_discount:'','placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shift-update-form","consultation_fees_discount",0,"today_timing");','readonly'=>false,'class'=>'form-control editscheduleform-consultation_fees_discount']); ?>
                </div>

                <div class="col-lg-6 col-sm-12">
                    <?php echo $form->field($model, 'emergency_fees_discount')->Input('number',['max'=>$model->emergency_fees,'value'=>($model->emergency_fees_discount > 0)?$model->emergency_fees_discount:'','placeholder' => Yii::t('db','Discounted Emergency Fee'), 'onchange' => 'maxvalidation("shift-update-form","emergency_fees_discount",0,"today_timing");','readonly'=>false,'class'=>'form-control editscheduleform-emergency_fees_discount']); ?>
                </div>
            </div>
        </div>

        <div class="form-group shift_time_update_btn">
            <?= Html::submitButton(Yii::t('frontend', 'Save'), ['name' => 'attender-save','class' => 'login-sumbit schedule_today_edit']) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>