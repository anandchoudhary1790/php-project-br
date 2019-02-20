<?php
use yii\helpers\Html;
use common\models\MetaKeys;
use common\models\MetaValues;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use kartik\select2\Select2;
?>
<?php $base_url= Yii::getAlias('@frontendUrl'); ?>
<?php $this->title = Yii::t('frontend', 'Doctor::Profile', ['modelAddressClass' => 'Doctor']);

$ajaxtreatmentUrl = "'".$base_url."/doctor/ajax-treatment-list'";

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
        
        $('#specialities').on('change', function () {
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
                    <div class="pace-left">
                        <?php $getuserImage=DrsPanel::getUserAvator($userProfile->user_id,'thumb');?>
                            <img src="<?php echo$getuserImage; ?>" alt="image">
                      </div>
                      <div class="pace-right">
                        <h4><?php echo $userProfile->name; ?> <small> (<?php echo $userProfile->experience; ?> yrs) </small> <!-- <a href="#" class="pull-right notification-cionmain"> <i class="fa fa-bell"></i> </a> --></h4>
                        <p> <?php echo $userProfile->address1; ?> </p>
                        <div class="doctor-educaation-details">
                          <p>Specialization : <span> <?php echo $userProfile->speciality; ?> </span> </p>
                          <?php if($userProfile->gender==1) { ?> <p> Gender : <span>Male</span> </p> <?php } 
                          else if($userProfile->gender==2){ ?> <p> Gender : <span>Female</span> </p> <?php }
                          else if($userProfile->gender==3){ ?> <p> Gender : <span>Other</span> </p> <?php }?>
                          <?php if($userProfile->experience){ ?><p>Experience : <span><?php echo $userProfile->experience; ?> + years </span> </p> <?php } ?>
                          </div>
                          <?php $maximumPoints  = 100;
                          if(Yii::$app->user->isGuest){
                          }else
                          {     
                          $hasCompletedProfileImage='';            
                          $hasCompletedDegree='';            
                          $hasCompletedSpeciality='';            
                          $hasCompletedTreatment='';            
                          $hasCompletedExperience='';            
                          $hasCompletedServices='';            
                          $hasCompletedDescription='';            
                          $hasCompletedGender='';            
                          $hasCompletedDob='';            
                          $hasCompletedDefault='';            
                               if($userProfile->avatar!="" && $userProfile->avatar_path!="" && $userProfile->avatar_base_url!=""){
                                  $hasCompletedProfileImage = 10;
                               }
                               if($userProfile->degree!=""){
                                  $hasCompletedDegree = 10;
                               }
                               if($userProfile->speciality!=""){
                                  $hasCompletedSpeciality = 10;
                               }
                               if($userProfile->treatment!=""){
                                  $hasCompletedTreatment = 10;
                               }
                               if($userProfile->experience!=""){
                                  $hasCompletedExperience = 10;
                               }
                               if(!empty($userProfile->services!="")){
                                  $hasCompletedServices = 10;
                               }
                               if($userProfile->description!=""){
                                  $hasCompletedDescription = 10;
                               } 
                               if($userProfile->gender!=""){
                                  $hasCompletedGender = 10;
                               }
                               if($userProfile->dob!=""){
                                  $hasCompletedDob = 10;
                               }
                                if($userProfile->name!=""){
                                  $hasCompletedDefault = 10;
                               }
                            
                               $profilepercentage = ($hasCompletedProfileImage+$hasCompletedDegree+$hasCompletedSpeciality+$hasCompletedTreatment+$hasCompletedExperience +$hasCompletedServices+$hasCompletedDescription+$hasCompletedGender+$hasCompletedDob+$hasCompletedDefault)*$maximumPoints/100;
                            }  ?>
                          <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $profilepercentage?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $profilepercentage; ?>%"> Complete Profile <?php echo $profilepercentage; ?>% </div>
                          </div>
                           </div>
                          <div class="repl-foracpart">
                            <ul>
                              
                              <li><a href="<?php echo $base_url.'/doctor/edit-profile'?>">
                                <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/edit-icon.png" alt="image"> </div>
                                <div class="datacontent-et">
                                  <p>Edit Profile</p>
                                </div>
                              </a> 
                              </li>   
                              <li> 
                                <a class="modal-call" href="javascript:void(0)" title="Add More " id="speciality-popup">
                                  <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/doctor-profile-icon4.png" alt="image">  </div>
                                  <div class="datacontent-et">
                                    <p>Speciality</p>
                                  </div>
                                </a> 
                              </li>
                                <li><a href="<?php echo $base_url.'/doctor/services'?>" class="hide">
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
                                <li><a href="<?php echo $base_url.'/doctor/experiences'?>">
                                  <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/exprience-icon.png" alt="image"> </div>
                                  <div class="datacontent-et">
                                    <p>Experience</p>
                                  </div>
                                </a> 
                              </li> 

                                 <li><a href="<?php echo $base_url.'/doctor/educations'?>">
                                <div class="list-mainboxe"> <img src="<?php echo $base_url?>/images/profile-icon/education-icon.png" alt="image"> </div>
                                <div class="datacontent-et">
                                  <p>Education</p>
                                </div>
                              </a> 
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
                $form = ActiveForm::begin(['enableAjaxValidation'=>false]); ?>
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
                        ])->label(false);
                    ?>
                    </div>
                    <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>
                  </div>
                  <?php ActiveForm::end(); ?>
                </div>
              </div><!-- /.modal-content -->
            </div>
          </div>
        </div>
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
        <?php $form = ActiveForm::begin(['enableAjaxValidation'=>false]); ?>
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