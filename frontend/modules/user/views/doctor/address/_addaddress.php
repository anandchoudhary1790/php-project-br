<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
use yii\helpers\Url;
use common\models\UserAdddressImages;
use common\models\UserAdddress;


/* @var $this yii\web\View */
/* @var $model common\models\UserAddress */
/* @var $form yii\widgets\ActiveForm */
$citiesList=[];//DrsPanel::getCitiesList();
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
$idarray=array('Hospital'=>'Hospital','Clinic'=>'Clinic');
?>
<?php

$frontend=Yii::getAlias('@frontendUrl');

$js="
$('#state_list').on('change', function () {
 $.ajax({
  method:'POST',
  url: 'ajax-city-list',
  data: {state_id:$(this).val()}
})
.done(function( msg ) { 

  $('#city_list').html('');
  $('#city_list').html(msg);

});
}); 





";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>

<div class="register-section">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalContact">Add Address</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">

                <div class="box-body">
                  <?php $form = ActiveForm::begin(['options' => ['enctype'=> 'multipart/form-data']]); ?>
                  <?= $form->field($model, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>
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
                                <?= $form->field($model, 'type')->dropDownList($idarray)->label(false);  ?>
                                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Hospital/Clinic Name'])->label(false) ?>
                                <?= $form->field($model, 'address')->textInput(['placeholder' => 'Address Line 1'])->label(false) ?>

                                <div class="row">
                                 <div class="col-sm-6">
                                  <?= $form->field($model, 'state')->dropDownList($statesList,['id'=>'state_list','prompt' => 'Select State'])->label(false) ?>
                                </div>
                                <div class="col-sm-6">
                                  <?= $form->field($model, 'city')->dropDownList($citiesList,['id'=>'city_list','prompt' => 'Select City'])->label(false) ?>
                                </div>

                              </div>
                              <div class="row">
                                <div class="col-sm-6">
                                  <?= $form->field($model, 'landline')->textInput(['placeholder' => 'Landline'])->label(false) ?>
                                </div>
                                <div class="col-sm-6">
                                 <?= $form->field($model, 'phone')->textInput(['placeholder' => 'Mobile No'])->label(false) ?>
                               </div>
                             </div>
                             
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="file btn btn-lg ">
                                  <span style="text-align: right;float: left;padding-right: 17px;">Other Images</span>
                                  <?= $form->field($userAdddressImages, 'image[]')->fileInput([
                                    'options' => ['accept' => 'image/*'],
                                'maxFileSize' => 5000000, // 5 MiB
                                'multiple' => true,

                                ])->label(false);   ?>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                              <input type="hidden" name="type" value="add"/>
                              <?= Html::submitButton('Add', ['class' => 'login-sumbit']) ?>
                          </div>
                          <?php ActiveForm::end(); ?>
                        </div>
            </div>
        </div>
    </div>
</div>

