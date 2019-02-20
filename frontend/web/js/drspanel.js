$(function () {
                



$('.modal-call').on('click',function(){
	var thisId=$(this).attr('id');
	var modalId=thisId.split('-');
	$('#'+modalId[0]+'-modal').modal({backdrop: 'static',keyboard: false,show: true})
});

$('.reg_on_change').on('change',function(){

	var thisId=$(this).attr('id');
	var value=$(this).val();
	if(value==''){
		$('.field-'+thisId).addClass('has-error required');
		$('#'+thisId).attr('aria-invalid',true);
		$('#'+thisId).next('.help-block').addClass('error').text('This field cannot be blank.');
	}else{
		$('.field-'+thisId).removeClass('has-error required');
		$('#'+thisId).removeAttr('aria-invalid');
		$('#'+thisId).next('.help-block').removeClass('error').text('');  
	}
});


$('.email_on_change').on('change',function(){

	var thisId=$(this).attr('id');
	var value=$(this).val();
	ValidateEmail(value);
	if(value==''){
		$('.field-'+thisId).addClass('has-error required');
		$('#'+thisId).attr('aria-invalid',true);
		$('#'+thisId).next('.help-block').addClass('error').text('This field cannot be blank.');
	}else{
		$('.field-'+thisId).removeClass('has-error required');
		$('#'+thisId).removeAttr('aria-invalid');
		$('#'+thisId).next('.help-block').removeClass('error').text('');  
	}
});


$('.check-unique').on('change',function(){

	var thisId=$(this).attr('id');
	var splitIds=thisId.split('-');
	var value=$(this).val();
	var groupId='#'+splitIds[0]+'-groupid';
	var groupid=$(groupId).val();
	if(groupid==''){
		$('.field-'+splitIds[0]+'-groupid').addClass('has-error required');
		$(groupId).attr('aria-invalid',true);
		$(groupId).next('.help-block').addClass('error').text('This field cannot be blank.');
	}else{
		$('.field-'+splitIds[0]+'-groupid').removeClass('has-error required');
		$(groupId).removeAttr('aria-invalid');
		$(groupId).next('.help-block').removeClass('error').text('');
	}
	if(value==''){
		$('.field-'+thisId).addClass('has-error required');
		$('#'+thisId).attr('aria-invalid',true);
		$('#'+thisId).next('.help-block').addClass('error').text('This field cannot be blank.');
		
	}else{

		$('.field-'+thisId).removeClass('has-error required');
		$('#'+thisId).removeAttr('aria-invalid');
		$('#'+thisId).next('.help-block').removeClass('error').text('');
		

	}
});


// signup form

 



});

//reminder time
$('.reminder_time_check').timepicker({defaultTime: '08:00 A'});

// Shift time picker 
$('.addscheduleform-start_time').timepicker({defaultTime: '08:00 A'});
$('.addscheduleform-end_time').timepicker({defaultTime: '12:00 P'});



// Shift time picker 
// $('.editscheduleform-start_time').timepicker({defaultTime: '08:00 A'});
// $('.editscheduleform-end_time').timepicker({defaultTime: '12:00 P'});


