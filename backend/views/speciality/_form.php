<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\MetaValues;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="meta-values-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>
    <?php if(in_array($model->key,MetaValues::Image_Upload_Key_id)){ ?>

        <?= $form->field($model, 'image')->fileInput([
              'options' => ['accept' => 'image/*'],
            'maxFileSize' => 5000000, // 5 MiB
               
          ]);   ?>
            <?php if($model->image){  ?>
            <div class="edit-image" style="margin-left: 200px;margin-top: -70px;">
            <img  src="<?php echo Yii::getAlias('@storageUrl/source/'.strtolower(MetaValues::getKeyName($model->key)).'/').$model->image; ?>" width="75" height="75"/>
            </div>
            <?php } ?>

            <hr>
            <?= $form->field($model, 'icon')->fileInput([
              'options' => ['accept' => 'image/*'],
            'maxFileSize' => 5000000, // 5 MiB
               
          ]);   ?>
            <?php if($model->icon){  ?>
            <div class="edit-icon" style="margin-left: 200px;margin-top: -70px;">
            <img  src="<?php echo Yii::getAlias('@storageUrl/source/'.strtolower(MetaValues::getKeyName($model->key)).'/').$model->icon; ?>" width="75" height="75"/>
            </div>
            <?php } ?>

    <?php } ?>
    <?php echo $form->field($model, 'status')->dropDownList(\common\models\MetaValues::statuses()) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
