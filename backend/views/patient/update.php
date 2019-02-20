<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('backend', 'Update {modelClass}: ', ['modelClass' => 'Patient']) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->email, 'url' => ['view', 'id' => $model->email]];
$this->params['breadcrumbs'][] = ['label'=>Yii::t('backend', 'Update')];
?>

<?php //echo $this->render('_update_top', ['userProfile' => $userProfile]); ?>


<div class="row" id="userdetails">
    <div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Personal Information</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>
                <div class="col-sm-12">
                    <?php echo $form->field($userProfile, 'name') ?>
                </div>
                <div class="col-sm-12">
                    <?php echo $form->field($model, 'email') ?>
                </div>



                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3">
                            <?php echo $form->field($model, 'countrycode')->dropDownList(\common\components\DrsPanel::getCountryCode(91)) ?>
                        </div>
                        <div class="col-sm-9">
                            <?php echo $form->field($model, 'phone') ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= $form->field($userProfile, 'dob')->textInput()->widget(
                            DatePicker::className(), [
                            'convertFormat' => true,
                            'options' => ['placeholder' => 'Date of Birth*'],
                            'layout'=>'{input}{picker}',
                            'pluginOptions' => [
                                'autoclose'=>true,
                                'format' => 'yyyy-MM-dd',
                                'endDate' => date('Y-m-d'),
                                'todayHighlight' => true
                            ],]); ?>
                    </div>
                </div>


                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-1"><label>Gender</label></div>

                                    <?php echo $form->field($userProfile, 'gender', ['options' => ['class' =>
                                        'col-sm-12']])->radioList(['1' => 'Male', '2' => 'Female'], [
                                        'item' => function ($index, $label, $name, $checked, $value) {

                                            $return = '<span>';
                                            $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
                                            $return .= '<label for="gender_' . $label . '" >' . ucwords($label) . '</label>';
                                            $return .= '</span>';

                                            return $return;
                                        }
                                    ])->label(false)
                                    ?>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-6 hide">
                            <?php echo $form->field($userProfile, 'blood_group')->dropDownList(\common\components\DrsPanel::getBloodGroups()) ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 hide">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php echo $form->field($userProfile, 'height')->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?php echo $form->field($userProfile, 'weight')->textInput() ?>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 hide">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php echo $form->field($userProfile, 'marital')->textInput() ?>
                        </div>
                        <div class="col-sm-6">
                            <?php echo $form->field($userProfile, 'location')->textInput() ?>
                        </div>
                    </div>
                </div>



                <div class="form-group clearfix col-sm-12">
                    <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>

</div>
<div class="modal fade" id="editlivestatus" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalContact">Update Profile/Live Status</h4>
                    </div>
                    <div class="modal-body">
                        <?= $this->render('_edit_live_status', [
                            'userProfile' => $userProfile
                            ]) ?>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
