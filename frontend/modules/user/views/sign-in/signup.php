<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\UserProfile;
use common\models\Groups;
use common\components\Drspanel;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\SignupForm */

$this->title = Yii::t('frontend', 'Signup');
$baseUrl=Yii::getAlias('@frontendUrl');

$titlelist="'".$baseUrl."/site/prefix-title'";

$sendOtp="'".$baseUrl."/ajax-signup'";


if(empty($titleList)){ 
    $hidebox="
    $('#title_list').hide();
    $('#gender_list').hide();
    $('#signup_dob').hide();
    ";
    $this->registerJs($hidebox, \yii\web\VIEW::POS_END); 
}
$js="
$('#otp_box').hide();
//$('#title_list').hide();
//$('#gender_list').hide();
//$('#signup_dob').hide();

$('#signup_from').on('click',function(){
    type=$('#signup_user_type').val();
    var err=0;
    if(type==''){
        $('.field-signup_user_type').addClass('has-error');
        $('#signup_user_type').next('.help-block').addClass('error').text('Register type cannot be blank.');
    }
    if(type!=5 && type!=''){       
        $('#gender_list').show();
        $('#signup_dob').show();
        $('#title_list').show();
        prefix=$('#signup_user_prefix').val();
        gender=$('#signup_user_gender').val();
        dob=$('#signup_dob_text').val();
         if (prefix=='') {
            $('.field-signup_user_prefix').addClass('has-error');
            $('#signup_user_prefix').next('.help-block').addClass('error').text(' Title cannot be blank.');
        err=1;
        } else {
            $('#signup_user_prefix').next('.help-block').removeClass('error').text('');   
        }

        if (gender=='') {
            $('.field-signup_user_gender').addClass('has-error');
            $('#signup_user_gender').next('.help-block').addClass('error').text('Gender cannot be blank.');
        err=1;
        } else {
            $('#signup_user_gender').next('.help-block').removeClass('error').text('');   
        }

        if (dob=='') {
            $('.field-signup_dob_text').addClass('has-error');
            $('#signup_user_dob').next('.help-block').addClass('error').text('Dob cannot be blank.');
        err=1;
        } else {
            $('#signup_dob_text').next('.help-block').removeClass('error').text('');   
        }
    }else{
        $('#gender_list').hide();
        $('#signup_dob').hide();
        $('#title_list').hide();
        name=$('#signupform-name').val();
        phone=$('#signupform-phone').val();
        email=$('#signupform-email').val();
        if (name=='') {
            $('.field-signupform-name').addClass('has-error');
            $('#signupform-name').next('.help-block').addClass('error').text('Name cannot be blank.');
        err=1;
        } else {
            $('#signupform-name').next('.help-block').removeClass('error').text('');   
        }
        if (phone=='') {
            $('.field-signupform-phone').addClass('has-error');
            $('#signupform-phone').next('.help-block').addClass('error').text('Phone cannot be blank.');
        err=1;
        } else {
            $('#signupform-phone').next('.help-block').removeClass('error').text('');   
        }
        if (email=='') {
            $('.field-signupform-email').addClass('has-error');
            $('#signupform-email').next('.help-block').addClass('error').text('Email cannot be blank.');
        err=1;
        } else {
            $('#signupform-email').next('.help-block').removeClass('error').text('');   
        }
    }

     if(err==1){
            return false;
        }else{
            /*
            $.ajax({
              method: 'POST',
              url: $sendOtp,
              data: $('#form-signup-page').serialize(),
        })
          .done(function( msg ) { 
            if(msg){
            $('#otp_box').show();
            }
          }); */


            return true;
        }
})

$('#signup_user_type').on('change', function () {
    type=$(this).val();
    if(type!='5'){
    $('#gender_list').show();
    $('#signup_dob').show();
        $.ajax({
              method: 'POST',
              url: $titlelist,
              data: { type: type}
        })
          .done(function( msg ) { 
            if(msg){
            $('#title_list').show();
            $('#signup_user_prefix').html('');
            $('#signup_user_prefix').html(msg);
            }
          });
    }else{
        $('#gender_list').hide();
        $('#title_list').hide();
        $('#signup_dob').hide();
        $('#signup_user_prefix').html('');
    }

   
       });
";
$this->registerJs($js, \yii\web\VIEW::POS_END); 


?>

 <section class="mid-content-part">
        <div class="signup-part">
            <div class="container">
                <div class="row">
                    <div class="col-md-9">
                        <h2 class="display-6 lg_pb_30">Create Account</h2>
                        <div class="row">
                            <?php $form = ActiveForm::begin(['id' => 'form-signup-page','enableAjaxValidation' => true]); ?>
                                 <div class="col-md-6">
                                    <?php echo $form->field($model, 'groupid')->dropDownList(Groups::allgroups(),['class'=>'input_field','prompt' => 'Register Type','id'=>'signup_user_type'])->label(false); ?>
                                </div>
                                <div id="title_list" class="col-md-6">
                                <?php echo $form->field($model, 'prefix')->dropDownList([$titleList],['class'=>'input_field','prompt' => 'Title','id'=>'signup_user_prefix'])->label(false); ?>
                                </div>
                                <div class="col-md-6">
                                <?php echo $form->field($model, 'name')->textInput(['class'=>'input_field','placeholder'=>'Full Name'])->label(false); ?>
                                </div> 
                                <div class="col-md-6">
                                    <?php echo $form->field($model, 'email')->textInput(['class'=>'input_field','placeholder'=>'Email'])->label(false); ?>
                                    
                                </div>

                                <div class="col-md-6">
                                	<?php echo $form->field($model, 'phone')->textInput(['class'=>'input_field','placeholder'=>'Mobile Number'])->label(false) ?>
                                </div>

                                <div id="gender_list" class="col-md-6">
                                <?php 
                                $genderList = Drspanel::getGenderList();
                                echo $form->field($model, 'gender')->dropDownList($genderList,['class'=>'input_field','prompt' => 'Gender','id'=>'signup_user_gender'])->label(false); ?>
                                </div>

                                <div class="col-md-6" id="signup_dob">
                                      
                                        <?= $form->field($model, 'dob')->textInput([])->widget(
                                            DatePicker::className(), [
                                            'convertFormat' => true,
                                            'type' => DatePicker::TYPE_INPUT,
                                            'options' => ['class'=>'selectpicker','id'=>'signup_dob_text','placeholder' => 'Date of Birth'],
                                            'layout'=>'{input}{picker}',
                                            'pluginOptions' => [
                                                'autoclose'=>true,
                                                'format' => 'yyyy-MM-dd',
                                                'endDate' => date('Y-m-d'),
                                                'todayHighlight' => true
                                            ],])->label(false); ?>
     
                                </div>
                                
                                <div class="col-md-6" id="otp_box">
                                     <?php echo $form->field($model, 'otp')->textInput(['class'=>'input_field','placeholder'=>'enter otp'])->label(false); ?>
                                </div> 


                                <div class="clearfix"></div>
                                <div class="col-md-12 text-center">
                                <?php echo Html::submitButton(Yii::t('frontend', 'Register'), ['id'=>'signup_from','class' => 'submit_btn', 'name' => 'signup-button']) ?>
                                </div>
                          <?php ActiveForm::end(); ?>
                 </div>
                    </div>
                    <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
                </div>
            </div>
        </div>
    </section>


                      
  