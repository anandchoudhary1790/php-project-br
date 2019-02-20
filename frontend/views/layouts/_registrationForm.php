<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Groups;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use frontend\modules\user\models\SignupForm;

$signup= new SignupForm();
$groups = Groups::allgroups();
?>

<div class="register-section model_opacity">
    <div class="modal fade model_opacity" id="signup-modal" tabindex="-1"
         role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h3 id="myModalLabel">Registration <span> with Drs Panel</span></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">
                    <?php $form = ActiveForm::begin(['id'=>"signupform",'action'=>'','method'=>"post",'enableAjaxValidation' => true,'class'=>'form-horizontal']); ?>
                    <div class="row">
                        <div  class="col-md-12">
                            <?php
                            unset($groups[6]);
                            echo $form->field($signup, 'groupid',
                                ['options' => ['class' => 'col-sm-12 reg_on_change reg_group_channge']])->radioList($groups,
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    $return = '<span class="span_col_3">';
                                    $return .= Html::radio($name, $checked, ['value' => $value,
                                        'autocomplete' => 'off', 'id' => 'reg_group_' . $label,'class'=>'groupid_change']);
                                    $return .= '<label for="reg_group_' . $label . '" >' . ucwords($label) . '</label>';
                                    $return .= '</span>';
                                    return $return;
                                }])->label(false);

                            ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo $form->field($signup, 'name')->textInput(['class'=>'reg_on_change input_field form-control','placeholder'=>'Full Name'])->label(false); ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo $form->field($signup, 'email')->textInput(['class'=>'input_field form-control','placeholder'=>'Email'])->label(false); ?>
                        </div>

                        <div class="col-md-12">
                            <?php echo $form->field($signup, 'phone')->textInput(['class'=>'input_field ten_number form-control','placeholder'=>'Mobile Number','maxlength'=>'10'])->label(false) ?>
                        </div>

                        <div class="col-md-12" id="signup_dob">

                            <?= $form->field($signup, 'dob')->textInput([])->widget(
                                DatePicker::className(), [
                                'convertFormat' => true,
                                'type' => DatePicker::TYPE_INPUT,
                                'options' => ['class'=>'reg_on_change selectpicker','placeholder' => 'Date of Birth','autocomplete'=>"off"],
                                'layout'=>'{input}',
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'yyyy-MM-dd',
                                    'endDate' => date('Y-m-d'),
                                    'todayHighlight' => true
                                ],])->label(false); ?>

                        </div>

                        <!--<div id="gender_list" class="col-md-12">
                            <?php
/*                            $genderList = Drspanel::getGenderList();
                            echo $form->field($signup, 'gender')->dropDownList($genderList,['class'=>'reg_on_change input_field form-control','prompt' => 'Gender'])->label(false); */?>
                        </div>-->

                        <div id="gender_list" class="form-group clearfix">
                            <div class="col-sm-12">
                                <div class="row">
									<div class="col-sm-2"><label><?= Yii::t('db','Gender'); ?>:</label></div>

									<?php $genderList = Drspanel::getGenderList(); ?>

									<?php echo $form->field($signup, 'gender',['options'=>['class'=>
										'col-sm-10']])->radioList($genderList, [
										'item' => function ($index, $label, $name, $checked, $value) {

											$return = '<span>';
											$return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label,'class'=>'gender_change']);
											$return .= '<label for="gender_' . $label . '" >' . Yii::t('db',ucwords($label)) . '</label>';
											$return .= '</span>';

											return $return;
										}
									])->label(false) ?>
								</div>
                            </div>

                        </div>



                        <div class="clearfix"></div>
                        <div class="col-md-12 text-center">
                            <?php echo Html::Button(Yii::t('frontend', 'Register'), ['id'=>'signup_from','class' => 'login-sumbit', 'name' => 'signup-button']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>

                </div>
            </div>
        </div>
    </div>
</div>