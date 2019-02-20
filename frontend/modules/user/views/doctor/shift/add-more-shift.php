<?php
 use yii\helpers\Html;
 use yii\widgets\ActiveForm;
use yii\web\View;

$this->registerJs(" 
   $('.addscheduleform-start_time').timepicker({defaultTime: '08:00 A'});
   $('.addscheduleform-end_time').timepicker({defaultTime: '12:00 P'});
", View::POS_END);
?>

<div class="bt_formpartmait shift_time_section" id="shift_count_<?php echo $shift_count + 1?>">

    <div class="edit-delete" id="edit_shift_<?php echo $shift_count + 1 ?>" >
        <a href="javascript:void(0);" class="remove_shiftbox_div"><i class="fa fa-trash" aria-hidden="true"></i></a>
    </div>

    <div class="row">

      <div class="col-md-12 mx-auto calendra_slider">
          <div class="week_sectionmain">
              <ul>
                  <?php
                  echo $form->field($model, 'weekday['.$shift_count.'][]')
                      ->checkboxList($weeks, [
                          'item' => function ($index, $label, $name, $checked, $value) {
                              $return = '<li><div class="weekDays-selector"><span>';
                              $return .= Html::checkbox($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'week_' .$name.'_'.$value,'class'=> 'weekday']);
                              $return .= '<label for="week_' .$name.'_'. $value . '" >' . Yii::t('db',ucwords($label)) . '</label>';
                              $return .= '</span></div></li>';
                              return $return;
                          }
                      ])->label(false) ?>
              </ul>
          </div>
      </div>

    <div class="col-md-6">
      <?= $form->field($model, 'start_time['.$shift_count.']')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'shift-time-check addscheduleform-start_time form-control','onchange' => 'shiftOneValue("shiftform",'.$shift_count.',"add");'])->label('From'); ?>
    </div>
    <div class="col-md-6">
      <?= $form->field($model, 'end_time['.$shift_count.']')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'shift-time-check addscheduleform-end_time form-control','onchange' => 'shiftOneValue("shiftform",'.$shift_count.',"add");'])->label('To'); ?>
    </div>
    <div class="col-md-6">
      <?php echo $form->field($model, 'appointment_time_duration['.$shift_count.']')->Input('number',['min'=>'1.00','max'=>'10.00','placeholder' => Yii::t('db','Duration (Minutes)'),'onchange' => 'maxvalidation("shiftform","appointment_time_duration",'.$shift_count.',"add");', 'readonly'=>false])->label('Duration'); ?>
    </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'patient_limit['.$shift_count.']')->Input('number',['min'=>'1','max'=>'10.00','placeholder' => Yii::t('db','Patient Limit'),'onchange' => 'patientcount("shiftform","patient_limit",'.$shift_count.',"add");', 'readonly'=>false])->label('Patient Limit'); ?>
        </div>

    <div class="col-md-6">
      <?php echo $form->field($model, 'consultation_fees['.$shift_count.']')->Input('number',['min'=>'0.00','placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shiftform","consultation_fees",'.$shift_count.',this.value,"add");','readonly'=>false]); ?>
    </div>
    <div class="col-md-6">
      <?php echo $form->field($model, 'emergency_fees['.$shift_count.']')->Input('number',['min'=>'0.00','placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shiftform","emergency_fees",'.$shift_count.',this.value,"add");','readonly'=>false]); ?>
    </div>
      <div class="col-md-6">
          <?php echo $form->field($model, 'consultation_fees_discount['.$shift_count.']')->Input('number',['min'=>'1.00','max'=>'10.00','placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shiftform","consultation_fees_discount",'.$shift_count.',"add");','readonly'=>false]); ?>
      </div>
      <div class="col-md-6">
          <?php echo $form->field($model, 'emergency_fees_discount['.$shift_count.']')->Input('number',['min'=>'1.00','max'=>'10.00','placeholder' => Yii::t('db','Discounted Emergency Fee'), 'onchange' => 'maxvalidation("shiftform","emergency_fees_discount",'.$shift_count.',"add");','readonly'=>false]); ?>
      </div>
    <div class="clearfix"></div>
    <div class="remove_shiftbox hide"><a href="#"><i class="fa fa-minus"></i></a></div>
  </div>
</div>
