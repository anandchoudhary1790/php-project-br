 <?php
 use yii\helpers\Html;
 use yii\widgets\ActiveForm;
 use frontend\modules\user\models\LoginFormType;
 use kartik\date\DatePicker;



 $this->title = Yii::t('frontend', 'Login');
 $baseUrl=Yii::getAlias('@frontendUrl');
 //$model->scenario = 'login';
 $scriptbaseUrl="'".$baseUrl."'";
 $titleList=[];

 $titlelist="'".$baseUrl."/site/prefix-title'";

 $signupOtp="'".$baseUrl."/ajax-signup'";

 $login="'".$baseUrl."/login-ajax'";

 if(empty($titleList)){ 
 	$hidebox="
        $('#title_list').hide();
        $('#gender_list').hide();
        $('#signup_dob').hide();
 	";
 	$this->registerJs($hidebox, \yii\web\VIEW::POS_END); 
 }
 $js=" 
 
 window.onload = setValue;   
 function setValue(){
    $('#reg_group_Patient').click(); 
    $('#group_Patient').click(); 
 }
 
 $('#signupform-groupid').on('change', function () {
 	//type=$(this).val();
 	type=$('#signupform-groupid input[type=radio].groupid_change:checked').val();
 	
 	
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
 				$('#signupform-prefix').html('');
 				$('#signupform-prefix').html(msg);
 			}
 		});
 	}else{
 		$('#gender_list').hide();
 		$('#title_list').hide();
 		$('#signup_dob').hide();
 		$('#signup_user_prefix').html('');
 	}

 });

 

 $('#signup_from').on('click',function(){
 	var result=signupValidate('signupform',$scriptbaseUrl);
 	console.log(result);
 	if(result){
 		return false;
 	}else{
 		$.ajax({
            method: 'POST',
            url: $signupOtp,
            data: $('#signupform').serialize(),
        })
        .done(function( resJson ) { 
            if(resJson){ 
               var obj = jQuery.parseJSON(resJson);
               if(obj.status && obj.error){
                  
              }else{
                  $('#signup-modal').modal('hide');
                  $('#otp-verify-form').html('');
                  $('#otp-verify-form').html(obj.data);
                  $('#otp-modal').modal({backdrop: 'static',keyboard: false,show: true})
              }
          }
      });

  }

})

$(document).on('keypress','#loginform-identity', function(e){
    if(e.which == 13){
        e.preventDefault();
        $('#login-submit-btn').click();
    }
});

$(document).on('click','#login-submit-btn',function (e){
    var identity=$('#loginform-identity').val();
    var groupid=$('input[type=radio].groupid_change:checked').val();
    if(identity=='') { 
        $('.field-loginform-identity').addClass('has-error required');
        $('#loginform-identity').attr('aria-invalid',true);
        $('#loginform-identity').next('.help-block').addClass('error').text('Mobile Number cannot be blank.');
        return false;	
    }
    if(!phonenumber(identity)){
        $('.field-loginform-identity').addClass('has-error required');
        $('#loginform-identity').attr('aria-invalid',true);
        $('#loginform-identity').next('.help-block').addClass('error').text('Please enter 10 digits number!');
        return false;
    }    
    if(identity!='' && groupid!=''){        
        $.ajax({
            method: 'POST',
            url: $login,
            data: $('#login-form').serialize(),
        })
        .done(function( resJson ) { 
            if(resJson){
                var obj = jQuery.parseJSON(resJson);
                if(obj.status && obj.error){
                    $('.field-loginform-identity').addClass('has-error required');
                    $('#loginform-identity').attr('aria-invalid',true);
                    $('#loginform-identity').next('.help-block').addClass('error').text(obj.data['identity']);
                }else{                
                    $('#login-modal').modal('hide');
                        $('#otp-verify-form').html('');
                        $('#otp-verify-form').html(obj.data);
                        $('#otp-modal').modal({backdrop: 'static',keyboard: true,show: true})
                      
                    
                }
            }
        }); 
    }else{            
        if(identity==''){
            $('.field-loginform-identity').addClass('has-error required');
            $('#loginform-identity').attr('aria-invalid',true);
            $('#loginform-identity').next('.help-block').addClass('error').text('Mobile Number cannot be blank.');                
        }
        if(groupid==''){
            $('.field-login_user_type').addClass('has-error required');
            $('#login_user_type').attr('aria-invalid',true);
            $('#login_user_type').next('.help-block').addClass('error').text('Login with cannot be blank.');
        }
    }    
})";
$this->registerJs($js, \yii\web\VIEW::POS_END); 


?>
<?php if(Yii::$app->user->isGuest){ ?>

    <?= $this->render('_loginForm.php') ?>
    <?= $this->render('_otpverifyForm.php') ?>
    <?= $this->render('_registrationForm.php') ?>

<?php } ?>


<!-- Model confirm message Sow -->
<div class="login-section ">
    <div id="ConfirmModalShow" class="modal model_opacity" role="dialog">
       <div class="modal-dialog modal_inner_popoup">
          <div class="modal-content">
             <div class="modal-header">
                <h3 id="ConfirmModalHeading"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body" id="ConfirmModalContent">

            </div>
            <div class="modal-footer">
                <button type="button" class="confirm-theme" id="confirm_ok">Ok</button>
                <button type="button" data-dismiss="modal" class="confirm-theme">Cancel</button>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Model confirm message Sow -->
<div class="login-section ">
    <div id="success-modal" class="modal model_opacity" role="dialog">
       <div class="modal-dialog">
          <div class="modal-content">
             <div class="modal-header">
                 <h3 id="SuccessModalHeading"></h3>
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
             </div>
             <div class="modal-body" id="SuccessModalContent">

             </div>
         </div>
     </div>
 </div>
</div>

 <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>