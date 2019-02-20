<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\UserProfile;
$citiesList=[];
$base_url= Yii::getAlias('@frontendUrl');
$this->title = 'Drspanel :: Add Shift';

if($modelAddress->state)
  $citiesList=ArrayHelper::map(DrsPanel::getCitiesList($modelAddress->state,'name'),'name','name');
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
$idarray=array('Hospital'=>'Hospital','Clinic'=>'Clinic');
$frontend=Yii::getAlias('@frontendUrl');
$cityUrl="'".$frontend."/doctor/city-list'";
$addmoreshift="'".$frontend."/doctor/add-more-shift'";

$js="
var ShiftCount = 0;
$('#estate_list').on('change', function () { 
  $.ajax({
    method:'POST',
    url: $cityUrl,
    data: {state_id:$(this).val()}
  })
  .done(function( msg ) { 

    $('#ecity_list').html('');
    $('#ecity_list').html(msg);

  });
}); 

$('.add_siftbox').click(function(){
  var numItems = $('.shift_time_section').length;
  $.ajax({
    type: 'POST',
    url: $addmoreshift,
    data:{shiftcount:numItems},
    success: function(data) {
      ShiftCount = ShiftCount+1;
      $('.add_more_shift').append(data);
      addValidationRules('shiftform',ShiftCount,'add');

    }
  });



  });
 

";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="inner-banner"> </div>

