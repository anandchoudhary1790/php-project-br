<?php 
use common\components\DrsPanel;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
?>
<?php $baseUrl= Yii::getAlias('@frontendUrl'); ?>
<?php  $this->title = Yii::t('frontend', 'Hospital :: My Find Doctors', [
  'modelAddressClass' => 'Hospital',
  ]);

$loginUser=Yii::$app->user->identity; 
$updateStatus="'".$baseUrl."/hospital/update-status'";
$this->title = Yii::t('frontend','Hospital :: Facilities'); 
$js="
$('.statusID').on('click', function () {
  status_id=$(this).attr('dataid');
  requested_to =$(this).attr('dataid2');
  requested_from =$(this).attr('dataid3');
  $.ajax({
    method:'POST',
    url: $updateStatus,
    data: {status:status_id,request_to:requested_to,request_from:requested_from}
  })
  .done(function( msg ) { 

  });
}); 
function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById('doctor_filter_input');
    filter = input.value.toUpperCase();
    ul = document.getElementById('filter_doctor');
    li = ul.getElementsByTagName('div');
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName('h4')[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = '';
        } else {
            li[i].style.display = 'none';
        }
    }
}";
$this->registerJs($js,\yii\web\VIEW::POS_END); ?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="today-appoimentpart">
            <h3 class="mb-3"> My Find Doctors </h3>
          </div>
          <?php $form = ActiveForm::begin(); ?>
          <div class="search-boxicon">
            <div class="search-iconmain"> <i class="fa fa-search"></i> </div>
                 <input placeholder="search doctors, hospitals..." class="form-control" type="text" id="doctor_filter_input" onkeyup="myFunction()">
            <?php 
            $doctorlists=array();
            if(isset($lists)){
            foreach ($lists as $list) {
              $doctorlists[$list['user_id']] = $list['name'];
            }
          } ?>
              
            </div>
            <div class="row" id="filter_doctor">
               <?php 
               if(!empty($lists)){ 
                $i=0;
                foreach ($lists as $list) {  
                  $hospital_id = $user_id;
                  $doctor_id = $list['user_id'];
                  $checkRequest = DrsPanel::sendRequestCheck ($hospital_id,$doctor_id);
                  ?>
                  <div class="col-sm-4" id="request_ids" >
                    <div class="pace-part">
                     <span class="pace-left ">
                       <?php if(!empty($list['avatar'])) { ?>
                    <img src="<?php echo $list['avatar_base_url'].$list['avatar_path'].$list['avatar']?>" alt="image">
                    <?php } else { ?> 
              <img src="<?php echo $baseUrl?>/images/doctor-profile-image.jpg" alt="image">
                    <?php } ?>
                    </span>
                      <span class="pace-right">
                        <h4><?php echo isset($list['name'])?$list['name']:''?>
                          <span class="pull-right mydoctorpart hide">
                            <a class="modal-call" href="javascript:void(0)" title="Update Status" id="experiences-popup"><i class="fa fa-plus"></i></a></span>
                          </h4>
                          <p><?php echo isset($list['speciality'])?$list['speciality']:''?></p>
                          <p><?php echo isset($list['degree'])?$list['degree']:''?> </p>
                          <p class="hide"> Triveni Nagar, Gopalpura Bypass.. </p>
                          <?php if(!empty($checkRequest)){ ?>
                          <p class="status">Status: <?php echo $checkRequest?></p>
                          <?php } ?>
                          <?php if($checkRequest=='pending') {?>
                          <button type="button" dataid ="1" dataid2="<?php echo $doctor_id ?>" dataid3="<?php echo $hospital_id?>" class="btn confirm-theme statusID">Send Request</button>
                          <?php } ?>
                        </span>
                      </div>
                    </div>


                    <?php } $i++; } 

                     if(empty($findDoctor) && !isset($lists))
                          {
                            echo 'Record Not Found';
                          }
                    ?>
                  </div>
                  <div class="btdetialpart hide">
                    <div class="pull-left">
                     <?php echo Html::submitButton(Yii::t('frontend', 'Update Status'), ['class' => 'confirm-theme', 'name' => 'signup-button']) ?>
                   </div>
                   <div class="pull-right text-right hide">
                    <input class="confirm-theme" value="Remove " type="button">
                  </div>
                </div>
                <?php ActiveForm::end(); ?>

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
               <h4 class="modal-title" id="experiencesContact">Update Statis </h4>
               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
             </div>
             <div class="modal-body">
              <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
              <?php //echo $this->render('update_status_form',['form' => $form,'model' => $model]) ?>

              <?php ActiveForm::end(); ?>
            </div>
          </div><!-- /.modal-content -->
        </div>
      </div>
    </div>