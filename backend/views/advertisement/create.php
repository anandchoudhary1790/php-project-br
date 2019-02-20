<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Advertisement */

$this->title = 'Create Advertisement';
$this->params['breadcrumbs'][] = ['label' => 'Advertisements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="advertisement-create">
            <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
        </div>
    </div>
</div>