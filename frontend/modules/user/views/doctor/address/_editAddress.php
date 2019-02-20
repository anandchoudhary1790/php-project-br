<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
$base_url= Yii::getAlias('@frontendUrl');
?>

<div class="register-section">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalContact">Update Address</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>    
      </div>
      <div class="modal-body">

        <div class="box-body">
          <?php $form = ActiveForm::begin(['id'=>'update-form','enableClientValidation'=>true,'options' => ['enctype'=> 'multipart/form-data']]); ?>
          <div class="row">
            <div class="col-sm-12">
              <div class="user_profile_img">
                <div class="doc_profile_img">
                  <img src="<?= DrsPanel::getAddressAvator($model->id,true); ?>" />
                </div>
                  <input style="display:none" id="uploadfile" onchange="readImageURL(this);" type="file" name="UserAddress[image]" class="form-control" placeholder="uploadfile">
                  <i class="fa fa-camera profileimageupload" style="cursor:pointer"></i>
              </div>
            </div>
          </div>
          <input type="hidden" name="type" value="edit"/>
          <?= $form->field($model, 'id')->hiddenInput(['maxlength' => true])->label(false); ?>
          <?= $form->field($model, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>
          <?= $this->render('_form', [
                'model' => $model,
                'form'=>$form,
                'userAddressImages'=>$userAddressImages,
                'userAddressFiles' => $userAddressFiles
            ]) ?>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  