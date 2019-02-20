<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$form = ActiveForm::begin([
    'action'=>['add-appointments','id'=>$model->id],
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
        $addAppointment->date=$date;
    }
    ?>
    <div class="form-group">
        <?= $form->field($addAppointment, 'date')->textInput()->widget(
            DatePicker::className(), [
            'convertFormat' => true,
            'options' => ['placeholder' => 'Shift Date*'],
            'layout'=>'{input}{picker}',
            'pluginOptions' => [
                'autoclose'=>true,
                'format' => 'yyyy-MM-dd',
                'startDate' => date('Y-m-d'),
            ],])->label('Select Date'); ?>
    </div>
</div>

<div class="col-sm-12">
    <?php
    if($shifts_available == 1){ ?>


        <div class="row radio_btns">
            <div class="col-sm-12"><label>Select Shift</label></div>

            <?php
            echo $form->field($addAppointment, 'slot', ['options' => ['class' =>
                'col-sm-12']])->radioList($keys_avail, [
                'item' => function ($index, $label, $name, $checked, $value) {

                    $return = '<span>';
                    $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'slot_' . $label]);
                    $return .= '<label for="slot_' . $label . '" >' . ucwords($label) . '</label>';
                    $return .= '</span>';

                    return $return;
                }
            ])->label(false)
            ?>
        </div>


        <?= $form->field($addAppointment, 'name')->textInput()->label('Full Name') ?>

        <?= $form->field($addAppointment, 'age')->textInput()->label('Age') ?>

        <?= $form->field($addAppointment, 'phone')->textInput()->label('Mobile/Phone no.') ?>

        <?= $form->field($addAppointment, 'address')->textInput()->label('Address.') ?>


        <div class="row radio_btns">
            <div class="col-sm-12"><label>Gender</label></div>

            <?php
            echo $form->field($addAppointment, 'gender', ['options' => ['class' =>
                'col-sm-12']])->radioList(['1' => 'Male', '2' => 'Female'], [
                'item' => function ($index, $label, $name, $checked, $value) {

                    $return = '<span>';
                    $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
                    $return .= '<label for="gender_' . $label . '" >' . ucwords($label) . '</label>';
                    $return .= '</span>';

                    return $return;
                }
            ])->label(false)
            ?>
        </div>

        <div class="row radio_btns">
            <div class="col-sm-12"><label>Payment Method</label></div>

            <?php
            echo $form->field($addAppointment, 'payment_type', ['options' => ['class' =>
                'col-sm-12']])->radioList(['cash' => 'Cash', 'already_paid' => 'Already Paid'], [
                'item' => function ($index, $label, $name, $checked, $value) {

                    $return = '<span>';
                    $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'payment_type_' . $label]);
                    $return .= '<label for="payment_type_' . $label . '" >' . ucwords($label) . '</label>';
                    $return .= '</span>';

                    return $return;
                }
            ])->label(false)
            ?>
        </div>

        <div class="form-group clearfix">
            <div class="col-md-12 text-right">
                <input type="submit" class="show-profile schedule_form_btn" value="<?php echo Yii::t('db','Save'); ?>">
            </div>
        </div>


    <?php }
    else{
        echo "No Shifts for selected date.";
    } ?>




</div>

<?php ActiveForm::end(); ?>

