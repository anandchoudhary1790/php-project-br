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
        <?= $form->field($model, 'name'); ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'dob') ?>
        <?= $form->field($model, 'address') ?>
        <?= $form->field($model, 'phone') ?>
    <div class="form-group">
            <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div>
