<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Areas */

$this->title = 'Create Areas';
$this->params['breadcrumbs'][] = ['label' => 'Areas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="areas-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>
