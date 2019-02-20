<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\components\DrsPanel;

$booking=DrsPanel::getCommission($userProfile->user_id,'booking');
$cancel=DrsPanel::getCommission($userProfile->user_id,'cancel');
$reschedule=DrsPanel::getCommission($userProfile->user_id,'reschedule');
?>
<?php $form = ActiveForm::begin([
    'id' => 'update-fee-percent',
    'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]); ?>

    <div class="col-sm-12">
        <div class="form-group clearfix field-booking-fees">
            <label class="control-label">Booking Fees Percentage</label>
            <input style="display: none;" id="booking-fees" class="form-control" type="text" name="Booking[fees]"/>
            <p class="help-block help-block-error"></p>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Admin</label>
                    <input class="form-control booking_fee" type="number" name="Fees[booking][admin]" value="<?php echo $booking['admin']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Doctor</label>
                    <input class="form-control booking_fee" type="number" name="Fees[booking][user_provider]" value="<?php echo $booking['user_provider']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 list-seperate"></div>


    <div class="col-sm-12">
        <div class="form-group clearfix field-cancel-fees">
            <label class="control-label">Cancellation Fees Percentage</label>
            <input style="display: none;" id="cancel-fees" class="form-control" type="text" name="Cancel[fees]"/>
            <p class="help-block help-block-error"></p>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Admin</label>
                    <input class="form-control cancel_fee" type="number" name="Fees[cancel][admin]" value="<?php echo $cancel['admin']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Doctor</label>
                    <input class="form-control cancel_fee" type="number" name="Fees[cancel][user_provider]" value="<?php echo $cancel['user_provider']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Patient</label>
                    <input class="form-control cancel_fee" type="number" name="Fees[cancel][user_patient]" value="<?php echo $cancel['user_patient']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 list-seperate"></div>

    <div class="col-sm-12">
        <div class="form-group clearfix field-reschedule-fees">
            <label class="control-label">Reschedule Fees Percentage</label>
            <input style="display: none;" id="reschedule-fees" class="form-control" type="text" name="Reschedule[fees]"/>
            <p class="help-block help-block-error"></p>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Admin</label>
                    <input class="form-control reschedule_fee" type="number" name="Fees[reschedule][admin]" value="<?php echo $reschedule['admin']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Doctor</label>
                    <input class="form-control reschedule_fee" type="number" name="Fees[reschedule][user_provider]" value="<?php echo $reschedule['user_provider']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group clearfix">
                    <label>Patient</label>
                    <input class="form-control reschedule_fee" type="number" name="Fees[reschedule][user_patient]" value="<?php echo $reschedule['user_patient']; ?>"/>
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-md-12 text-right">
            <a href="javascript:void(0)" class="btn btn-primary submit_fee_form">Update</a>
            <input style="display:none;" type="submit" class="show-profile" value="<?php echo Yii::t('db','Update'); ?>">
        </div>
    </div>
<?php ActiveForm::end(); ?>