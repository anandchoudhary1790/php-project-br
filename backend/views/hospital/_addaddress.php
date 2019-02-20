<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model common\models\UserAddress */
/* @var $form yii\widgets\ActiveForm */
$citiesList=[];//DrsPanel::getCitiesList();
$statesList=ArrayHelper::map(DrsPanel::getStateList(),'name','name');
?>

<div class="box-body"> 
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'user_id')->hiddenInput(['maxlength' => true])->label(false); ?>

        <?= $form->field($model, 'type')->hiddenInput(['maxlength' => true])->label(false);  ?>
         <?= $form->field($model, 'is_register')->hiddenInput(['maxlength' => true])->label(false);  ?>
        <?= $form->field($model, 'name')->textInput()->label('Hospital Name') ?>
        <?= $form->field($model, 'address')->textInput()->label('Address Line 1') ?>

        <div class="row">
             <div class="col-sm-6">
                <?= $form->field($model, 'state')->dropDownList($statesList,['id'=>'state_list','prompt' => 'Select State'])->label('State') ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'city')->dropDownList($citiesList,['id'=>'city_list','prompt' => 'Select City'])->label('City') ?>
            </div>
           
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'phone')->textInput()->label('Mobile/Phone no.') ?>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'lat')->textInput()->label('Latitude') ?>
            </div>
            
            <div class="col-sm-6">
                <?= $form->field($model, 'lng')->textInput()->label('Longitude') ?>
            </div> 
        </div>
        <div class="form-group">
            <?= Html::submitButton('Add', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

<?php

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