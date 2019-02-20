<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;


$this->title = Yii::t('frontend', 'Patient::Profile');
$baseUrl=Yii::getAlias('@frontendUrl');

?>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <div class="appointment_part">
                        <div class="appointment_details">
                            <div class="pace-part main-tow">
                                <h3 class="addnew">Edit Profile</h3>
                                
                                    <?php $form = ActiveForm::begin(['id' => 'patient-profile-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
                                        <div class="row">
											<div class="col-md-12">
												<div class="user_profile_img">
													<div class="doc_profile_img patient_profile_img">
													 
														<?php 
														$image = DrsPanel::getUserAvator($userProfile->user_id);
														echo Lightbox::widget([
														'files' => [
														[
														'thumb' => $image,
														'original' => $image,
														'title' => $userProfile['name'],
														],
														]
														]); 
														 ?>
													</div>

													<input style="display:none" id="uploadfile" onchange="readImageURL(this);" type="file" name="UserProfile[avatar]" class="form-control" placeholder="uploadfile">
													<i class="fa fa-camera profileimageupload" style="cursor:pointer"></i>
												</div>
											</div>
											<div class="col-md-6">
												<?php echo $form->field($userProfile, 'name')->textInput(['class'=>''])->label(false); ?>
											</div>
											<div class="col-md-6">
												<?php echo $form->field($userModel, 'email')->textInput(['class'=>''])->label(false); ?>
											</div>
											<div class="col-md-6">
												<?php echo $form->field($userModel, 'phone')->textInput(['class'=>''])->label(false); ?>
											</div>

											<div class="col-md-6 ">
												<?= $form->field($userProfile, 'dob')->textInput([])->widget(
												DatePicker::className(), [
												'convertFormat' => true,
												'type' => DatePicker::TYPE_INPUT,
												'options' => ['placeholder' => 'Date of Birth*','class'=>'form-group selectpicker '],
												'layout'=>'{input}',
												'pluginOptions' => [
												'autoclose'=>true,
												'format' => 'yyyy-MM-dd',
												'endDate' => date('Y-m-d'),
												'todayHighlight' => true
												],])->label(false); ?>
											</div>

											<div class="">
												<?php
												echo $form->field($userProfile, 'gender', ['options' => ['class' =>
												'col-sm-12 selectpicker']])->radioList($genderList, [
												'item' => function ($index, $label, $name, $checked, $value) {

												$return = '<span>';
												$return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
												$return .= '<label for="gender_' . $label . '" >' . ucwords($label) . '</label>';
												$return .= '</span>';

												return $return;
												}
												])->label('Gender');
												?>
											</div>

											<div class="col-md-6 hide">
												<?php echo $form->field($userProfile, 'blood_group')
													->dropDownList(DrsPanel::getBloodGroups(),
														['class'=>'selectpicker','prompt'=>'Select Blood Group'])
													->label(false); ?>
											</div>

											<div class="col-md-6 hide">
												<?php echo $form->field($userProfile, 'marital')->dropDownList(DrsPanel::getMaritalStatus(),['class'=>'selectpicker','prompt'=>'marital','placeholder'=>'Marital'])->label(false); ?>
											</div>
											<div class="col-md-6 hide">
												<?php echo $form->field($userProfile, 'weight')->textInput(['class'=>'','prompt'=>'Weight','placeholder'=> 'Weight'])->label(false); ?>
											</div>

											<div class="col-md-6 hide">
												<?php echo $form->field($userProfile, 'location')
													->textInput(['class'=>'','prompt'=>'location','placeholder'=>'Location'])
													->label(false); ?>
											</div>
											<div class="col-md-6 hide">
												<?php
												echo $form->field($userProfile, 'height')->dropDownList(DrsPanel::getPatientHeight(),['class'=>' selectpicker','prompt'=>'Height in Feet', 'placeholder'=>'Height In Feet'])->label(false); ?>
											</div>
											<div class="col-md-6 hide">
												<?php echo $form->field($userProfile, 'inch')->dropDownList(DrsPanel::getInch(),['class'=>'selectpicker','prompt'=>'Height in Inch','placeholder'=> 'Height In Inch'])->label(false); ?>
											</div>
											<div class="col-sm-12 text-center">
												<?php echo Html::submitButton(Yii::t('backend', 'Profile Update'), ['class' => 'submit_btn btn btn-primary', 'name' => 'signup-button']) ?>
											</div>
										</div>
                                     <?php ActiveForm::end(); ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
            </div>
        </div>
    </div>
</section>
<?= Yii::$app->session->getFlash('error'); ?>