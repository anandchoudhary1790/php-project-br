<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  common\components\DrsPanel ;
$baseUrl= Yii::getAlias('@frontendUrl');

$getlist="'".$baseUrl."/search/booking-confirm-step2'";

?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3 >Update Reminder</span></h3>
        </div>
        <div class="modal-body" id="updatereminder">
            <div class="col-md-12 mx-auto">
                <div class="pace-part main-tow mb-0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pace-left">
                                <?php $image = DrsPanel::getUserAvator(110);?>
                                <img src="<?php  echo $image; ?>" alt="image"/>
                            </div>
                            <div class="pace-right">
                                <h4>Dr. Anand</h4>
                                <p> General Surgery</p>
                                <p> <i class="fa fa-calendar"></i> <?php echo date('d M Y'); ?> <span class="pull-right"> <strong>$100</strong></span></p>
                                <p><i class="fa fa-clock-o" aria-hidden="true"></i>  22-10-2019  </p>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="workingpart">
                    <input id="slot_date" type="hidden" value="">
                    <div class="form-group">
                        <div class="pull-left">
                            <h5> 971 Barkat nagar </h5>
                            </div>
                            <div class="pull-right">
                            <p>Kishan Marg jaipur rajasthan</p>
                        </div>
                        <div class="pull-right hide"> <a href="#" data-toggle="modal" data-target="#myModal"> 0.8 km <i class="fa fa-location-arrow"></i> </a> </div>
                    </div>

                </div>
                <div class="workingpart">
                    <div class="form-group">
                        <div class="pull-left">
                            <p> Token <span> <a href="#" class="roundone">  53 </a> </span> </p>
                        </div>
                        <div class="pull-right"> <a href="#" class="time-bg">22-12-2018- 23-12-2018 </a> </div>
                    </div>
                </div>
                <form class="appoiment-form-part mt-0 mt-nill">
                    <div class="btdetialpart mt-0" id="user_name_div">
                        <input type="text" id="date" name="date" placeholder="Name">
                    </div>
                    <div class="btdetialpart" id="user_phone_div">
                        <input type="text" id="appointment_time" name="Appointment Time" placeholder="Appointment TIME" maxlength="10">
                    </div>
                    <div class="btdetialpart">
                    <div class="pull-left">
                        <button type="button" class="confirm-theme" data-dismiss="modal">Cancel</button>                </div>
                        <div class="pull-right text-right">
                            <input type="hidden" name="slot_id" id="slot_id" value=""/>
                            <input type="hidden" name="doctor_id" id="doctor_id" value=""/>
                            <button type="button" class="confirm-theme booking_confirm_step1">Confirm Now</button>
                        </div>
                    </div>
                </form>

                <?php

                $form = ActiveForm::begin(['id' => 'reminder-form']); ?>

                <?= $form->field($reminder, 'id')->hiddenInput(['maxlength' => true])->label(false); ?>

                <?= $form->field($reminder, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>
                <?= $form->field($reminder, 'appointment_id')->hiddenInput(['maxlength' => true])->label(false); ?>
                <div class="btdetialpart mt-0" id="user_name_div">
                    Appointment Id: <?= $reminder->appointment_id; ?>
                </div>
                <div class="btdetialpart mt-0" id="user_name_div">
                    <?= $form->field($reminder, 'reminder_date')->textInput([])->widget(
                        DatePicker::className(), [
                        'convertFormat' => true,
                        'type' => DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => 'Date*','class'=>'form-group '],
                        'layout'=>'{input}',
                        'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-MM-dd',
                        'todayHighlight' => true
                        ],])->label(false); ?>
                </div>
                <div class="btdetialpart mt-0" id="user_name_div">
                    <?= $form->field($reminder, 'reminder_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Time'),'readonly'=> false,'class'=>'reminder_time_check form-control'])->label(false); ?>
                </div>
                <div class="file_row clearfix">
                    <div class="submitbtn text-center">
                        <?php echo Html::submitButton( $type, ['name' => 'add-update-reminder']) ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
