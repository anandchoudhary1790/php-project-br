<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  common\components\DrsPanel ;
$baseUrl= Yii::getAlias('@frontendUrl');
$slot_date=$slot->date;

$getlist="'".$baseUrl."/$userType/booking-confirm-step2'";

$js="
$('.booking_confirm_step1').on('click',function(){
  $('#user_name_div').removeClass('error');
  $('#user_phone_div').removeClass('error'); 
  $('.btdetialpart_error_msg').css('display','none'); 
  var ne=0; var pe=0;var ge=0;
  var name=$('#user_name').val();
  var user_name = $.trim(name);



  if(user_name == ''){
    $('#user_name_div').addClass('error');
    $('#user_name_div_msg').text('Name can not be blank');
    $('#user_name_div_msg').css('display','block'); 
    var ne=1;
  }else
  {
    if(/^[a-zA-Z0-9- ]*$/.test(user_name) == false) {
     $('#user_name_div').addClass('error');
        $('#user_name_div_msg').text('Name contains illegal characters');
        $('#user_name_div_msg').css('display','block');        
         var pe=1;
    }
  }
 
  
  var phone=$('#user_phone').val();
  if(phone == ''){
    $('#user_phone_div').addClass('error');
    $('#user_phone_div_msg').text('Phone number can not be blank');
    $('#user_phone_div_msg').css('display','block'); 
    var pe=1;
  }
  else{
    var ph=/^[+]?([\d]{0,3})?[\(\.\-\s]?([\d]{3})[\)\.\-\s]*([\d]{3})[\.\-\s]?([\d]{4})$/; 
    if(ph.test(phone)==false){
        $('#user_phone_div').addClass('error');
        $('#user_phone_div_msg').text('Invalid Phone Number');
        $('#user_phone_div_msg').css('display','block');        
         var pe=1;
    } 
  }
  
  var gender=$('input[name=gender]:checked').val();
  if(gender == '' || typeof gender == 'undefined'){    
    $('#user_gender_div_msg').text('Gender can not be blank');
    $('#user_gender_div_msg').css('display','block'); 
    var ge=1;
  }
  
  var slot_id=$('#slot_id').val();
  
  if(ne == 1 || pe == 1 || ge == 1){
    return false;
  }
  else{
    $.ajax({
        method:'POST',
        url: $getlist,
        data: {slot_id:slot_id,name:name,phone:phone,gender:gender}
    })
   .done(function( msg ) { 
    if(msg){
      $('#pslotTokenContent').html('');
      $('#pslotTokenContent').html(msg); 
      //$('#patientbookedShowModal').modal({backdrop: 'static',keyboard: false});
    }
    });
  }
  

});

 $('#user_phone').keypress(function (e) {
   var phone=$('#user_phone').val();
    if(phone == ''){
    }
    else{
        $('#user_phone_div').removeClass('error');
        $('#user_phone_div_msg').text('');
        $('#user_phone_div_msg').css('display','none'); 
    }    
  });

$('#user_name').keypress(function (e) {
       // if (e.which === 32 && !this.value.length)
      var regex = new RegExp('^[a-zA-Z0-9- ]+$');
      var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
      if (regex.test(str)) {
        $('#user_name_div').removeClass('error');
        $('#user_name_div_msg').text('');
         $('#user_name_div_msg').css('display','none'); 
        return true;
      }
      else{
          e.preventDefault();
          $('#user_name_div').addClass('error');
          $('#user_name_div_msg').text('Name can not be blank');
          $('#user_name_div_msg').css('display','block'); 
          var ne=1;
          return false;
      }
    });



";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>

<div class="col-md-12 mx-auto">
    <div class="youare-text"> You are Booking an appointment with </div>
    <?php if($userType == 'hospital' || $userType == 'attender') { ?>
        <div class="pace-part main-tow mb-0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="pace-left">
                        <?php $image = DrsPanel::getUserAvator($doctor->id);?>
                        <img src="<?php echo $image; ?>" alt="image"/>
                    </div>
                    <div class="pace-right">
                        <h4><?=$doctor['userProfile']['name']?></h4>
                        <p> <?= $doctor['userProfile']['speciality']; ?></p>
                        <p> <i class="fa fa-calendar"></i> <?php echo date('d M Y',strtotime($slot->date)); ?> <span class="pull-right"> <strong><i class="fa fa-rupee"></i> <?=$slot->fees?></strong></span></p>
                        <p><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $slot->shift_label; ?> </p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="workingpart book-confirm cls-1">
        <input id="slot_date" type="hidden" value="<?php echo $slot->date; ?>">
        <div class="form-group">
            <div class="pull-left">
                <h5> <?php echo $address->address; ?> </h5>
                <p><?php echo DrsPanel::getAddressLine($address); ?> </p>
            </div>
            <div class="pull-right hide">
                <a href="#" data-toggle="modal" data-target="#myModal"> 0.8 km
                    <i class="fa fa-location-arrow"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="workingpart book-confirm cls-2">
        <div class="form-group">
            <div class="pull-left">
                <p> Token <span> <a href="#" class="roundone"> <?php echo $slot->token; ?> </a> </span> </p>
            </div>
            <div class="pull-right">
                <a href="#" class="time-bg">
                    <?php echo date('h:i a',$slot->start_time); ?> - <?php echo date('h:i a',$slot->end_time); ?>
                </a>
            </div>
        </div>
    </div>


    <form class="appoiment-form-part mt-0 mt-nill">
        <div class="btdetialpart mt-0" id="user_name_div">
            <input type="text" id="user_name" name="name" placeholder="Patient Name">
        </div>
        <div class="btdetialpart_error_msg" id="user_name_div_msg" style="display: none;"></div>

        <div class="btdetialpart" id="user_phone_div">
            <input type="text" id="user_phone" name="phone" placeholder="Contact Number" maxlength="10">
        </div>

        <div class="btdetialpart_error_msg" id="user_phone_div_msg" style="display: none;"></div>

        <div class="form-group">
            <label> Gender </label>
            <div class="row">
                <?php $genderList=DrsPanel::getGenderList();
                foreach($genderList as $key=>$gender){ ?>
                    <div class="col-sm-3">
                        <span>
                            <input name="gender" id="<?= $key; ?>" type="radio" value="<?= $key; ?>">
                            <label for="<?= $key; ?>"><?= $gender; ?></label>
                        </span>
                    </div>
                <?php }
                ?>
            </div>
        </div>

        <div class="btdetialpart_error_msg" id="user_gender_div_msg" style="display: none;"></div>

        <div class="btdetialpart">
            <div class="pull-left">
                <button type="button" class="confirm-theme" data-dismiss="modal">Cancel</button>                </div>
            <div class="pull-right text-right">
                <input type="hidden" name="slot_id" id="slot_id" value="<?= $slot->id; ?>"/>
                <button type="button" class="confirm-theme booking_confirm_step1">Confirm Now</button>
            </div>
        </div>
    </form>

</div>