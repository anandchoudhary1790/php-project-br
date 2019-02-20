<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('backend', 'Hospitals/Clinics');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row" id="useraddress">
     <div class="col-md-12">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Hospitals/Clinics
                    <div class="text-right">
                        <a href="javascript:void(0)" title="Add More Addresses" data-target="#addaddress" data-toggle="modal" id="'.$userProfile->user_id.'">Add More</a>
                    </div>
                </h3>
            </div>
            <div class="panel-body">
                <?php
                echo \yii\widgets\ListView::widget( [
                    'layout' => "<div class='list-item' id='listitems'>{items}</div>",
                    'dataProvider' => $addressProvider,
                    'itemView' => '_address',
                    ]);

                    ?>

                </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="addaddress" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalContact">Add Address To Doctor's Profile</h4>
                </div>
                <div class="modal-body">
                    <?php $address=new \common\models\UserAddress();
                    $address->user_id=$userid;
                    ?>
                    <?= $this->render('_addaddress', [
                        'model' => $address
                        ]) ?>
                    </div>
                </div><!-- /.modal-content -->
            </div>
        </div>

        