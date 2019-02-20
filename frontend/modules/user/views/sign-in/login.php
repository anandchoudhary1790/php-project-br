<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Groups;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\LoginForm */

$this->title = Yii::t('frontend', 'Login');
$baseUrl=Yii::getAlias('@frontendUrl');
?>

<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8">
          <h2 class="display-6 lg_pb_30">Login</h2>
          <div class="row">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
            <div class="col-md-6">
              <?php echo $form->field($model, 'groupid')->dropDownList(Groups::allgroups(),['class'=>'input_field','prompt' => 'Login with','id'=>'login_user_type'])->label(false); ?>
            </div>
            <div class="col-md-6">
              <?php echo $form->field($model, 'type')->textInput(['class'=>'input_field','placeholder'=>'Phone Number'])->label(false); ?>
            </div>

            <div class="clearfix"></div>
            <div class="col-md-12 text-left">
              <?php echo Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'submit_btn', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
      </div>
    </div>
  </div>
</section>

