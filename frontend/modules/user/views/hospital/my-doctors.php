<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use backend\modelAddresss\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;
$base_url= Yii::getAlias('@frontendUrl'); ?>
<?php  $this->title = Yii::t('frontend', 'Hospital :: MyDoctors', [
  'modelAddressClass' => 'Hospital',
  ]);?>
  <div class="inner-banner"> </div>
  <section class="mid-content-part">
    <div class="signup-part">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="today-appoimentpart">
              <h3 class="mb-3"> My Doctors </h3>
            </div>
            <div class="search-boxicon">
              <div class="search-iconmain"> <i class="fa fa-search"></i> </div>
                <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data','action' => 'my-doctors']]); ?>
                <?php echo $form->field($userDoctorModel, 'name')->textInput(['class'=>'input_field','placeholder' => "search doctors ...",'class'=> 'form-control'])->label(false); ?>


                <?php ActiveForm::end(); ?>
            </div>
            
            <div class="row">
              <?php 
              if(!empty($findDoctor)) {
                foreach($findDoctor as $doctor) { 
                  ?>
                  <div class="col-sm-4">
                    <div class="pace-part">
                      <?php if(!empty($doctor['avatar'])) { ?>
                      <div class="pace-left "> <img src="<?php echo $doctor['avatar_base_url'].$doctor['avatar_path'].$doctor['avatar']?>" alt="image"></div>
                      <?php } else { ?> 
                      <div class="pace-left "> <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="image"></div
                        <?php } ?>
                        <div class="pace-right">
                          <h4><?php echo isset($doctor['name'])?$doctor['name']:''?>
                          </h4>
                          <p><?php echo isset($doctor['speciality'])?$doctor['speciality']:''?></p>
                          <p> <?php echo isset($doctor['degree'])?$doctor['degree']:''?> </p>
                        </div>
                      </div>
                      <?php } 
                    }

                      if(isset($lists)){  
                        foreach ($lists as $list) {  ?>
                            <div class="col-sm-4">
                              <div class="pace-part">
                                <?php if(!empty($list['avatar'])) { ?>
                                <div class="pace-left "> <img src="<?php echo $list['avatar_base_url'].$list['avatar_path'].$list['avatar']?>" alt="image">
                                </div>
                                <?php } else { ?> 
                                <div class="pace-left "> <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="image">
                                </div>
                                <?php } ?>
                                <div class="pace-right">
                                  <h4><?php echo isset($list['name'])?$list['name']:''?>
                                    <div class="pull-right mydoctorpart hide"> <i class="fa fa-pencil" aria-hidden="true"></i></div>
                                  </h4>
                                  <p><?php echo isset($list['speciality'])?$list['speciality']:''?></p>
                                  <p> <?php echo isset($list['degree'])?$list['degree']:''?> </p>
                                  <p class="hide"> Triveni Nagar, Gopalpura Bypass.. </p>
                                </div>
                              </div>
                            </div>
                      <?php }  
                          } 

                          if(empty($findDoctor) && !isset($lists))
                          {
                            echo 'Record Not Found';
                          }
                    

                          ?>
               
              </div>
              <div class="btdetialpart hide">
                <div class="pull-left">
                  <input class="confirm-theme" value="Add " type="button">
                </div>
                <div class="pull-right text-right">
                  <input class="confirm-theme" value="Remove " type="button">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

