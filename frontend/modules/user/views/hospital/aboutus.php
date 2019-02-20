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
?>

<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="appointment_part">
            <div class="hosptionhos-profileedit">
              <h2 class="addnew2">My About Us</h2>

              <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data','action' => 'userProfile']]); ?>
             
              <div class="clearfix"></div>
              <hr>
              <div class="row discri_edithost">
                <p class="col-sm-3"> Description :</p>

                <span class="col-sm-8"> 
                 <?php echo $form->field($userProfile, 'description')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            )->label(false) ?>

                </span> 
              </div>
               <div class="row discri_edithost">
                <p class="col-sm-3"> Vision :</p>
                <span class="col-sm-8"> 
                  <?php echo $form->field($userProfile, 'vision')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            )->label(false) ?>

                </span> 
              </div>
               <div class="row discri_edithost">
                <p class="col-sm-3"> Mission :</p>
                <span class="col-sm-8"> 
                  <?php echo $form->field($userProfile, 'mission')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            )->label(false) ?>
                </span> 
              </div>
               <div class="row discri_edithost">
                <p class="col-sm-3"> Timing :</p>
                <span class="col-sm-8">
                   <?php echo $form->field($userProfile, 'timing')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            )->label(false) ?>

                 </span> 
              </div>
                        <div class="bookappoiment-btn" style="margin:0px;">
                          <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['id'=>'profile_from','class' => 'login-sumbit', 'name' => 'profile-button']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>  

