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
// echo $model->user_id;die;
$expForm="'service-form'";
$js="
     
    // add/update modal
     $('#get_exp_add_model').on('click', function () {
       $.ajax({
          url: $expForm,
          data: {user_id:$model->user_id},
          dataType: 'html'
    })
      .done(function( msg ) { 

        $('#exp-upseart-body').html('');
        $('#exp-upseart-body').html(msg);
        $('#upseart_exp_modal').modal({backdrop: 'static',keyboard: false})

      });
    });
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 

$this->title = Yii::t('backend', 'Facilities/Services');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row" id="useraddress">

    <div class="col-md-8">
        <p>
                        <button type="button" id="get_exp_add_model" class="btn btn-success" >Add</button> 

        </p>
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Facilities/Services
                </h3>
            </div>
            <div class="panel-body">            
                <div class="table-responsive">
                    <table class="table no-margin">
                        <thead>
                            <tr>
                                <th>Services</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
/*                            pr($services);die;
*/                            if(!empty($services)){
                            foreach($services as $service){ 
                                ?>
                                <tr>
                                    <td><?php echo $service['label']; ?></td>
                                   
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

    <div class="modal fade" id="upseart_exp_modal" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Add Facilities/Services</h4>
            </div>
            <div class="modal-body" >
                <div id="exp-upseart-body" >

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>