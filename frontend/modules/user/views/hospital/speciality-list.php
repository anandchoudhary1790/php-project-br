<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;


$baseUrl= Yii::getAlias('@frontendUrl'); 

  if(!empty($specialities['speciality']))
  {
  $Servicedata = explode(',', $specialities['speciality']);
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
    if(!empty($specialities['treatments']))
    {
      ?> <h3>Treatments</h3> <?php 
      $Treatmentdata = explode(',', $specialities['treatments']);
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
          <?php } 
        } 
        ?>


