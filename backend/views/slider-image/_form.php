<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SliderImage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="slider-image-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sub_title')->textInput() ?>

    <?= $form->field($model, 'pages')->dropDownList(['Home'=>'Home','Event'=>'Event']); ?>
    
   <?= $form->field($model, 'image')->fileInput([
              'options' => ['accept' => 'image/*'],
            'maxFileSize' => 5000000, // 5 MiB
               
          ]);   ?>
    <?php if($model->image){  ?>
    <div class="edit-image" style="margin-left: 200px;margin-top: -70px;">
    <img  src="<?php echo Yii::getAlias('@storageUrl/source/slider-images/').$model->image; ?>" width="75" height="75"/>
    </div>
    <?php } ?>

    <?= $form->field($model, 'app_image')->fileInput([
        'options' => ['accept' => 'image/*'],
        'maxFileSize' => 5000000, // 5 MiB

    ]);   ?>
    <?php if($model->app_image){  ?>
        <div class="edit-image" style="margin-left: 200px;margin-top: -70px;">
            <img  src="<?php echo Yii::getAlias('@storageUrl/source/slider-images/').$model->app_image; ?>" width="75" height="75"/>
        </div>
    <?php } ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'Not Active','1'=>'Active']); ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
