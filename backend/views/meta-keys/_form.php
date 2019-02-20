<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MetaKeys */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="meta-keys-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true,'disabled'=>!$model->isNewRecord]) ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->dropDownList(\common\models\MetaKeys::statuses()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
