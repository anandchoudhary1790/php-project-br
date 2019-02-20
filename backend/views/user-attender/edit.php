<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $roles yii\rbac\Role[] */
$this->title = Yii::t('backend', 'Add New Attender', [
    'modelClass' => 'Doctor',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-create">
            <div class="user-form">
                <?php $form = ActiveForm::begin(); ?>
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                        <?php echo $form->field($model, 'name') ?>
                        </div>
                         <div class="col-sm-6">
                             <?php echo $form->field($model, 'address_id',['options'=>['class'=>'']])->dropDownList($hospitals)->label('Hospital/Clininc'); ?>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        
                    <div class="col-sm-6">
                        <?php echo $form->field($model, 'email') ?>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-3">
                            <?php echo $form->field($model, 'countrycode')->dropDownList(\common\components\DrsPanel::getCountryCode(91)) ?>
                        </div>
                        <div class="col-sm-9">
                            <?php echo $form->field($model, 'phone') ?>
                        </div>
                    </div>
                    
                    </div>
                   
                    <?php /*
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
                    </div> 
                    </div>
                    </div> */ ?>
                    <?php /*
                    <div class="form-group clearfix col-sm-12">
                        <div class="row">
                            <div class="col-sm-1"><label>Gender</label></div><br>

                            <?php
                            echo $form->field($model, 'gender', ['options' => ['class' =>
                                'col-sm-11']])->radioList(['1' => 'Male', '2' => 'Female'], [
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

                    </div> */ ?>
                    <div class="form-group clearfix col-sm-12">
                        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>