<?php 
use yii\widgets\ActiveForm;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 
$updateStatus="'".$baseUrl."/appointment/status-update'";
$this->title = Yii::t('frontend','Hospital :: Facilities'); 

?>

<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">       
         <div class="today-appoimentpart mb-3">
             <h3> Services </h3>
             <div class="calender_icon_main location pull-right ">
                 <a class="modal-call" href="javascript:void(0)" title="Add More Attender" id="experiences-popup">
                     <?php
                     if(!empty($servicesList[0]['services'])){ ?>
                         <i class="fa fa-pencil"></i>
                     <?php } else { ?>
                         <i class="fa fa-plus"></i>
                     <?php }
                     ?>
                 </a>
            </div>
         </div>
        <div class="doctor-timing-main">
         <?= $this->render('services-list',['servicesList' => $servicesList]);?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<div class="register-section">
  <div id="experiences-modal" class="modal fade model_opacity"  role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title" id="experiencesContact">Add Services </h4>
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       </div>
       <div class="modal-body">
        <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
        <?= $this->render('services_form', [
          'model' => $model,
          'form'=>$form,
          'services' => $services,
          'servicesList' =>$servicesList
          ]) ?>
          <?php ActiveForm::end(); ?>
        </div>
      </div><!-- /.modal-content -->
    </div>
  </div>
</div>