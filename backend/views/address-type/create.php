<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Add Address Type';
$this->params['breadcrumbs'][] = ['label' => 'Address Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-values-create">
            <?= $this->render('_form', [
                'model' => $model
            ]) ?>

        </div>
    </div>
</div>