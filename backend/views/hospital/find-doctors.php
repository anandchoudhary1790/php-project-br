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

$this->title = Yii::t('backend', 'Find Doctors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row" id="useraddress">


    <div class="col-md-8">
    
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Find Doctors
                </h3>
            </div>
            <div class="panel-body">            
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Hospital Name</th>
                                <th>Speciality</th>
                                <th>Degree</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(!empty($lists)){
                            foreach($lists as $list){ 
                                ?>
                                <tr>
                                    <td><?php echo $list['name']; ?></td>
                                    <td>
                                        <?php echo $list['speciality']?>
                                    </td> 
                                    <td>
                                        <?php echo $list['degree']?>
                                    </td>
                                    <td>
                                     
                                    </td>
                                </tr>  
                                <?php }
                            } else {
                                ?>
                                <tr><td>Record not found</td></tr>
                                <?php
                                
                            } ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>