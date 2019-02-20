<?php
$baseUrl=Yii::getAlias('@frontendUrl');
use common\components\DrsPanel;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\Html;
/*echo '<pre>';
print_r($doctorData);die;*/
?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h3 ><?php echo $type ?> Reminder</span></h3>
        </div>
        <div class="modal-body" id="updatereminder">
            <div class="col-md-12 mx-auto">
                <div class="pace-part main-tow mb-0">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="pace-left">
                                <?php $image = DrsPanel::getUserAvator(isset($doctorData['doctor_id'])?$doctorData['doctor_id']:'');?>
                                <img src="<?php  echo $image; ?>" alt="image"/>
                            </div>
                            <div class="pace-right">
                                <h4><?php echo $doctorData['doctor_name']?></h4>
                                <p> <?php echo $doctorData['doctor_speciality'] ?></p>
                                <ul class="doctor_reminder doctor_reminder_edit">
                          <li>Date</li><li><?= $doctorData['appointment_date'];?></li>
                                              
                      </ul>
                               <?php /* ?> <p> <i class="fa fa-calendar"></i> <?php echo $doctorData['reminder_date']?> <span class="pull-right"> <strong>$<?php echo $doctorData['doctor_fees'] ?></strong></span></p>
                                <p><i class="fa fa-clock-o" aria-hidden="true"></i>  <?php echo $reminder->reminder_date?> </p> */?>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="workingpart hide">
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
                            <p> Token <span> <a href="#" class="roundone">  <?php echo $doctorData['token'];?> </a> </span> </p>
                        </div>
                        <div class="pull-right"> <a href="#" class="time-bg"><?php echo $doctorData['appointment_time']?> </a> </div>
                    </div>
                </div>
                <?php $form = ActiveForm::begin(['id' => 'reminder-form']); ?>

                <?= $form->field($reminder, 'id')->hiddenInput(['maxlength' => true])->label(false); ?>

                <?= $form->field($reminder, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>
                <?= $form->field($reminder, 'appointment_id')->hiddenInput(['maxlength' => true])->label(false); ?>
                    <div class="workingpart">
                        <div class="form-group">
                            <div class="pull-left">
                                <p> Booking Id </p>
                            </div>
                            <div class="pull-right">
                                <p><span> <a href="#" >  <?php echo isset($doctorData['booking_id'])?$doctorData['booking_id']:''; ?> </a> </span> </p>
                            </div>
                        </div>
                    </div>
                    <div class="btdetialpart" id="user_phone_div">
                       <?= $form->field($reminder, 'reminder_date')->textInput([])->widget(
                        DatePicker::className(), [
                        'convertFormat' => true,
                        'type' => DatePicker::TYPE_INPUT,
                        'options' => ['placeholder' => 'Date*','class'=>'form-group '],
                        'layout'=>'{input}',
                        'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'yyyy-MM-dd',
                        'endDate' => $doctorData['appointment_date'],
                        'todayHighlight' => true
                        ],])->label(false); ?>
                    </div>
                    <div class="btdetialpart">
                    <?= $form->field($reminder, 'reminder_time')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Time'),'readonly'=> false,'class'=>'reminder_time_check form-control'])->label(false); ?>
                    </div>
                    <div  style="padding-top: 15px"></div>
                    <div class="btdetialpart">
                        <div class="submitbtn pull-left reminder_add">
                            <?php echo Html::submitButton( $type, ['name' => 'add-update-reminder','class' => 'confirm-theme',]) ?>
                        </div>
                        <div class="pull-right reminder_cancel">
                                <a href="javascript:void(0)" class="confirm-theme" data-dismiss="modal">Cancel</a>
                        </div>
                    </div>
            </div>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
