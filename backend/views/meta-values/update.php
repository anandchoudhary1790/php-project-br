<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Update: '.$model->value;
$this->params['breadcrumbs'][] = ['label' => 'Meta Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-update">

            <?= $this->render('_form', [
                'model' => $model,'metakeys'=>$metakeys
            ]) ?>

        </div>
    </div>
</div>