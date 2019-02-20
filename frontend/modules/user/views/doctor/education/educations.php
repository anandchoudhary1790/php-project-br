<?php 
use yii\widgets\ActiveForm;
$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 
$this->title = Yii::t('frontend','DrsPanel :: Doctor Educations');
$educationUrl="'".$baseUrl."/doctor/education-show'";
$educationDelete="'".$baseUrl."/doctor/education-delete'";

$this->registerJs("
$(document).on('click','.call-edit-modal', function () {
    id=$(this).attr('data-id');
    $.ajax({
          method: 'POST',
          url: $educationUrl,
          data: { id: id,}
    })
      .done(function( msg ) { 
        if(msg){
        $('#edit-modal-form').html('');
        $('#edit-modal-form').html(msg);
        $('#attenderEdit-modal').modal({backdrop: 'static',keyboard: false,show: true})
        }
      });

   
       });

$(document).on('click','.call-delete-modal' ,function () {
    id=$(this).attr('data-id');
    name= $('#collage-name-'+id).html();
    $('#ConfirmModalHeading').html('Confirm Education Delete');
    $('#ConfirmModalContent').html('<p>Are You sure to delete '+name+'</p>');
    $('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false,show: true})
    
     .one('click', '#confirm_ok', function(e) {
             $.ajax({
                method:'POST',
                url: $educationDelete,
                data: { id: id,}
          })
            .done(function( msg ) { 
  
              $('#ConfirmModalShow').modal('hide');
              $('#SuccessModalHeading').html('Experience Delete');
              $('#SuccessModalContent').html(msg);
              $('#success-modal').modal({show: true});


            });
            //ajax close
         }) //confirm alert box close

   
       });

", \yii\web\VIEW::POS_END);

?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">       
         <div class="today-appoimentpart mb-3">
          <h3> Educations </h3>
          <div class="calender_icon_main location pull-right "> <a class="modal-call" href="javascript:void(0)" title="Add More Educations" id="attender-popup"><i class="fa fa-plus-circle"></i></a> </div>
        </div>
        <div class="doctor-timing-main">
         <?= $this->render('_educations_list',['edu_list' => $edu_list]);?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="register-section signup-part">
  <div id="attender-modal" class="modal fade model_opacity"  role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title" id="myModalContact">Add Education </h4>
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       </div>
       <div class="modal-body">
        <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
        <?= $this->render('_educations_form', [
          'model' => $model,
          'form'=>$form,
            'degreelist'=>$degreelist
          ]) ?>
          <?php ActiveForm::end(); ?>
        </div>
      </div><!-- /.modal-content -->
    </div>
  </div>
</div>