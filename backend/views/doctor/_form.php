<?php

use common\models\User;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>

<div class="user-form">
    <?php $form = ActiveForm::begin(); ?>
        <div class="col-sm-12">
            <?php echo $form->field($model, 'name') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'email') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'phone') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'dob') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'bloodgroup') ?>
        </div>
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

        </div>
        <div class="form-group clearfix col-sm-12">
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