function signupValidate(formId,baseUrl){ 
	error=true;
 	form=$('#'+formId).serializeArray();
 	var blankGroup=[];
 	var groupid=$('input[type=radio].groupid_change:checked').val();
 	var i=0;
$.each($('#'+formId).serializeArray(), function() { 
	var name=this.name
	var value=this.value;
	var name=name.split('[');
	if(typeof name[1] !== 'undefined'){
		name=(name[1].slice(0,-1));
		if(value==''){
			blankGroup[i]=name;
			$('.field-'+formId+'-'+name).addClass('has-error required');
			$('#'+formId+'-'+name).attr('aria-invalid',true);
			$('#'+formId+'-'+name).next('.help-block').addClass('error').text('This field cannot be blank.');
			i++;		
		}else{

			
			$('.field-'+formId+'-'+name).removeClass('has-error required');
			$('#'+formId+'-'+name).removeAttr('aria-invalid');
			$('#'+formId+'-'+name).next('.help-block').removeClass('error').text('');	
			if(name=='email'){
				if(!ValidateEmail(value)){
					blankGroup[i]=name;
				$('.field-'+formId+'-'+name).addClass('has-error required');
				$('#'+formId+'-'+name).attr('aria-invalid',true);
				$('#'+formId+'-'+name).next('.help-block').addClass('error').text('You have entered an invalid email address!');					
				i++;
				}
			}

			if(name=='phone'){
				if(!phonenumber(value)){
					blankGroup[i]=name;
				$('.field-'+formId+'-'+name).addClass('has-error required');
				$('#'+formId+'-'+name).attr('aria-invalid',true);
				$('#'+formId+'-'+name).next('.help-block').addClass('error').text('Please enter 10 digits number!');					
				i++;
				}
			}
		}
	}

});

if(groupid==4 || groupid==3){
	$('#gender_list').show();
	$('#signup_dob').show();
	$('#title_list').show();
	if((jQuery.inArray("dob", blankGroup) !== -1) || (jQuery.inArray("title", blankGroup) !== -1)) {
		error=true;
	}else{
		error= false;
	}

}else if(groupid==5){
	$('#gender_list').hide();
	$('#signup_dob').hide();
	$('#title_list').hide();
	if((jQuery.inArray("dob", blankGroup) !== -1) || (jQuery.inArray("title", blankGroup) !== -1)) {
		error= false;
	}else{
		error= true;
	}
}

if(error==false){

		$.ajax({
	 		method: 'POST',
	 		url: baseUrl+'/ajax-unique',
	 		data: $('#'+formId).serialize(),
	 	})
	 	.done(function( resJson ) { 
	 		if(resJson){
	 			var obj = jQuery.parseJSON(resJson);
	 			if(obj.email && obj.phone){
	 				$('.field-'+formId+'-'+'email').addClass('has-error required');
					$('#'+formId+'-'+'email').attr('aria-invalid',true);
					$('#'+formId+'-'+'email').next('.help-block').addClass('error').text('This email address already exists.');

					$('.field-'+formId+'-'+'phone').addClass('has-error required');
					$('#'+formId+'-'+'phone').attr('aria-invalid',true);
					$('#'+formId+'-'+'phone').next('.help-block').addClass('error').text('This phone number already exists.');
	 				error=true;
	 			}else if(obj.email){
	 				$('.field-'+formId+'-'+'email').addClass('has-error required');
					$('#'+formId+'-'+'email').attr('aria-invalid',true);
					$('#'+formId+'-'+'email').next('.help-block').addClass('error').text('This email address already exists.');
	 				error=true;
	 			}else if(obj.phone){
	 				$('.field-'+formId+'-'+'phone').addClass('has-error required');
					$('#'+formId+'-'+'phone').attr('aria-invalid',true);
					$('#'+formId+'-'+'phone').next('.help-block').addClass('error').text('This phone number already exists.');
	 			error=true;
	 			}
		 			
	 			}
	 		
	 	});
	 	return error;

}else{
	return true
}


 }





$('.ten_number').on('keyup',function(){
	var thisId=$(this).attr('id');
	var value=$(this).val();
	if(value==''){
		$('.field-'+thisId).addClass('has-error required');
		$('#'+thisId).attr('aria-invalid',true);
		$('#'+thisId).next('.help-block').addClass('error').text('Mobile number cannot be blank.');
	}else{

		if(!phonenumber(value)){
			$('.field-'+thisId).addClass('has-error required');
			$('#'+thisId).attr('aria-invalid',true);
			$('#'+thisId).next('.help-block').addClass('error').text('Please enter 10 digits number!');
		}
        else{
            $('.field-'+thisId).removeClass('has-error');
            $('#'+thisId).removeAttr('aria-invalid');
            $('#'+thisId).next('.help-block').removeClass('error').text('');
        }
		  
	}
});


function ValidateEmail(mail) 
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail))
  {
    return true;
  }
    return false;
}

function phonenumber(inputtxt)
{
  var phoneno = /^\d{10}$/;
  if((inputtxt.match(phoneno))) {
      return true;
  }
  else {
	  return false;
  }
}

// $("#sidebar_btn").mouseover(function() {
// $(this).show();
// })
// $("#sidebar-wrapper").mouseleave(function() {
// $(this).hide();
// });

