<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Advertisement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="advertisement-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-sm-12">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <?= $form->field($model, 'start_date')->textInput()->widget(
                        DatePicker::className(), [
                        'convertFormat' => true,
                        'options' => ['placeholder' => 'Show From'],
                        'layout'=>'{input}{picker}',
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-MM-dd',
                            'endDate' => date('Y-m-d'),
                            'todayHighlight' => true
                        ],])->label('Show From'); ?>

                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <?= $form->field($model, 'end_date')->textInput()->widget(
                        DatePicker::className(), [
                        'convertFormat' => true,
                        'options' => ['placeholder' => 'Show Till'],
                        'layout'=>'{input}{picker}',
                        'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-MM-dd',
                            'endDate' => date('Y-m-d'),
                            'todayHighlight' => true
                        ],])->label('Show Till'); ?>

                </div>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'show_for_seconds')->Input('number'); ?>
            </div>

            </div>
    </div>


    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-6">
                <?= $form->field($model, 'sequence')->Input('number') ?>

            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'status')->dropDownList([ 'active' => 'Active', 'inactive' => 'Inactive', ]) ?>

            </div>
        </div>
    </div>



    <div class="form-group clearfix col-sm-12">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
