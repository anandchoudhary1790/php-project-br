<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
use yii\helpers\Url;
use common\models\UserAddressImages;

/* @var $this yii\web\View */
/* @var $model common\models\UserAddress */
/* @var $form yii\widgets\ActiveForm */
// pr($model);die;
$citiesList=[];
$base_url= Yii::getAlias('@frontendUrl');

if($model->state)
  $citiesList=ArrayHelper::map(DrsPanel::getCitiesList($model->state,'name'),'name','name');
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
$idarray=array('Hospital'=>'Hospital','Clinic'=>'Clinic');
$frontend=Yii::getAlias('@frontendUrl');
$cityUrl="'".$frontend."/site/city-list'";

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
}); ";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>

  


  <?= $form->field($model, 'type')->dropDownList($idarray)->label(false);  ?>

  <?= $form->field($model, 'name')->textInput(['placeholder' => 'Hospital/Clinic Name'])->label(false) ?>

  <?= $form->field($model, 'address')->textInput(['placeholder' => 'Address Line 1'])->label(false) ?>

  <div class="row">
    <div class="col-sm-6">
      <?= $form->field($model, 'state')->dropDownList($statesList,['id'=>'estate_list','prompt' => 'Select State'])->label(false) ?>
    </div>
    <div class="col-sm-6">
      <?= $form->field($model, 'city')->dropDownList($citiesList,['id'=>'ecity_list','prompt' => 'Select City'])->label(false) ?>
    </div>

  </div>
  <div class="row">
    <div class="col-sm-6">
      <?= $form->field($model, 'landline')->textInput(['placeholder' => 'Landline'])->label(false) ?>
    </div>
    <div class="col-sm-6">
      <?= $form->field($model, 'phone')->textInput(['placeholder' => 'phone'])->label(false) ?>
    </div>
  </div>
 
  <div class="row">
    <div class="col-sm-12">
  <?php /*
  <div class="file btn btn-lg ">
  <span style="text-align: right;float: left;padding-right: 17px;">Upload Prifile Image</span>
  <?php if(!empty($userAddressImages)){
  ?>
  <?php echo  $form->field($userAddressImages, 'image[]')->fileInput([
  'options' => ['accept' => 'image/*'],
  'maxFileSize' => 5000000, // 5 MiB
  'multiple' => true,

  ])->label(false);   ?>
  <?php } else {

  $userAddressImages = new UserAddressImages();
  echo  $form->field($userAddressImages, 'image[]')->fileInput([
  'options' => ['accept' => 'image/*'],
  'maxFileSize' => 5000000, // 5 MiB
  'multiple' => true,

  ])->label(false);   ?>

  <?php }?>
  </div>
  <?php if(!empty($userAddressImages->image)) { ?>
  <img src="<?php echo  $userAddressImages->image_base_url.$userAddressImages->image_path.$userAddressImages->image?>" id="upfile1" style="cursor:pointer;" width="50" height="50" />
  <?php } ?> 

  </div>*/ ?>
  <div class="file_area">
    <div class="attachfile_area"> 
      <?php 
      echo  $form->field($userAddressImages, 'image[]')->fileInput([
      'options' => ['accept' => 'image/*'],
      'maxFileSize' => 5000000, // 5 MiB
      'multiple' => true,
      ])->label(false);   
      ?>

  <span class="attachfile"><i aria-hidden="true" class="fa fa-paperclip"></i> Attach file </span> 

  </div>
  <ul>
    <?php foreach ($userAddressFiles as $usrimg) { ?>
    <li><img src="<?php echo $usrimg->image_base_url.$usrimg->image_path.$usrimg->image ?>" alt="img"> </li>
    <?php } ?>
  </ul>

  </div>
  <div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'login-sumbit']) ?>
  </div>
  </div>
  </div>