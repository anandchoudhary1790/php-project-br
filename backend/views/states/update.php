<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\States */

$this->title = 'Update: '.$model->value;
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box">
    <div class="box-body">
        <div class="states-update">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
