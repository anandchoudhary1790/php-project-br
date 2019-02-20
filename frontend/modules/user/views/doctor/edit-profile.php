<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use backend\modelAddresss\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;
use common\models\User;

$this->title = Yii::t('frontend', 'Doctor::Profile Update', [
  'modelAddressClass' => 'Doctor',
  ]);
$base_url= Yii::getAlias('@frontendUrl');

$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
$idarray=array('Hospital'=>'Hospital','Clinic'=>'Clinic');

$degree_list = array();$speciality_list=$treatment_list=array();
foreach ($degrees as $d_key=>$degree) {
  $degree_list[$degree->value] = $degree->label;
}


foreach ($specialities as $speciality) {
  $speciality_list[$speciality->value] = $speciality->label;
}
if(!empty($treatment)){
  foreach ($treatment as $obj) {
    $treatment_list[$obj->value] = $obj->label;
  }
}

if($userProfile->treatment){
  $this->registerJs(" $('#treatment_list_update').show();",\yii\web\VIEW::POS_END); 
}else{
  $this->registerJs(" $('#treatment_list_update').hide();",\yii\web\VIEW::POS_END); 
}
$this->registerJs("
   $(document).ready(function(){
        $('#specialities').trigger('change');
        $('#treatment_list_update').show();
   });
    
    
  $('#degree_Other').on('click', function () {
    if($(this).prop('checked') == true){
      $('#other_degree').show();
    }else{
      $('#other_degree').hide();
    } 
  });
  $('#specialities').bind('change', function () {       
       $.ajax({
        method: 'POST',
        url: 'ajax-treatment-list',
        data: { id: $('#specialities').val(),'user_id':$userProfile->user_id}
      })
      .done(function( msg ) { 
        if(msg){ 
          $('#treatment_list_update').html('');
          $('#treatment_list_update').html(msg);
        }
      });
});

",\yii\web\VIEW::POS_END); 


?>


<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="appointment_part">
            <div class="appointment_details">
              <div class="pace-part main-tow">
              
              <h2 class="addnew2">Edit Profile</h2>

                <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
                <div class="row">
              <div class="col-md-12">
                <div class="user_profile_img">
                  <div class="doc_profile_img">
                    <img src="<?= DrsPanel::getUserDefaultAvator($userProfile->user_id,'thumb'); ?>" />
                  </div>

                  <input style="display:none" id="uploadfile" onchange="readImageURL(this);" type="file" name="UserProfile[avatar]" class="form-control" placeholder="uploadfile">
                  <i class="fa fa-camera profileimageupload" style="cursor:pointer"></i>
                </div>
              </div>
              <div class="col-lg-6 col-sm-12">
                <?php echo $form->field($userProfile, 'name')->textInput(['class'=>'input_field','placeholder' =>'Name'])->label(false); ?>
              </div>
              <div class="col-md-6">
                <?php echo $form->field($userModel, 'email')->textInput(['class'=>'input_field','placeholder' =>'Email'])->label(false); ?>
              </div>
              <div class="col-md-6">
                <?php echo $form->field($userModel, 'phone')->textInput(['class'=>'input_field','placeholder'=>'Phone'])->label(false); //'onchange'=>"checkGroupUniqueNum('user',$base_urls,$userModel->id,$userModel->groupid)"?>
              </div>

                  <div class="col-lg-6 col-sm-12">
				    <div class="row">
                      <?php
                      echo $form->field($userProfile, 'gender', ['options' => ['class' =>
                          'col-md-12 selectpicker']])->radioList($genderList, [
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
                  </div>
                  <div class="clearfix"></div>

              <div class="col-md-6 dob_icon">
                <?= $form->field($userProfile, 'dob')->textInput([])->widget(
                  DatePicker::className(), [
                  'convertFormat' => true,
                  'type' => DatePicker::TYPE_INPUT,
                  'options' => ['placeholder' => 'Date of Birth*','class'=>'form-group '],
                  'layout'=>'{input}',
                  'pluginOptions' => [
                  'autoclose'=>true,
                  'format' => 'yyyy-MM-dd',
                  'endDate' => date('Y-m-d'),
                  'todayHighlight' => true
                  ],])->label(false); ?>
                </div>

                
                
                <div class="col-md-6">
                  <?php echo $form->field($userProfile, 'experience',['options'=> ['class'=>'input_field']])->Input('number',['min'=> '0','step'=>'1','placeholder' =>'Experience'])->label(false);?>
                </div>
                  <div class="col-md-2 hide">
                      <div class="row">Years</div>
                  </div>


                <div class="col-md-12 hide">
                  <?= $form->field($userProfile, 'speciality')->dropDownList($speciality_list,['id'=>'specialities','prompt'=>'Select Speciality','class' => 'selectpicker','placeholder'=> 'Speciality'])->label(false);  ?>
                </div>
                <div class="col-md-12 hide">
                  <div id="treatment_list_update">
                    <?php echo $this->render('ajax-treatment-list',['form'=>$form,'userProfile'=>$userProfile,'treatment_list'=>$treatment_list]); ?>
                  </div>
                </div>
                <div class="col-md-12">
                  <?php $userProfile->degree = explode(',',$userProfile->degree); 
                  ?>
                  <?php echo  $form->field($userProfile, 'degree')->widget(Select2::classname(), 
                    [
                    'data' => $degree_list,
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select a degree ...', 'multiple' => true],
                    'pluginOptions' => [
                    'tags' => true,
                    'tokenSeparators' => [',', ' '],
                    'maximumInputLength' => 10,
                    'allowClear' => true,
                    'multiple' => true,
                    ],
                    ])->label(false); ?>
                  </div>

                <div class="col-md-12">
                    <?= $form->field($userProfile, 'description')->textarea(['placeholder'=> 'About us'])->label(false);  ?>
                </div>

                  <div class="col-md-12 text-center lg_pt_20">
                    <div class="bookappoiment-btn" style="margin:0px;">
                        <?php if($model->admin_status == User::STATUS_ADMIN_PENDING){
                            $btn_text='Request for live';
                        } elseif($model->admin_status == User::STATUS_ADMIN_REQUESTED){
                            $btn_text='Requested for live';

                        } elseif($model->admin_status == User::STATUS_ADMIN_APPROVED){
                            $btn_text='Profile Approved';
                        } else{
                            $btn_text='Profile Update';
                        }?>
                      <?php echo Html::submitButton(Yii::t('frontend', $btn_text), ['id'=>'profile_from','class' => 'login-sumbit', 'name' => 'profile-button']) ?>
                    </div>
                  </div>
                </div>
                  <?php ActiveForm::end(); ?>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>