function checkGroupUniqueNum(formId,baseUrl,id,groupid)
 {
    var phone=$('#'+formId+'-phone').val();
    //if(!$('#'+formId+'-phone').val().match('[0-9]{10}')){ 
    if(phone!=''){
    if(phone.match('[0-9]{10}')){
	 	$.ajax({
		 		method: 'POST',
		 		url: baseUrl+'/ajax-unique-group-number',
		 		data: {phone:phone,id:id,groupid:groupid},
		 	})
		 	.done(function( res ) { 

		 		if(res!=''){
		 			$('.field-'+formId+'-phone').addClass('has-error required');
					$('#'+formId+'-phone').attr('aria-invalid',true);
					$('#'+formId+'-phone').next('.help-block').addClass('error').text('This phone number already exists.');
		 			
		 		}

		 	});
		} else{
			$('.field-'+formId+'-phone').addClass('has-error required');
			$('#'+formId+'-phone').attr('aria-invalid',true);
			$('#'+formId+'-phone').next('.help-block').addClass('error').text('phone number exactly 10 digits.');
			
			}
	}/*
	else{
		$('.field-'+formId+'-phone').addClass('has-error required');
		$('#'+formId+'-phone').attr('aria-invalid',true);
		$('#'+formId+'-phone').next('.help-block').addClass('error').text('phone number exactly 10 digits.');
	} */
 }
// $(document).on('submit','#shiftform',function(event)
// {
// 	function GetHours(d) {
// 		var h = parseInt(d.split(':')[0]);
// 		if (d.split(':')[1].split(' ')[1] == "PM") {
// 			h = h + 12;
// 		}
// 		return h;
// 	}
// 	function GetMinutes(d) {
// 		return parseInt(d.split(':')[1].split(' ')[0]);
// 	}
// 	var dataArray = $("#shiftform").serializeArray();

// 	var startTimeId = 'addscheduleform-start_time';

// 	var endTimeId = 'addscheduleform-end_time';

// 	var  weekdayId = 'AddScheduleForm[weekday]';

// 	for (i=0; i<=ShiftCount; i++) {
// 		if(i==0)
// 		{
// 			var startTimeId = startTimeId;
// 			var endTimeId = endTimeId;
// 		}else{
// 			var startTimeId = startTimeId+'-'+i;
// 			var endTimeId = endTimeId+'-'+i;
// 		}
// 		if(startTimeId!='undefined' || endTimeId!= 'undefined')
// 		{
// 			var strStartTime = $('#'+startTimeId).val();
// 			var strEndTime = $('#'+endTimeId).val();
// 		}

// 		var startTime = new Date().setHours(GetHours(strStartTime), GetMinutes(strEndTime), 0);
// 		var endTime = new Date(startTime);
// 		endTime = endTime.setHours(GetHours(strEndTime), GetMinutes(strEndTime), 0);

// 		if (startTime > endTime) {
// 			$('.field-'+startTimeId).addClass('has-error required');
// 			$('#'+startTimeId).attr('aria-invalid',true);
// 			$('#'+startTimeId).next('.help-block').addClass('error').text('Start Time is greater than end time');
// 		}
// 		else if (startTime == endTime) {
// 			$('.field-'+startTimeId).addClass('has-error required');
// 			$('#'+startTimeId).attr('aria-invalid',true);
// 			$('#'+startTimeId).next('.help-block').addClass('error').text('Start Time equals end time');
// 		}
// 		else if (startTime > endTime) {
// 			$('.field-'+startTimeId).addClass('has-error required');
// 			$('#'+startTimeId).attr('aria-invalid',true);
// 			$('#'+startTimeId).next('.help-block').addClass('error').text('Start Time is less than end time');
// 		}
// 		else {
// 			$('.field-'+startTimeId).removeClass('has-error');
// 			$('#'+startTimeId).attr('aria-invalid',false);
// 			$('#'+startTimeId).next('.help-block').removeClass('error').text('');
// 		}

// 	}
// 	// event.preventDefault();
// 	// return false;
// });
  $(document).on('click','.remove_shiftbox_div',function(){
	  parent_id = $(this).parent().parent().attr('id');
      var idparent = parent_id.split('_');
      total = parseInt(idparent[2]) - parseInt(1);
      removeValidationRules('shiftform',total);
      $(this).parent().parent().remove();
      ShiftCount--;
  });