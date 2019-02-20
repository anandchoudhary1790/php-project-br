<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$base_url= Yii::getAlias('@frontendUrl');


 ?>
 <?php $form = ActiveForm::begin(['id'=>'experience-update-form','action'=>$base_url."/doctor/experience-update",'enableAjaxValidation'=>true,]); ?>
 		<?php echo $form->field($model,'id')->hiddenInput()->label(false); ?>
 		<?php echo $form->field($model,'user_id')->hiddenInput()->label(false); ?>
                    <?= $this->render('_experiences_form', [
                        'model' => $model,
                        'form'=>$form
                    ]) ?>
<?php ActiveForm::end(); ?>