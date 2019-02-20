<?php

use common\models\UserEducations;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
$js="
$('#usereducations-collage_name').keypress(function (e) {
  var regex = new RegExp('^[a-zA-Z0-9- ]+$');
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (regex.test(str)) {
    return true;
  }
  else{
    e.preventDefault();
    var ne=1;
    return false;
  }
});
$('#usereducations-education').keypress(function (e) {
  var regex = new RegExp('^[a-zA-Z0-9- ]+$');
  var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
  if (regex.test(str)) {
    return true;
  }
  else
  {
    e.preventDefault();
    var ne=1;
    return false;
  }
});
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>


<div class="edu-form">

    <?php echo $form->field($model, 'collage_name')->textInput(['placeholder'=> 'College Name'])->label(false) ?>
    <?php echo  $form->field($model, 'education')->widget(Select2::classname(),
        [
            'data' => $degreelist,
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select a degree ...', 'multiple' => false],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', ' '],
                'maximumInputLength' => 10,
                'allowClear' => true,
                'multiple' => false,
            ],
        ])->label(false); ?>


  <?php $years = array_combine(range(date("Y"), 1910), range(date("Y"), 1910));
  $startyears=$years;
  $next_year=date('Y', strtotime('+1 year'));
  $tillnow=array($next_year=>"Till Now");
  $endyears=  $tillnow + $startyears;
  ?>
  <?= $form->field($model, 'start')->dropDownList($startyears,['prompt'=>'Select Start Year','class' => 'selectpicker','placeholder'=> 'Start Year'])->label(false);  ?>

  <?= $form->field($model, 'end')->dropDownList($endyears,['prompt'=>'Select End Year','class' => 'selectpicker','placeholder'=> 'End Year'])->label(false);  ?>

  <?php echo Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'login-sumbit', 'id'=>"edu_form_btn", 'name' => 'signup-button']) ?>

</div>
