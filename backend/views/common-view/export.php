<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
use common\models\Groups;
use yii\helpers\Url;

$idarray=array(''=>'Select export type','format'=>'File Format','data'=>'All Record');
$baseUrl=Yii::getAlias('@backendUrl');
$exportData="'".$baseUrl."/site/export-model-data'";
$exportData2=$baseUrl."/site/export-model-data";
$model_name="'User'";
$type=Groups::GROUP_DOCTOR;
$js="
    $('#file_confirm_ok').on('click', function(e) {

                export_type=$('#export_type').val();
                if(export_type==''){
                    $('.field-export_type').addClass('has-error');
                    $('.help-block').html('select export type');
                    return false;
                }else {
                	$('#FileModalShow').modal('hide');
                	return true;
                } 
              }) 
                
    $('#export_type').on('change', function(e) {
                export_type=$('#export_type').val();
                if(export_type==''){
                    $('.field-export_type').addClass('has-error');
                    $('.help-block').html('select export type');
                    return false;
                }else {

                	 $('.field-export_type').removeClass('has-error');
                    $('.help-block').html('');
                }

                 
              }) 
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>
	<?php $form = ActiveForm::begin(['action'=>$exportData2]); ?>
	<div class="modal-body"> 
	        <?= $form->field($model, 'type')->dropDownList($idarray,['id'=>'export_type'])->label(false);  ?>
    </div>
        <div class="modal-footer">
        	<?= Html::SubmitButton('Ok', ['class' => 'btn btn-default', 'id'=>"file_confirm_ok"]) ?>
		 <?php /*   <button type="button" data-dismiss="modal" class="btn btn-default" id="file_confirm_ok">Ok</button> */ ?>
		    <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
	  </div>
    <?php ActiveForm::end(); ?>
