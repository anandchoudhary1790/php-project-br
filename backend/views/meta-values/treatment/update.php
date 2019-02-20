<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MetaValues */

$this->title = 'Update Treatment: '.$model->value;
$this->params['breadcrumbs'][] = ['label' => 'Treatments', 'url' => ['treatment']];
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