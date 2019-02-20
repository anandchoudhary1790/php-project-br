<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MetaKeys */

$this->title = 'Update Key: '.$model->label;
$this->params['breadcrumbs'][] = ['label' => 'Meta Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="box">
    <div class="box-body">
        <div class="meta-keys-update">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>