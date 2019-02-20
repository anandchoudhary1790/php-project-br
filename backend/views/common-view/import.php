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
$exportData2=$baseUrl."/site/import-model-data";
$model_name="'User'";
$type=Groups::GROUP_DOCTOR;
$js="
    $('#file_confirm_ok').on('click', function(e) {

        var file_data = $('#import_file').prop('file')[0];   
	    var form_data = new FormData();                  
	    form_data.append('file', file_data);
console.log(form_data);
                export_type=$('#import_file').prop('files')[0];
                if(export_type==''){
                    $('.field-export_type').addClass('has-error');
                    $('.help-block').html('select file');
                    return false;
                }else {
                	$('#FileModalShow').modal('hide');
                	return true;
                } 
              }) 
                
    $('#export_type').on('change', function(e) {
                export_type=$('#import_file').prop('files')[0];
                if(export_type==''){
                    $('.field-export_type').addClass('has-error');
                    $('.help-block').html('select file');
                    return false;
                }else {

                	 $('.field-export_type').removeClass('has-error');
                    $('.help-block').html('');
                }

                 
              }) 
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>
	<?php $form = ActiveForm::begin(['action'=>$exportData2,'id'=>'import_file_data','options' => ['enctype' => 'multipart/form-data']]); ?>
	<div class="modal-body"> 
	        <?php  echo $form->field($model, 'file')->fileInput(['id'=>'import_file'])->label(false) ?>
    </div>
        <div class="modal-footer">
        	<?= Html::SubmitButton('Ok', ['class' => 'btn btn-default', 'id'=>"file_confirm_ok"]) ?>
		 <?php /*   <button type="button" data-dismiss="modal" class="btn btn-default" id="file_confirm_ok">Ok</button> */ ?>
		    <button type="button" data-dismiss="modal" class="btn btn-primary">Cancel</button>
	  </div>
    <?php ActiveForm::end(); ?>
