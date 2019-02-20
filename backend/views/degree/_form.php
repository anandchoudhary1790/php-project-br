<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="meta-values-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'status')->dropDownList(\common\models\MetaValues::statuses()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
