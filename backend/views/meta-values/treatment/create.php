<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Add Treatment';
$this->params['breadcrumbs'][] = ['label' => 'Treatments', 'url' => ['treatment']];
$this->params['breadcrumbs'][] = 'Add';
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