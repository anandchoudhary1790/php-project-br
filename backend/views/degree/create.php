<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Add Degree';
$this->params['breadcrumbs'][] = ['label' => 'Degree', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add';
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