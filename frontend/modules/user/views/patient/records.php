<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\User;
use common\models\UserProfile;
use common\models\PatientMemberFiles;
use common\models\Groups;
use frontend\modules\user\models\PatientMemberForm;

$memberData =  DrsPanel::membersList($id);
$PatientMembersData = new  PatientMemberFiles();
$PatientModel = new PatientMemberForm();
$baseUrl=Yii::getAlias('@frontendUrl');
$genderList=DrsPanel::getGenderList();

$this->registerJsFile($baseUrl.'/js/popper.min.js', ['depends' => [yii\web\JqueryAsset::className()]]); 

$this->registerJs("
  $('.OpenRecord').on('click', function () {
  var myMemberId = $(this).attr('data-id');
     $('.modal-body #memberId').val(myMemberId);
   })

",\yii\web\VIEW::POS_END);
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
      <div class="col-md-9">
          <div class="today-appoimentpart">
              <h3 class="text-left mb-3"> My Records List </h3>
          </div>
        <div class="record_part_list">
          <ul class="record_list record-col">
            <?php 
            if(!empty($memberData)){
                foreach ($memberData as $member) { ?>
                <li class="dropdown">
				    <div class="pull-left"><?php echo $member['name']?> </div>
                    <div class="pull-right">
                        <input onclick="location.href='<?php echo yii\helpers\Url::to(['patient-appointments','id'=>$member['id']]); ?>'" class="confirm-theme record-list-btn-app" value="Appointments " type="button">
                        <input onclick="location.href='<?php echo yii\helpers\Url::to(['patient-record-files','slug'=>$member['slug']]); ?>'" class="confirm-theme record-list-btn-record" value="Records " type="button">
                         <!--<a href="#" onclick="updatePatientRecord(<?php /*echo $member['id'];*/?>)" class="add-record"> <i class="fa fa-pencil pull-right add_record_plus"></i></a>
                        <a href="<?php /*echo $baseUrl*/?>/patient/patient-record-files/<?php /*echo $member['slug']*/?>"  class="dd eye_icon"><i class="fa fa-eye pull-right" aria-hidden="true"></i></a>-->
							<a href="#"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="red-star"><i class="fa fa-share-alt"></i></a>
							<ul class="dropdown-menu">
								<li><a href="#"><i class="fa fa-facebook"></i> Facebook </a></li>
								<li><a href="#"><i class="fa fa-twitter"></i> Twitter </a></li>
								<li><a href="#"><i class="fa fa-google-plus"></i> Google+ </a></li>
								<li><a href="#"><i class="fa fa-instagram"></i> Instagram </a></li>
								<li><a href="#"><i class="fa fa-whatsapp"></i> Whatsapp </a></li>
							</ul>
                    </div>
                </li>
                <?php }
                } 
                else { ?> 
                    Records not found 
                <?php } ?>
            </ul>
        </div>
        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
        </div>
        </div>
        </div>
        </section>

<div class="register-section">
<div class="modal fade model_opacity" id="updaterecord" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
</div>
</div>