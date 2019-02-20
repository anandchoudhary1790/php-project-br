<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\DoctorForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Add New {modelClass}', [
    'modelClass' => 'Doctor',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

 $titleList=DoctorForm::prefixingList();


?>
<div class="box">
    <div class="box-body">
        <div class="user-create">
            <div class="user-form">
                <?php $form = ActiveForm::begin(); ?>
                    <div id="title_list" class="col-md-12">
                    <?php echo $form->field($model, 'prefix')->dropDownList($titleList,['class'=>'input_field form-control reg_on_change','prompt' => 'Select Title'])->label('Titl'); ?>
                    </div>
                    <div class="col-sm-12">
                        <?php echo $form->field($model, 'name') ?>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                    <div class="col-sm-6">
                        <?php echo $form->field($model, 'email') ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-3 hide">
                            <?php // echo $form->field($model, 'countrycode')->dropDownList(\common\components\DrsPanel::getCountryCode(91)) ?>
                        </div>
                        <div class="col-sm-12">
                            <?php echo $form->field($model, 'phone') ?>
                        </div>
                    </div>
                    </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">

                        <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'dob')->textInput()->widget(
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
                    <?php /*
                    <div class="col-sm-6">
                        <?php echo $form->field($model, 'blood_group')->dropDownList(\common\components\DrsPanel::getBloodGroups()) ?>
                    </div> */ ?>
                     <div class="form-group clearfix col-sm-6">
                        <div class="row">
                            <div class="col-sm-1"><label>Gender</label></div><br>

                            <?php
                            echo $form->field($model, 'gender', ['options' => ['class' =>
                                'col-sm-11']])->radioList(['1' => 'Male', '2' => 'Female', "3" => 'Other'], [
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
                    </div>
                   
                    <div class="form-group clearfix col-sm-12">
                        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>