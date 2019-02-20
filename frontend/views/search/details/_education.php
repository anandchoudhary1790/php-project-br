<?php
use common\components\DrsPanel;
$educations = DrsPanel::getDoctorEducation($user_id);
$baseUrl= Yii::getAlias('@frontendUrl');

?>
<?php
if(!empty($educations)){ ?>
  <div class="pace-part patient-prodetials">
    <div class="row">
      <div class="col-sm-12">
        <div class="pace-icon"> <img src="<?php echo $baseUrl?>/images/doctor-profile-icon6.png"><!--  <i class="fa fa-map-marker" aria-hidden="true"></i> --> </div>
        <div class="pace-right main-second">
          <h4> Education <span class="ratingpart pull-right"></span></h4>
            <?php foreach ($educations as $education) {?>
            <p> <?=$education['education']?> - <?php echo $education['collage_name']?>, <?php  echo date("Y",$education['start']); echo '-'; echo (date('Y',$education['end']) > date("Y"))?'Till Now':date('Y',$education['end']); ?>
            </p>
            <?php }  ?>
          </div>
        </div>
      </div>
    </div>
<?php } ?>