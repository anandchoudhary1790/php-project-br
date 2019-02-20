<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use backend\modelAddresss\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;
$this->title = Yii::t('frontend', 'Hospital::Profile Update', [
  'modelAddressClass' => 'Doctor',
  ]);

$citiesList=[];//DrsPanel::getCitiesList();
// pr($citiesList);die;
if(empty($citiesList)){
  $citiesList=ArrayHelper::map(DrsPanel::getCitiesList($userAddress->state,'name'),'name','name');
}
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');


$base_url= Yii::getAlias('@frontendUrl');
$base_urls= "'".$base_url."'";
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="appointment_part">
            <div class="hosptionhos-profileedit">
              <h2 class="addnew2">Edit Profile</h2>

              <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data','action' => 'userProfile']]); ?>
              <div class="col-md-12">
                <div class="user_profile_img">
                  <div class="doc_profile_img">
                    <img src="<?= DrsPanel::getUserDefaultAvator($userProfile->user_id,'thumb'); ?>" />
                  </div>

                  <input style="display:none" id="uploadfile" onchange="readImageURL(this);" type="file" name="UserProfile[avatar]" class="form-control" placeholder="uploadfile">
                  <i class="fa fa-camera profileimageupload" style="cursor:pointer"></i>
                </div>
              </div>
              <div class="clearfix"></div>
              <div class="row discri_edithost">
                <p class="col-sm-3"> Profile Name :</p>
                <span class="col-sm-7"> <?php echo $form->field($userProfile, 'name')->textInput(['class'=>'input_field'])->label(false); ?></span> 
              </div>
              <div class="row discri_edithost">
                <p class="col-sm-3"> E-mail ID :</p>
                <span class="col-sm-7">  <?php echo $form->field($userModel, 'email')->textInput(['class'=>'input_field'])->label(false); ?></span> 
              </div>
              <div class="row discri_edithost">
                <p class="col-sm-3"> Mobile Number :</p>
                <span class="col-sm-7">  <?php echo $form->field($userModel, 'phone')->textInput(['class'=>'input_field',])->label(false); //'onchange'=>"checkGroupUniqueNum('user',$base_urls,$userModel->id,$userModel->groupid)"?></span> </div>
                <div class="row discri_edithost">
                  <p class="col-sm-3"> Establishment Year :</p>
                  <span class="col-sm-7"> <?= $form->field($userProfile, 'dob')->textInput([])->widget(
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
                    ],])->label(false); ?> </span> </div>

                <?php
                    echo $form->field($userAddress,'id')->hiddenInput()->label(false);
                    echo $form->field($userAddress,'user_id')->hiddenInput()->label(false);
                    echo $form->field($userAddress,'type')->hiddenInput()->label(false);
                ?>
                <div class="row discri_edithost">
                    <p class="col-sm-3"> Hospital Name :</p>
                    <span class="col-sm-7"> <?php echo $form->field($userAddress, 'name')->textInput(['class'=>'input_field'])->label(false); ?></span>
                </div>



                        <div class="row discri_edithost">
                          <p class="col-sm-3">State :</p>
                          <span class="col-sm-7"> <?= $form->field($userAddress, 'state')->dropDownList($statesList,['id'=>'state_list','prompt' => 'Select State','placeholder' => 'Select State'])->label(false) ?> </span> 
                        </div>
                        <div class="row discri_edithost">
                          <p class="col-sm-3">City :</p>
                          <span class="col-sm-7"> <?= $form->field($userAddress, 'city')->dropDownList($citiesList,['id'=>'city_list','prompt' => 'Select City','placeholder' =>'Select City'])->label(false) ?> </span> 
                        </div>

                        <div class="row discri_edithost">
                            <p class="col-sm-3">Address:</p>
                            <span class="col-sm-7"> <?= $form->field($userAddress, 'address')->textInput(['class'=>'input_field','placeholder' => 'Address'])->label(false)?> </span>
                        </div>

                          <div class="row discri_edithost">
                                <p class="col-sm-3">Area:</p>
                                <span class="col-sm-7"> <?= $form->field($userAddress, 'area')->textInput(['class'=>'input_field','placeholder' => 'Area'])->label(false)?> </span> </div>


                        <div class="bookappoiment-btn" style="margin:0px;">
                          <?php echo Html::submitButton(Yii::t('frontend', 'Profile Update'), ['id'=>'profile_from','class' => 'login-sumbit', 'name' => 'profile-button']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>  

          <?php

          $frontend=Yii::getAlias('@frontendUrl');
          $cityUrl="'".$frontend."/hospital/city-list'";

          $js="
          $('#state_list').on('change', function () {
            if($(this).val())
            {
             $.ajax({
              method:'POST',
              url: $cityUrl,
              data: {state_id:$(this).val()}
            })
            .done(function( msg ) { 

              $('#city_list').html('');
              $('#city_list').html(msg);

            });
          }
        }); 
        ";
        $this->registerJs($js,\yii\web\VIEW::POS_END); 
        ?>