<section class="mid-content-part">

    <div class="container">
      <div class="row">
          <div class="col-md-12">
       <?php $form = ActiveForm::begin(['id' => 'shiftform','options' => ['enctype'=> 'multipart/form-data','enableAjaxValidation' => true]]); ?>
        <div class="col-md-12 mx-auto">
          <div class="main_usrimgbox">
            <h2 class="track_headline">Add Shift</h2>
          </div>
          <div class="row">
            <div class="col-md-6">
                <?= $form->field($modelAddress, 'name')->textInput(['placeholder' => 'Hospital/Clinic Name'])->label(false) ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($modelAddress, 'state')->dropDownList($statesList,['id'=>'estate_list','prompt' => 'Select State'])->label(false) ?>
            </div>
            <div class="col-md-6">
              <?= $form->field($modelAddress, 'city')->dropDownList($citiesList,['id'=>'ecity_list','prompt' => 'Select City'])->label(false) ?>
            </div>
              <div class="col-md-6">
                  <?= $form->field($modelAddress, 'address')->textInput(['placeholder' => 'Address'])->label(false) ?>
              </div>
              <div class="col-md-6">
                  <?= $form->field($modelAddress, 'area')->textInput(['placeholder' => 'Area/Colony'])->label(false) ?>
              </div>
            <div class="col-md-3">
            <?= $form->field($modelAddress, 'phone')->textInput(['placeholder' => 'Phone','maxlength'=> 10])->label(false) ?>
            </div>
            <div class="col-md-3">
            <?= $form->field($modelAddress, 'landline')->textInput(['placeholder' => 'Landline','maxlength'=> 12])->label(false) ?>
            </div>
           
            <div class="col-md-12 address_attachment">
              <div class="file_area">
                <div class="attachfile_area"> 
                  <?php 
                  echo  $form->field($userAdddressImages, 'image[]')->fileInput([
                    'options' => ['accept' => 'image/*'],
                    'multiple' => true,
                    ])->label(false);   
                    ?>
                </div>
              </div>
                <span class="attachfile address_attachment_upload"><i aria-hidden="true" class="fa fa-paperclip"></i> Attach file </span>
                <div class="address_gallery"></div>
            </div>
            <div class="clearfix"></div>
          </div>
          </div>

              

          <div class="ct_newpart">
              <div class="col-md-12 add_more_shift">
                <?php if(isset($postData['AddScheduleForm']) && !empty($postData['AddScheduleForm'])) {
                   $shiftcount = count($postData['AddScheduleForm']['start_time']);
                   for($i = 0; $i<$shiftcount;$i++) { ?>
                    <div class="bt_formpartmait shift_time_section" id="shift_count_<?php echo $i + 1 ?>">
                      <div class="edit-delete" style="<?php echo ($i== 0)?'display:none;':'' ?>" id="edit_shift_<?php echo $i + 1 ?>" >
                        <a href="#"><i class="fa fa-minus-square" aria-hidden="true"></i></a>
                      </div>
                      <div class="row">
                        <div class="col-md-12 mx-auto calendra_slider">
                          <div class="week_sectionmain">
                            <ul>
                              <?php
                               $model->weekday[$i]=$postData['AddScheduleForm']['weekday'][$i];
                              echo $form->field($model, 'weekday['.$i.'][]')
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
                            <?= $form->field($model, 'start_time['.$i.']')->textInput(['value'=>$postData['AddScheduleForm']['start_time'][$i],'autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'shift-time-check addscheduleform-start_time form-control','onchange' => 'shiftOneValue("shiftform",'.$i.',"add");'])->label('From'); ?>
                          </div>
                          <div class="col-md-6">
                            <?= $form->field($model, 'end_time['.$i.']')->textInput(['value'=>$postData['AddScheduleForm']['end_time'][$i],'autocomplete'=>'off','placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'shift-time-check addscheduleform-end_time form-control','onchange' => 'shiftOneValue("shiftform",'.$i.',"add");'])->label('To'); ?>
                          </div>
                          <div class="col-md-6">
                            <?php echo $form->field($model, 'appointment_time_duration['.$i.']')->Input('number',['min'=>'1.00','max'=>'240','value'=>$postData['AddScheduleForm']['appointment_time_duration'][$i],'placeholder' => Yii::t('db','Duration (Minutes)'), 'onchange' => 'maxvalidation("shiftform","appointment_time_duration",'.$i.',"add");', 'readonly'=>false])->label('Duration'); ?>
                          </div>
                          <div class="col-md-6">
                              <?php echo $form->field($model, 'patient_limit['.$i.']')->Input('number',['min'=>'1.00','max'=>'240','value'=>$postData['AddScheduleForm']['patient_limit'][$i],'placeholder' => Yii::t('db','Patient Limit'), 'onchange' => 'patientcount("shiftform","patient_limit",'.$i.',"add");', 'readonly'=>false])->label('Patient Limit'); ?>


                          </div>
                          <div class="col-md-6">

                              <?php echo $form->field($model, 'consultation_fees['.$i.']')->Input('number',['min'=>'0.00','value'=>$postData['AddScheduleForm']['consultation_fees'][$i],'placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shiftform","consultation_fees",'.$i.',this.value,"add");','readonly'=>false]); ?>


                          </div>
                          <div class="col-md-6">

                              <?php echo $form->field($model, 'emergency_fees['.$i.']')->Input('number',['min'=>'0.00','value'=>$postData['AddScheduleForm']['emergency_fees'][$i],'placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shiftform","emergency_fees",'.$i.',this.value,"add");','readonly'=>false]); ?>

                          </div>
                          <div class="col-md-6">

                              <?php echo $form->field($model, 'consultation_fees_discount['.$i.']')->Input('number',['min'=>'1.00','max'=>($postData['AddScheduleForm']['consultation_fees'][$i] - 1),'value'=>$postData['AddScheduleForm']['consultation_fees_discount'][$i],'placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shiftform","consultation_fees_discount",'.$i.',"add");', 'readonly'=>false]); ?>

                          </div>

                          <div class="col-md-6">
                              <?php echo $form->field($model, 'emergency_fees_discount['.$i.']')->Input('number',['min'=>'1.00','max'=>($postData['AddScheduleForm']['emergency_fees'][$i] - 1),'value'=>$postData['AddScheduleForm']['emergency_fees_discount'][$i],'placeholder' => Yii::t('db','Discounted Emergency Fee'),'onchange' => 'maxvalidation("shiftform","emergency_fees_discount",'.$i.',"add");','readonly'=>false]); ?>

                          </div>
                          <div class="clearfix"></div>
                          <input type="hidden" name="shift_key[]" value="<?= $i; ?>"/>
                        </div>
                      </div> <?php  ?>
                      <?php 
                    
                    }
                    }
                    else{ ?>
                        <div class="bt_formpartmait shift_time_section" id="shift_count_1">

                            <div class="edit-delete shiftDelete" id="edit_shift_1" style="display:none;">
                                <a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
                            </div>

                            <div class="row">

                                <div class="col-md-12 mx-auto calendra_slider">
                                    <div class="week_sectionmain">
                                        <ul>
                                            <?php
                                            echo $form->field($model, 'weekday[0][]')
                                                ->checkboxList($weeks, [
                                                    'item' => function ($index, $label, $name, $checked, $value) {
                                                        $return = '<li><div class="weekDays-selector"><span>';
                                                        $return .= Html::checkbox($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'week_' .$name.'_'.$value,'class'=> "weekday"]);
                                                        $return .= '<label for="week_' .$name.'_'. $value . '" >' . Yii::t('db',ucwords($label)) . '</label>';
                                                        $return .= '</span></div></li>';
                                                        return $return;
                                                    }
                                                ])->label(false) ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <?= $form->field($model, 'start_time[]')->textInput(['value'=>$model->start_time,'autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'shift-time-check addscheduleform-start_time form-control','onchange' => 'shiftOneValue("shiftform",0,"add");'])->label('From'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?= $form->field($model, 'end_time[]')->textInput(['value'=>$model->end_time,'autocomplete'=>'off','placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'shift-time-check addscheduleform-end_time form-control','onchange' => 'shiftOneValue("shiftform",0,"add");'])->label('To'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'appointment_time_duration[]')->Input('number',['min'=>'1.00','max'=>'240','value'=>$model->appointment_time_duration,'placeholder' => Yii::t('db','Duration (Minutes)'), 'onchange' => 'maxvalidation("shiftform","appointment_time_duration",0,"add");','readonly'=>false])->label('Duration'); ?>
                                </div>

                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'patient_limit[]')->Input('number',['min'=>'1.00','max'=>'240','value'=>$model->patient_limit,'placeholder' => Yii::t('db','Patient Limit'), 'onchange' => 'patientcount("shiftform","patient_limit",0,"add");','readonly'=>false])->label('Patient Limit'); ?>
                                </div>

                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'consultation_fees[]')->Input('number',['min'=>'0.00','value'=>$model->consultation_fees,'placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shiftform","consultation_fees",0,this.value,"add");', 'readonly'=>false]); ?>
                                </div>

                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'emergency_fees[]')->Input('number',['min'=>'0.00','value'=>$model->emergency_fees,'placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shiftform","emergency_fees",0,this.value,"add");', 'readonly'=>false]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'consultation_fees_discount[]')->Input('number',['min'=>'1.00','max'=>'10.00','value'=>$model->consultation_fees_discount,'placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shiftform","consultation_fees_discount",0,"add");','readonly'=>false]); ?>
                                </div>
                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'emergency_fees_discount[]')->Input('number',['min'=>'1.00','max'=>'10.00','value'=>$model->emergency_fees_discount,'placeholder' => Yii::t('db','Discounted Emergency Fee'),'onchange' => 'maxvalidation("shiftform","emergency_fees_discount",0,"add");','readonly'=>false]); ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    <?php }?>
              </div>
              <div class="col-md-12">
                  <div class="add_siftbox">
                      <a href="javascript:void(0)"><i class="fa fa-plus"></i> Add More Shifts</a>
                  </div>
              </div>
            <div class="col-md-12">
              <div class="bookappoiment-btn" style="margin:0px;">
                <?php echo Html::submitButton(Yii::t('frontend', 'Save Shift'), ['id'=>'profile_from','class' => 'login-sumbit schedule_form', 'name' => 'profile-button']) ?>
              </div>
            </div>
          </div>
          <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
</section>



