<?php
use yii\helpers\Html;
use common\models\MetaKeys;
use common\models\MetaValues;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use kartik\select2\Select2;
 $base_url= Yii::getAlias('@frontendUrl'); ?>
<?php  $this->title = Yii::t('frontend', 'Hospital::Profile', [
  'modelAddressClass' => 'Hospital',
  ]);
$ajaxtreatmentUrl = "'".$base_url."/hospital/ajax-treatment-list'";

if($userProfile->treatment){
    $this->registerJs(" $('#treatment-value').show();",\yii\web\VIEW::POS_END);
}else{
    $this->registerJs(" $('#treatment-value').hide();",\yii\web\VIEW::POS_END);
}
$this->registerJs("
        $(document).ready(function(){
            var specval=$('#specialities').val();
            $('#specialities').trigger('change');
            $('#treatment-value').show();
        });
        
        $('.modal').on('shown.bs.modal', function (e) {
            var specval=$('#specialities').val();
            $('#specialities').trigger('change');
            $('#treatment-value').show();
        });
        
        $('#specialities').bind('change', function () {
              $('#treatment-value').hide();

          $.ajax({
            method: 'POST',
            url: $ajaxtreatmentUrl,
            data: { id: $('#specialities').val(),'user_id':$userProfile->user_id}
          })
          .done(function( msg ) { 
            if(msg){
              $('#treatment-value').show();
              $('#treatment-value').html('');
              $('#treatment-value').html(msg);
            }
          });
        });

        ",\yii\web\VIEW::POS_END);
  ?>
  <div class="inner-banner"> </div>
  <section class="mid-content-part">
    <div class="signup-part">
      <div class="container">
        <div class="row">
          <div class="col-md-8 mx-auto">
            <div class="appointment_part">
              <div class="appointment_details">
                <div class="pace-part main-tow">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="pace-left hos-clinics"> 
                       <?php if($userProfile->avatar){  ?>
                       <img src="<?php echo $userProfile->avatar_base_url.$userProfile->avatar_path.$userProfile->avatar; ?>" alt="image"> 
                       <?php } else { ?>
                       <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="">
                       <?php } ?> </div>
                       <div class="pace-right">
                         <h4><?php echo $userProfile->name; ?></h4>
                         <p> <?php echo $userProfile->address1; ?> </p>
                         <div class="doctor-educaation-details">
                          <p>Specialization : <span> <?php echo $userProfile->speciality; ?> </span> </p>
                          <?php if($userProfile->gender==1) { ?> <p> Gender :<span>Male</span> </p> <?php } 
                          else if($userProfile->gender==2){ ?> <p> Gender :<span>Female</span> </p> <?php }
                          else if($userProfile->gender==3){ ?> <p> Gender :<span>Other</span> </p> <?php } ?>
                          
                        </div> 

                        <?php $maximumPoints  = 100;
                          if(Yii::$app->user->isGuest){
                          }else
                          {     
                          /* UserProfile fields */
                          $hasFilledProfileImage='';            
                          $hasFilledGender='';            
                          $hasFilledDob='';            
                          $hasFilledDefault=''; 
                          
                          /* User Address field*/ 
                          
                          $hasFilledHospitalName='';            
                          $hasFilledMobileNo='';            
                          $hasFilledState='';            
                          $hasFilledCity='';            
                          $hasFilledAddress='';            
                          $hasFilledArea=''; 

                          /* Other field specailites/aboutus/services */

                          $hasFilledSpecialites='';            
                          $hasFilledAboutUs='';            
                          $hasFilledServices='';  

                          /* About us field*/  
                          $hasFilledDescription='';            
                          $hasFilledVision='';        
                          $hasFilledMission='';        
                          $hasFilledTiming='';        

                               if($userProfile->avatar!="" && $userProfile->avatar_path!="" && $userProfile->avatar_base_url!=""){
                                  $hasFilledProfileImage = 5;
                               }
                               if($userProfile->gender!=""){
                                  $hasFilledGender = 5;
                               }
                               if($userProfile->dob!=""){
                                  $hasFilledDob = 5;
                               }
                                if($userProfile->name!=""){
                                  $hasFilledDefault = 5;
                               }

                               /*User Address Field*/

                               if(!empty($useraddressList->name!="")){
                                  $hasFilledHospitalName = 5;
                               }
                               if($useraddressList->address!=""){
                                  $hasFilledAddress = 5;
                               } 
                               if($useraddressList->area!=""){
                                  $hasFilledArea = 5;
                               }
                               if($useraddressList->city!=""){
                                  $hasFilledCity = 5;
                               }
                                if($useraddressList->state!=""){
                                  $hasFilledState = 5;
                               }
                               if($useraddressList->phone!=""){
                                  $hasFilledMobileNo = 5;
                               }

                               /* Specialities/Services/AboutUs */

                                if(!empty($userProfile->services!="")){
                                  $hasFilledServices = 10;
                               }



                                if(!empty($userProfile->speciality!="")){
                                  $hasFilledSpecialites = 10;
                               }

                               if(!empty($userProfile->treatment!="")){
                                  $hasFilledTreatment = 10;
                               }
                            
                               $profilepercentage = ($hasFilledProfileImage+$hasFilledServices+$hasFilledDescription+$hasFilledVision+$hasFilledMission+$hasFilledTiming+$hasFilledGender+$hasFilledDob+$hasFilledDefault+$hasFilledHospitalName+$hasFilledAddress+$hasFilledArea+$hasFilledCity+$hasFilledState+$hasFilledMobileNo+$hasFilledSpecialites)*$maximumPoints/100;
                            }  ?>
                          <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $profilepercentage?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $profilepercentage; ?>%"> Complete Profile <?php echo $profilepercentage; ?>% </div>
                          </div>
                      
                      </div>
                      <div class="repl-foracpart">
                        <ul>
                          <li> <a href="<?php echo $base_url.'/hospital/edit-profile'?>">
                            <div class="list-mainboxe"> <img src="<?php echo $base_url ?>/images/doctor-profile-icon1.png" alt="image"> </div>
                            <div class="datacontent-et">
                              <p>Edit Profile</p>
                            </div>
                          </a> </li> 
                          <li> <a href="<?php echo $base_url.'/hospital/aboutus'?>">
                            <div class="list-mainboxe"> <img src="<?php echo $base_url ?>/images/doctor-profile-icon2.png" alt="image"> </div>
                            <div class="datacontent-et">
                              <p>My Aboutus</p>
                            </div>
                          </a> </li>
                        
                          <?php /*<li> <a href="<?php echo $base_url.'/hospital/speciality'?>">
                            <div class="list-mainboxe"> <img src="<?php echo$base_url ?>/images/doctor-profile-icon4.png" alt="image"> </div>
                            <div class="datacontent-et">
                              <p>Speciality/Treatment</p>
                            </div>
                          </a> </li> */?>

                          <li> 
                            <a class="modal-call" href="javascript:void(0)" title="Add More " id="speciality-popup">
                              <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon4.png" alt="image">  </div>
                              <div class="datacontent-et">
                                <p>Speciality</p>
                              </div>
                            </a> 
                          </li>
                          <li><a href="<?php echo $base_url.'/hospital/services'?>" class="hide">
                                        <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/service-icon.png" alt="image"> </div>
                                        <div class="datacontent-et">
                                            <p>Facilities/Services</p>
                                        </div>
                                    </a>
                                <?php
                                if(!empty($servicesList[0]['services'])){ ?>
                                <a class="modal-call" href="javascript:void(0)" title="Edit Services" id="experiences-popup">
                                  <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/service-icon.png" alt="image"> </div>
                                  <div class="datacontent-et">
                                      <p>Facilities/Services</p>
                                  </div>
                                </a>
                                <?php } else { ?>
                                <a class="modal-call" href="javascript:void(0)" title="Add Services" id="experiences-popup">
                                   <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/service-icon.png" alt="image"> </div>
                                  <div class="datacontent-et">
                                      <p>Facilities/Services</p>
                                  </div>
                                </a>
                                <?php }
                                ?>
                                </li>
                      
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Add/Edit/View Hospital Specialities  -->
  <div class="register-section">
    <div id="speciality-modal" class="modal fade model_opacity"  role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="specialityContact">View Specialities </h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          </div>
          <div class="modal-body">
            <?php
            $specialityies=explode(',',$userProfile->speciality);
            $userProfile->speciality = $specialityies;
            $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
            <?php
            $specialities_list=$treatment_list=array();
            foreach ($speciality as $h_key=>$speciality) {
              $specialities_list[$speciality->value] = $speciality->label;
            }
            $treatment_list=[];
            ?>

            <div class="edu-form">
              <?php 
              if(isset($specialityies[0]) && !empty($specialityies[0])) {
                $key=MetaValues::findOne(['value'=>$specialityies[0]]);
                $treatments=MetaValues::find()->andWhere(['status'=>1,'key'=>9])->andWhere(['parent_key'=>isset($key->id)?$key->id:'0'])->all();
                foreach ($treatments as $treatment) {
                  $treatment_list[$treatment->value] = $treatment->label;
                }
              }
              ?>  
              <?php echo  $form->field($userProfile, 'speciality')->widget(Select2::classname(), 
                [
                'data' => $specialities_list,
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Speciality','id'=>'specialities'],
                'pluginOptions' => [
                'allowClear' => true,
                ],
                ]); ?>  
              <?php 

              $userProfile->treatment = explode(',',$userProfile->treatment);
              ?>
              <div id="treatment-value">
                <?php echo  $form->field($userProfile, 'treatment')->widget(Select2::classname(),
                    [
                        'data' => $treatment_list,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select an treatment ...', 'multiple' => true],
                        'pluginOptions' => [
                            'tags' => true,
                            'tokenSeparators' => [',', ' '],
                            'maximumInputLength' => 10,
                            'allowClear' => true
                        ],
                    ]); ?>
                </div>
                <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
              </div>
              <?php ActiveForm::end(); ?>
            </div>
          </div><!-- /.modal-content -->
        </div>
      </div>
    </div>

  <!-- Add/Edit/View Hospital Services  -->
    <div class="register-section">
      <div id="experiences-modal" class="modal fade model_opacity"  role="dialog">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <?php if(!empty($servicesList)) {?>
              <h4 class="modal-title" id="experiencesContact">Update Services </h4>
              <?php } else { ?>
              <h4 class="modal-title" id="experiencesContact">Add Services </h4>
              <?php }?>
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
              <?php $form = ActiveForm::begin(['enableAjaxValidation'=>true]); ?>
              <?= $this->render('services_form', [
                'model' => $userProfile,
                'form'=>$form,
                'services' => $services,
                'servicesList' =>$servicesList
                ]) ?>
                <?php ActiveForm::end(); ?>
              </div>
            </div><!-- /.modal-content -->
          </div>
        </div>
      </div>