<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use common\components\DrsPanel;
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="user_profile_img">
                <div class="doc_profile_img">
                    <img src="<?= DrsPanel::getUserDefaultAvator($model->id,'thumb'); ?>" />
                </div>
                <?php echo  $form->field($model, 'avatar')->fileInput(['class' => 'form-control','onchange' => "readImageURL(this)",'style' => 'display:none'])->label(false); ?>
                
                 <i class="fa fa-camera profileimageupload" data-slug = "<?php echo ($model->id)?'attendereditform-avatar':'attenderform-avatar'?>" style="cursor:pointer"></i>
            </div>
        </div>
    </div>
      <div class="row">
        <div class="col-sm-12" >
            <?php echo  $form->field($model, 'shift_id')->widget(Select2::classname(), 
                ['data' => $shifts,
                'size' => Select2::SMALL,
                'options' => ['multiple' => true]
                ])->label('Select Multiple Shifts'); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <?php echo $form->field($model, 'name') ?>
        </div>
        <div class="col-sm-6">
            <?php echo $form->field($model, 'email') ?>
        </div>
    </div>

  
    <div class="row">
        <div class="col-sm-12">
             <?= $form->field($model, 'phone')->textInput(['placeholder' => 'Phone','maxlength'=> 10])->label('Phone') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Save'), ['name' => 'attender-save','class' => 'login-sumbit']) ?>
    </div>

