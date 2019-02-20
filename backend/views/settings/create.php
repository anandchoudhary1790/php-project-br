<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Add Meta Values';
$this->params['breadcrumbs'][] = ['label' => 'Meta Values', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-create">
            <?= $this->render('_form', [
                'model' => $model,'metakeys'=>$metakeys
            ]) ?>

        </div>
    </div>
</div>