<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\UserProfile;
$citiesList=[];
$base_url= Yii::getAlias('@frontendUrl');
$this->title = 'Drspanel :: Edit Shift';


if($modelAddress->state)
    $citiesList=ArrayHelper::map(DrsPanel::getCitiesList($modelAddress->state,'name'),'name','name');
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
$frontend=Yii::getAlias('@frontendUrl');

$cityUrl="'".$frontend."/doctor/city-list'";
$addmoreshift="'".$frontend."/doctor/add-more-shift'";

$js="
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
            $('.add_more_shift').append(data);
            addValidationRules('shiftform',numItems,'edit');
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
                        <h2 class="track_headline">Edit Shift</h2>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelAddress, 'name')->textInput(
                                    ['placeholder' => 'Hospital/Clinic Name','readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelAddress, 'state')->dropDownList($statesList,['id'=>'estate_list','prompt' => 'Select State','readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelAddress, 'city')->dropDownList($citiesList,['id'=>'ecity_list','prompt' => 'Select City','readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelAddress, 'address')->textInput(['placeholder' => 'Address','readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelAddress, 'area')->textInput(['placeholder' => 'Area/Colony','readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelAddress, 'phone')->textInput(['placeholder' => 'Phone','maxlength'=> 10,'readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div> 
                        <div class="col-md-3">
                            <?= $form->field($modelAddress, 'landline')->textInput(['placeholder' => 'Landline','maxlength'=> 12,'readOnly'=>($disable_field == 0)?false:true])->label(false) ?>
                        </div>
                        <?= $form->field($modelAddress, 'id')->hiddenInput()->label(false) ?>

                        <div class="col-md-12 address_attachment">
                            <?php if($disable_field == 0) { ?>
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
                            <?php } ?>
                            <?php if(!empty($addressImages)) { ?>

                                        <div class="address_gallery gallary_images">
                                            <?php foreach($addressImages as $addressImage) { ?>
                                            <?php $image_url=$addressImage->image_base_url.$addressImage->image_path.$addressImage->image; ?>
                                                <div class="address_img_attac">
                                                    <img class="imageThumb" src="<?= $image_url?>" title="<?= $addressImage->image; ?>">
                                                    <span class="remove remove_address_image" id="<?php echo 'image_'.$addressImage->id?>"><i class="fa fa-trash"></i></span>
                                                </div>
                                            <?php } ?>
                                        </div>

                            <?php }  else { ?>
                                <div class="address_gallery"></div>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="ct_newpart">
                    <div class="col-md-12 add_more_shift">
                        <?php 
                        if(count($shifts) > 0) {
                            $s=0;
                            foreach($shifts as $keys=> $shift) { ?>
                                <div class="bt_formpartmait shift_time_section" id="shift_count_<?php echo $s + 1 ?>">
                                    <div class="edit-delete" style="<?php echo ($s == 0)?'display:none;':'' ?>" id="edit_shift_<?php echo $s + 1 ?>" >
                                        <a href="#"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mx-auto calendra_slider">
                                            <div class="week_sectionmain">
                                                <ul>
                                                    <?php
                                                    $model->weekday[$s]=$shift['shifts'];
                                                    echo $form->field($model, 'weekday['.$s.'][]')
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
                                                <?php 
                                                if(!empty($shift['shifts_days_id']))
                                                {
                                                    foreach ($shift['shifts_days_id'] as $key => $shiftidValue) {

                                                        ?>
                                                        <input type="hidden" name="shift_ids[<?php echo $keys; ?>][<?php echo $key?>]" value="<?php echo $shiftidValue?>"/>
                                                        <?php 
                                                    }
                                                }
                                                 ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <?= $form->field($model, 'start_time['.$s.']')->textInput(['value'=>$shift['start_time'],'autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'shift-time-check addscheduleform-start_time form-control','onchange' => 'shiftOneValue("shiftform",'.$s.',"edit");'])->label('From'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'end_time['.$s.']')->textInput(['value'=>$shift['end_time'],'autocomplete'=>'off','placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'shift-time-check addscheduleform-end_time form-control','onchange' => 'shiftOneValue("shiftform",'.$s.',"edit");'])->label('To'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'appointment_time_duration['.$s.']')->Input('number',['min'=>'1.00','max'=>'10.00','value'=>$shift['appointment_time_duration'],'placeholder' => Yii::t('db','Duration (Minutes)'),'onchange' => 'maxvalidation("shiftform","appointment_time_duration",'.$s.',"edit");', 'readonly'=>false])->label('Duration'); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'patient_limit['.$s.']')->Input('number',['min'=>'1.00','max'=>'10.00','value'=>$shift['patient_limit'],'placeholder' => Yii::t('db','Patient Limit'),'onchange' => 'patientcount("shiftform","patient_limit",'.$s.',"edit");', 'readonly'=>false])->label('Patient Limit'); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'consultation_fees['.$s.']')->Input('number',['min'=>'0.00','value'=>$shift['consultation_fees'],'placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shiftform","consultation_fees",'.$s.',this.value,"edit");','readonly'=>false]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'emergency_fees['.$s.']')->Input('number',['min'=>'0.00','value'=>$shift['emergency_fees'],'placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shiftform","emergency_fees",'.$s.',this.value,"edit");','readonly'=>false]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'consultation_fees_discount['.$s.']')->Input('number',['min'=>'1.00','max'=>($shift['consultation_fees'] - 1),'value'=>$shift['consultation_fees_discount'],'placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shiftform","consultation_fees_discount",'.$s.',"edit");', 'readonly'=>false]); ?>

                                        </div>
                                        <div class="col-md-6">
                                            <?php echo $form->field($model, 'emergency_fees_discount['.$s.']')->Input('number',['min'=>'1.00','max'=>($shift['emergency_fees'] - 1),'value'=>$shift['emergency_fees_discount'],'placeholder' => Yii::t('db','Discounted Emergency Fee'), 'onchange' => 'maxvalidation("shiftform","emergency_fees_discount",'.$s.',"edit");', 'readonly'=>false]); ?>
                                        </div>
                                        <div class="clearfix"></div>
                                        <input type="hidden" name="AddScheduleForm[id]" value="<?php echo $shift['shift_id']?>"/>
                                           
                                    </div>
                                </div>
                            <?php $s++; }
                        }
                        else{ ?>
                            <div class="bt_formpartmait shift_time_section" id="shift_count_1">
                                <div class="edit-delete" id="edit_shift_1" style="display:none;">
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
                                        <?= $form->field($model, 'start_time[]')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','Start'),'readonly'=> false,'class'=>'shift-time-check addscheduleform-start_time form-control','onchange' => 'shiftOneValue("shiftform",0,"add");'])->label('From'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'end_time[]')->textInput(['autocomplete'=>'off','placeholder' => Yii::t('db','To'), 'readonly'=>false,'class'=>'shift-time-check addscheduleform-end_time form-control','onchange' => 'shiftOneValue("shiftform",0,"add");'])->label('To'); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'appointment_time_duration[]')->Input('number',['min'=>'1.00','max'=>'240','placeholder' => Yii::t('db','Duration (Minutes)'), 'onchange' => 'maxvalidation("shiftform","appointment_time_duration",0,"add");','readonly'=>false])->label('Duration'); ?>
                                    </div>

                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'patient_limit[]')->Input('number',['min'=>'1.00','max'=>'240','placeholder' => Yii::t('db','Patient Limit'),'onchange' => 'patientcount("shiftform","patient_limit",0,"add");', 'readonly'=>false])->label('Patient Limit'); ?>
                                    </div>

                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'consultation_fees[]')->Input('number',['min'=>'0.00','placeholder' => Yii::t('db','Consultancy Fee'), 'onchange' => 'feesvalidation("shiftform","consultation_fees",0,this.value,"add");', 'readonly'=>false]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'emergency_fees[]')->Input('number',['min'=>'0.00','placeholder' => Yii::t('db','Emergency Fee'), 'onchange' => 'feesvalidation("shiftform","emergency_fees",0,this.value,"add");', 'readonly'=>false]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'consultation_fees_discount[]')->Input('number',['min'=>'1.00','max'=>'10.00','placeholder' => Yii::t('db','Discounted Consultancy Fee'), 'onchange' => 'maxvalidation("shiftform","consultation_fees_discount",0,"add");','readonly'=>false]); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php echo $form->field($model, 'emergency_fees_discount[]')->Input('number',['min'=>'1.00','max'=>'10.00','placeholder' => Yii::t('db','Discounted Emergency Fee'),'onchange' => 'maxvalidation("shiftform","emergency_fees_discount",0,"add");','readonly'=>false]); ?>
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
                            <input type="hidden" name="deletedImages" type="text" value=""/>
                            <?php echo Html::submitButton(Yii::t('frontend', 'Update Shift Detail'), ['id'=>'profile_from','class' => 'login-sumbit schedule_form_edit', 'name' => 'profile-button']) ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</section>



