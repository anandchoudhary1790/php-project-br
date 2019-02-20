<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;

/* @var $this yii\web\View */
/* @var $model common\models\Areas */
/* @var $form yii\widgets\ActiveForm */

$citiesList=ArrayHelper::map(DrsPanel::getCitiesList($state_id = NULL,$listby = NULL),'id','name');
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'id','name');

$backend=Yii::getAlias('@backendUrl');
$cityUrl="'".$backend."/site/city-list'";

$js="
     $('#state_list').on('change', function () {
       $.ajax({
          method:'POST',
          url: $cityUrl,
          data: {state_id:$(this).val()}
    })
      .done(function( msg ) { 

        $('#city_list').html('');
        $('#city_list').html(msg);

      });
    }); 
   




";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>

<div class="areas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'state_id')->dropDownList($statesList,['id'=>'state_list','prompt' => 'Select State'])->label('State') ?>

    <?= $form->field($model, 'city_id')->dropDownList($citiesList,['id'=>'city_list','prompt' => 'Select City'])->label('City') ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 1 => '1', 0 => '0', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'lat')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lng')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
