<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = Yii::t('backend', 'Patient Detail');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-view">

            <?php echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'auth_key',
                    'email:email',
                    [
                        'label'=>'Status',
                        'value'=>$model->getStatusLabel($model->status),
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                    'logged_at:datetime',
                ],
            ]) ?>

        </div>
    </div>
</div>