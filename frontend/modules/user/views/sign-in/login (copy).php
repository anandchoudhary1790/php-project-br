<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\LoginForm */

$this->title = Yii::t('frontend', 'Login');
//$this->params['breadcrumbs'][] = $this->title;
?>
 <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">
  <section class="mid-content-part">
        <div class="signup-part">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="display-6 lg_pb_30">Login</h2>
                        <div class="row">
                         <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
                                <div class="col-md-12">
                                 <?php echo $form->field($model, 'identity')->textInput(['class'=>'input_field','placeholder'=>'Name/Email Address'])->label(false); ?>
                                </div>
                                <div class="col-md-12">
                                <?php echo $form->field($model, 'password')->passwordInput(['class'=>'input_field','placeholder'=>'Password'])->label(false); ?> 
                                </div>

                                <div class="clearfix"></div>
                                <div class="col-md-12 text-left">
                                 <?php echo Html::submitButton(Yii::t('frontend', 'Login'), ['class' => 'submit_btn', 'name' => 'login-button']) ?>
                                </div>
                             <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mobile_img">
                            <img src="images/mobile_img2.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

