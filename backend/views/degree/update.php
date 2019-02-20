<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Update Degree: '.$model->value;
$this->params['breadcrumbs'][] = ['label' => 'Degree', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-update">

            <?= $this->render('_form', [
                'model' => $model
            ]) ?>

        </div>
    </div>
</div>