<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;


$this->title = Yii::t('frontend', 'Doctor::Add New Attender', [
    'modelClass' => 'Doctor',
]);
$base_url= Yii::getAlias('@frontendUrl');
if(!empty($hospitalId))
{
$attenderUrl="'".$base_url."/hospital/attender-show'";
$attenderDelete="'".$base_url."/hospital/attender-delete'";
}
else{
  $attenderUrl="'".$base_url."/doctor/attender-show'";
$attenderDelete="'".$base_url."/doctor/attender-delete'";
}

$this->registerJs("
$('.call-edit-modal').on('click', function () {
    id=$(this).attr('data-id');
    $.ajax({
          method: 'POST',
          url: $attenderUrl,
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

$('.call-delete-modal').on('click', function () {
    id=$(this).attr('data-id');
    name= $('#user-name-'+id).html();
    $('#ConfirmModalHeading').html('Confirm Attender Delete');
    $('#ConfirmModalContent').html('<p>Are You sure to delete '+name+'</p>');
    $('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false,show: true})
    
     .one('click', '#confirm_ok', function(e) {
             $.ajax({
                method:'POST',
                url: $attenderDelete,
                data: { id: id,}
          })
            .done(function( msg ) { 
  
              $('#ConfirmModalShow').modal('hide');
              $('#SuccessModalHeading').html('Attender Delete');
              $('#SuccessModalContent').html(msg);
              $('#success-modal').modal({show: true});


            });
            //ajax close
         }) //confirm alert box close

   
       });

", \yii\web\VIEW::POS_END); 
?>

<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-10 mx-auto">
          <div class="today-appoimentpart">
            <div class="col-md-6 mx-auto calendra_slider">
              <h3> Attender </h3>
              <div class="calender_icon_main location pull-right "> <a class="modal-call" href="javascript:void(0)" title="Add More Attender" id="attender-popup"><i class="fa fa-plus"></i></a> </div>
            </div>
          </div>
          <div class="appointment_part">
            <div class="appointment_details">
              <div class="row">
              <?php if(count($list)>0){ foreach ($list as $key => $item) { ?>
                <div class="col-sm-6">
                  <div class="pace-part main-tow">
                    <div class="pace-left"> <img src="<?php echo $item['image']; ?>" alt="image"></div>
                    <div class="pace-right">
                      <h4 id="user-name-<?php echo $item['id'] ?>" ><?php echo $item['name']; ?></h4>
                      <p><span class="first_textB">Phone No:</span> +91 <?php echo $item['phone']; ?> </p>
                      <p><span class="first_textB">Email ID:</span> <?php echo $item['email']; ?> </p>
                      <div class="text-right ea_icon clearfix">
                        <ul>
                          <li><a href="javascript:void(0)" ><i class="fa fa-pencil call-edit-modal" aria-hidden="true" data-id="<?php echo $item['id']?>"></i></a></li>
                          <li class="hide"><a href="javascript:void(0)"><i class="fa fa-trash-o call-delete-modal" data-id="<?php echo $item['id']?>" aria-hidden="true"></i></a></li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } }else{ ?>
                 <div class="col-sm-6">
                 <h3> You have not added attender. </h3>
                 </div>
                <?php  } ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>



<div class="register-section">
<div id="attender-modal" class="modal fade model_opacity"  role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
       <h4 class="modal-title" id="myModalContact">Add Attender </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               
            </div>
            <div class="modal-body">
                    <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true,]); ?>
                    <?= $this->render('_form', [
                        'model' => $model,
                        'form'=>$form,
                        'shifts'=>$shifts,
                    ]) ?>
                    <?php ActiveForm::end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
</div>

<div class="register-section">
<div class="modal fade model_opacity" id="attenderEdit-modal" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
  <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
       <h4 class="modal-title" id="myModalContact">Update Attender </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               
            </div>
            <div class="modal-body" id="edit-modal-form">
                   
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
</div>