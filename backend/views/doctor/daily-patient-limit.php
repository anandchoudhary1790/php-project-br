<?php



/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->getPublicIdentity();
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['detail', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Daily Patient Limit';
?>
<div class="box">
    <div class="box-body">
        <div class="user-view">

            <div id="ajaxLoadShiftDetails">
                <?= $this->render('_dailylimit', [
                    'model' => $model,'userShift'=>$userShift,'listaddress'=>$listaddress
                ]); ?>
            </div>
            </div>
        </div>
    </div>
</div>
