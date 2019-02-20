<?php
use common\components\DrsPanel;
$experiences = DrsPanel::getDoctorExperience($user_id);
$baseUrl= Yii::getAlias('@frontendUrl');

?>
<?php if(!empty($experiences)){ ?>
<div class="pace-part patient-prodetials">
  <div class="row">
    <div class="col-sm-12">
      <div class="pace-icon"> <img src="<?php echo $baseUrl?>/images/doctor-profile-icon5.png"> <!-- <i class="fa fa-pencil-square" aria-hidden="true"></i> --> </div>
      <div class="pace-right main-second">
        <h4> Experience </h4>
           <?php foreach ($experiences as $experience) { ?>
             <p><?php  echo date("Y",$experience['start']); echo '-'; echo (date('Y',$experience['end']) > date("Y"))?'Till Now':date('Y',$experience['end']); ?>, <?=$experience['hospital_name']?>
            </p>
            <?php 
             } ?>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>