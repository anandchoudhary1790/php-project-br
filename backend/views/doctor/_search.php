<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\UserSearch */
/* @var $form yii\bootstrap\ActiveForm */
$baseurl =\Yii::$app->getUrlManager()->getBaseUrl();

$this->registerCssFile($baseurl.'/css/search.css');
$options=\common\models\User::admin_statuses();
array_unshift($options,'All');
?>



<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-horizontal searchlist',],
        'fieldConfig' =>
            [
                'template' => "<div class=\"col-lg-4\">{label}</div>\n<div class=\"col-lg-8\">{input}</div>",
            ],
    ]); ?>


    <?php echo $form->field($model, 'name') ?>

    <?php echo $form->field($model, 'phone')->label('Mobile'); ?>



    <?= $form->field($model, 'admin_status')->dropDownList($options)->label('Status'); ?>



    <div class="form-group bsubmit">
        <?php echo Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary submit']) ?>
        <?php echo Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-default reset']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<div class="clear"></div>
