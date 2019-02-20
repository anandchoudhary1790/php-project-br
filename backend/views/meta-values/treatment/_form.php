<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */
/* @var $form yii\widgets\ActiveForm */

$idarray=array();

foreach($metakeys as $val) {
    $idarray[$val['id']] = $val['label'];
}


?>

<div class="meta-values-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if($model->isNewRecord){
        echo $form->field($model, 'key')->dropDownList($idarray, ['prompt' => Yii::t('common', 'Select Speciality key')])->label('Speciality');
    }
    else{
        echo $form->field($model, 'key')->dropDownList($idarray, ['disabled'=>!$model->isNewRecord])->label('Speciality');
    }
     ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'status')->dropDownList(\common\models\MetaValues::statuses()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
