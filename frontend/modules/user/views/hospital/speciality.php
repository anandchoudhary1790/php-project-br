<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use kartik\select2\Select2;

$baseUrl= Yii::getAlias('@frontendUrl'); 

$this->title = Yii::t('frontend', 'Hospital::Specialities/Treatments', [
  'modelAddressClass' => 'Hospital',
  ]);
?>

<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">       
         <div class="today-appoimentpart mb-3">
          <h3> Specialities / Treatments </h3>
        </div>
        <div class="doctor-timing-main">
        <?php if(!empty($specialities['speciality']))
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
              <?php 
              } 
            } 
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


