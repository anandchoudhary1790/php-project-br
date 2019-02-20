<?php 
$baseUrl= Yii::getAlias('@frontendUrl'); 

//pr($doctorSpecialities);die;
?>


    <div class="doctor-timing-main">
        <?php if(!empty($doctorSpecialities['speciality']))
         {
         $Servicedata = explode(',', $doctorSpecialities['speciality']);
         foreach ($Servicedata as $list) {
         ?>
         <div class="morning-parttiming">
          <div class="main-todbox">
            <div class="pull-left">
              <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-bag-icon.png" alt="image"> 
                <span id="hospital-name" ><?php echo $list ?></span></div>
              </div>
            </div>
          </div>
          <?php } 
        }
        else { ?>
        You have no speciality
        <?php } ?>
        <hr>
        <?php 
        if(!empty($doctorSpecialities['treatments']))
        {
          ?> <h3>Treatments</h3> <?php 
          $Treatmentdata = explode(',', $doctorSpecialities['treatments']);
          foreach ($Treatmentdata as $list) {
            ?>
            <div class="morning-parttiming">
              <div class="main-todbox">
                <div class="pull-left">
                  <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-bag-icon.png" alt="image"> 
                    <span id="hospital-name" ><?php echo $list ?></span></div>
                  </div>
                </div>
              </div>
              <?php 
              } 
            } 
            ?>
          </div>



