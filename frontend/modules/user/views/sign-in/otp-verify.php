 <?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Groups;
use common\components\DrsPanel;
use frontend\modules\user\models\LoginForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\LoginForm */

$this->title = Yii::t('frontend', 'Otp Verify');
$baseUrl=Yii::getAlias('@frontendUrl');
$model = new LoginForm();
$model->scenario = 'otp';
$model->groupid=$user->groupid;
$model->identity=$user->phone;
$sendOtp="'".$baseUrl."/otp-verify'";

$form = ActiveForm::begin(
   ['id'=>'otp-form','action'=>['sign-in/otp-verify'],
       'enableAjaxValidation' => true,
       'method'=>"post",'class'=>'form-horizontal']);
echo $form->field($model, 'groupid')->hiddenInput()->label(false);
echo $form->field($model, 'identity')->hiddenInput()->label(false);
echo $form->field($model, 'otp')->textInput(['placeholder'=>'Please enter otp'])->label('Enter the 4-digit code send via SMS on '.$user->phone);
echo Html::submitButton(Yii::t('frontend', 'Verify'),
    ['id'=>"otp-submit",'class' => 'login-sumbit', 'name' => 'login-button']);
ActiveForm::end(); ?>