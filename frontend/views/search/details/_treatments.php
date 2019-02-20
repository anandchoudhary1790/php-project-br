<?php 
$baseUrl= Yii::getAlias('@frontendUrl');
if(!empty($treatments)){ ?>
<div class="pace-part patient-prodetials">
  <div class="row">
    <div class="col-sm-12">
      <div class="pace-icon"> <img src="<?php echo $baseUrl?>/images/doctor-profile-icon4.png"> <!-- <i class="fa fa-heartbeat" aria-hidden="true"></i>  --></div>
      <div class="pace-right main-second">
        <h4> Treatment  </h4>
           <p>
              <?php 
              $string = $treatments;
              $parts = explode(",", $string);
              $treatment = implode(', ', $parts);
              echo isset($treatment)?$treatment:''; // Return the value
              ?>
            </p>
        </div>
      </div>
    </div>
  </div>
  <?php } ?>