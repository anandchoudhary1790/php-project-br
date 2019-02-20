<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MetaKeys */

$this->title = 'Create Meta Keys';
$this->params['breadcrumbs'][] = ['label' => 'Meta Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="meta-keys-create">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>
</div>