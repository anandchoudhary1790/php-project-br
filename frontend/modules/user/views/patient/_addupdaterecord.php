<?php 

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<div class="login-section-record">
  <div class="modal fade model_opacity show" id="EditRecord" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

         <div class="modal-header text-center">
                <h4 class="modal-title w-100 ">Add Record</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body mx-3">
          <?php

           $form = ActiveForm::begin(['id' => 'patient-memberlist-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
            <?= $form->field($recordModel, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>
            <?= $form->field($recordModel, 'member_id')->hiddenInput(['maxlength' => true])->label(false); ?>
            <div class="file_row clearfix">
               <?php echo $form->field($recordModel, 'image_name')->textInput(['placeholder'=>'Record Label','maxlength' => 50])->label(false); ?>
            </div>
            <div class="file_row clearfix">
                <div class="pull-left">
                  <label>Upload Record</label>
                </div>
                <div class="pull-right">
                  <label class="btn-bs-file btn btn-lg btn-danger">
                    Browse
                    <?= $form->field($recordModel, 'image')->fileInput(['id'=>'file1',
                      'options' => ['accept' => 'image/*'],
                      'maxFileSize' => 5000000, // 5 MiB
                      ])->label(false);   ?>
                    </label>
                  </div>
            </div>
            <div class="file_row clearfix">
                <div class="submitbtn text-center">
                    <?php echo Html::submitButton(Yii::t('frontend', 'Add Record'), ['class' => 'add_record_btn', 'name' => 'signup-button']) ?>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>