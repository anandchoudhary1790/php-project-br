<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\PatientMemberFiles;
$memberData =  DrsPanel::membersList($id);
$PatientMembersData = new  PatientMemberFiles();
$baseUrl=Yii::getAlias('@frontendUrl');

$this->registerJs("
  $('.OpenRecord').on('click', function () {
  var myMemberId = $(this).attr('data-id');
     $('.modal-body #memberId').val(myMemberId);
   })

",\yii\web\VIEW::POS_END); ?>
<div class="record_part_list">
  <ul class="record_list">
    <?php 
    if(!empty($memberData)){
      
      foreach ($memberData as $member) { ?>
      <li><?php echo $member['name']?> 
       <div class="pull-right">
         <a href="#" data-toggle="modal" data-target="#attacheFile1" data-id=
      "<?php echo $member['id']?>" class="add-record  OpenRecord"> <i class="fa fa-plus-circle pull-right add_record_plus"></i></a>

      <a href="#" onclick="return updatePatientRecord(<?php echo $member['id']; ?>);" class="add-record "> <i class="fa fa-pencil pull-right add_record_plus"></i></a>

      <a href="<?php echo $baseUrl?>/patient/patient-record-files/<?php echo $member['slug']?>"  class="dd eye_icon"><i class="fa fa-eye pull-right" aria-hidden="true"></i></a>
       </div>
      </li>
      <?php }} else { ?> 
      Records Not Founds 

      <?php } ?>

    </ul>
  </div>
  


  
  <div class="login-section-record add-record-section">
  <div class="modal fade model_opacity" id="attacheFile1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

         <div class="modal-header text-center">
                <h4 class="modal-title w-100 font-weight-bold">Update Record</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body mx-3">
          <?php

           $form = ActiveForm::begin(['id' => 'patient-memberlist-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
         

          <?= $form->field($PatientMembersData, 'member_id')->hiddenInput(['value'=> '','id'=> 'memberId'])->label(false); ?>

          <div class="file_row clearfix">
              <?= $form->field($PatientModel, 'name')->textInput(['placeholder' => 'Name'])->label(false); ?>
          </div>
            <div class="file_row clearfix">
               <?php echo $form->field($PatientModel, 'phone')->textInput(['class'=>'','placeholder'=>'Mobile Number'])->label(false); ?>
          </div>
            <div class="file_row clearfix">
              <?php
              echo $form->field($PatientModel, 'gender', ['options' => ['class' =>
                'col-sm-12 selectpicker']])->radioList($genderList, [
                'item' => function ($index, $label, $name, $checked, $value) {

                  $return = '<span>';
                  $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
                  $return .= '<label for="gender_' . $label . '" >' . ucwords($label) . '</label>';
                  $return .= '</span>';

                  return $return;
                }
                ])->label('Gender');
                ?>
              </div>
          <div class="file_row clearfix">
            <div class="pull-left">
              <label>Upload Record</label>
            </div>
            <div class="pull-right">
              <label class="btn-bs-file btn btn-lg btn-danger">
                Browse
                <?= $form->field($PatientMembersData, 'image[]')->fileInput(['id'=>'file1', 'multiple' => true,
                  'options' => ['accept' => 'image/*'],
                  'maxFileSize' => 5000000, // 5 MiB
                  ])->label(false);   ?>
                </label>
              </div>
              <br>
              <div class="submitbtn">
              <?php echo Html::submitButton(Yii::t('frontend', 'Add Record'), ['class' => 'submit_btn btn btn-primary', 'name' => 'signup-button']) ?>
              </div>
            </div>

            <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>

   

 <?php $js="
     

    function updatePatientRecord(id){
    $.ajax({
        url: 'patient-record-update',
        dataType:   'html',
        method:     'POST',
        data: { id: id},
        success: function(response){
            $('#updaterecord').empty();
            $('#updaterecord').append(response);
            $('#updaterecord').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
    });

    
}
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>

<div class="register-section">
<div class="modal fade model_opacity" id="updaterecord" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
</div>
</div>