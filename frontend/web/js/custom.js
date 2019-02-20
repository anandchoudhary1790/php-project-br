// Slider Js
var baseurl=$('#uribase').val();

$('.fade1').slick({
		  dots: false,
		  infinite: true,
		  fade: true,
		  arrows:true,
		  cssEase: 'linear',
		  autoplay: true,
		  autoplaySpeed: 2000,
		  
		});
		
		
		// Category Js
		
		$('.responsive').slick({
		  dots: false,
		  infinite: true,
		  speed: 300,
		  slidesToShow:3,
		  slidesToScroll:3,
		  responsive: [
			{
			  breakpoint: 1024,
			  settings: {
				slidesToShow: 3,
				slidesToScroll:3,
				infinite: true,
				dots: true
			  }
			},
			{
			  breakpoint: 600,
			  settings: {
				slidesToShow: 2,
				slidesToScroll:2
			  }
			},
			{
			  breakpoint: 480,
			  settings: {
				slidesToShow: 1,
				slidesToScroll:1
			  }
			}
			// You can unslick at a given breakpoint now by adding:
			// settings: "unslick"
			// instead of a settings object
		  ]
		});


	var swiper = new Swiper('.swiper-container', {
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
		
		
      },
    });
$('.multiple-items').slick({
  infinite: false,
  slidesToShow: 5,
  slidesToScroll: 1
});

$('.appointment-time-slider').slick({
  dots:false,
  infinite: false,
  speed: 300,
  slidesToShow: 1,
  adaptiveHeight:false,

});

/*var slideno = 1;
$('.appointment-time-slider').slick('slickGoTo', slideno - 1);
$('.appointment-time-slider .slick-prev').css('display','none');*/

$('.shift_slots').slick({
    dots:false,
    infinite: false,
    slidesToShow: 3,
    adaptiveHeight:false,
    responsive: [
  {
    breakpoint: 1024,
    settings: {
        slidesToShow: 3,
        slidesToScroll:3,
        infinite: true,
        dots: true
    }
},
{
    breakpoint: 600,
    settings: {
        slidesToShow: 3,
        slidesToScroll:2
    }
},
{
    breakpoint: 480,
    settings: {
        slidesToShow: 1,
        slidesToScroll:1
    }
}
// You can unslick at a given breakpoint now by adding:
// settings: "unslick"
// instead of a settings object
]
});


$(document).on('click','#appointment-date',function(){
    date=$('#appointment-date').val();
});

function bookingsDate(date,type,userType,doctor_id){
    var key = new Date(date).getTime() / 1000;
    $.ajax({
        url: baseurl + '/'+ userType +'/get-next-slots',
        dataType: 'html',
        method: 'POST',
        data: { type:type,user_id :doctor_id,key:key,plus:0,operator:'+'},
        beforeSend: function() {
            $('#appointment_date_select').css('opacity','0.8');
            $('#main-js-preloader').css('display','block');
        },
        success: function (response) {
            var obj = jQuery.parseJSON(response);
            if(obj.status){
                $('#appointment_date_select').css('opacity','1');
                $('#appointment_date_select').html('');
                $('#appointment_date_select').append(obj.result);
                getShiftSlots(doctor_id,userType,type,key,0,'+');
                $('#main-js-preloader').css('display','none');
            }
        }
    });
}

function historyDate(date,type,userType,doctor_id){
    var key = new Date(date).getTime() / 1000;
    $.ajax({
        url: baseurl + '/'+ userType +'/get-next-slots',
        dataType: 'html',
        method: 'POST',
        data: { type:type,user_id :doctor_id,key:key,plus:0,operator:'+'},
        beforeSend: function() {
            $('#appointment_date_select').css('opacity','0.8');
            $('#main-js-preloader').css('display','block');
        },
        success: function (response) {
            var obj = jQuery.parseJSON(response);
            if(obj.status){
                $('#appointment_date_select').css('opacity','1');
                $('#appointment_date_select').html('');
                $('#appointment_date_select').append(obj.result);
                getShiftSlots(doctor_id,userType,type,key,0,'+');
                $('#main-js-preloader').css('display','none');
            }
        }
    });
}

$(document).on('click','.next_slot_calender',function(){
    var data=this.id;
    var splitid=data.split("_");
    var user_id=splitid[1];
    var key=splitid[2];
    var type=$(this).attr('data-type');
    var userType=$(this).attr('data-userType');
    $.ajax({
        url: baseurl + '/'+ userType +'/get-next-slots',
        dataType: 'html',
        method: 'POST',
        data: { type:type, user_id :user_id,key:key,plus:1,operator:'+'},
        beforeSend: function() {
            $('#appointment_date_select').css('opacity','0.8');
            $('#main-js-preloader').css('display','block');
        },
        success: function (response) {
            var obj = jQuery.parseJSON(response);
            if(obj.status){
                $('#appointment_date_select').css('opacity','1');
                $('#appointment_date_select').html('');
                $('#appointment_date_select').append(obj.result);
                getShiftSlots(user_id,userType,type,key,1,'+');
                $('#main-js-preloader').css('display','none');
                $('#appointment-date').val(obj.date);
            }

        }
    });
});

$(document).on('click','.prev_slot_calender',function(){
    var data=this.id;
    var splitid=data.split("_");
    var user_id=splitid[1];
    var key=splitid[2];
    var type=$(this).attr('data-type');
    var userType=$(this).attr('data-userType');
    $.ajax({
        url: baseurl + '/'+ userType +'/get-next-slots',
        dataType: 'html',
        method: 'POST',
        data: { type:type,user_id :user_id,key:key,plus:1,operator:'-'},
        beforeSend: function() {
            $('#appointment_date_select').css('opacity','0.8');
            $('#main-js-preloader').css('display','block');
        },
        success: function (response) {
            var obj = jQuery.parseJSON(response);
            if(obj.status){
                $('#appointment_date_select').css('opacity','1');
                $('#appointment_date_select').html('');
                $('#appointment_date_select').append(obj.result);
                getShiftSlots(user_id,userType,type,key,1,'-');
                $('#main-js-preloader').css('display','none');
                $('#appointment-date').val(obj.date);
            }
        }
    });
});

function getShiftSlots(doctor_id,userType,type,date,plus,operator){
    if(type == 'shifts'){
        var action = 'ajax-address-list';
        $.ajax({
            url: baseurl + '/'+ userType +'/'+action,
            dataType: 'html',
            method: 'POST',
            data: { user_id:doctor_id,type:type,date :date,plus:plus,operator:operator},
            beforeSend: function() {
                $('#appointment_shift_slots').css('opacity','0.8');
            },
            success: function (response) {
                $('#appointment_shift_slots').css('opacity','1');
                $('#appointment_shift_slots').html('');
                $('#appointment_shift_slots').append(response);
            }
        });
    }
    else if(type == 'history'){
        var action = 'ajax-history-content';
        $.ajax({
            url: baseurl + '/'+ userType +'/'+action,
            dataType: 'html',
            method: 'POST',
            data: { user_id:doctor_id,type:type,date :date,plus:plus,operator:operator},
            beforeSend: function() {
                $('#history-content').css('opacity','0.8');
            },
            success: function (response) {
                $('#history-content').css('opacity','1');
                $('#history-content').html('');
                $('#history-content').append(response);
                $('.shift_slots').slick({
                    dots:false,
                    infinite: false,
                    slidesToShow: 3,
                    adaptiveHeight:false
                });
            }
        });
    }
    else if(type == 'user_history'){
        var action = 'ajax-user-statistics-data';
        $.ajax({
            url: baseurl + '/'+ userType +'/'+action,
            dataType: 'html',
            method: 'POST',
            data: { user_id:doctor_id,type:type,date :date,plus:plus,operator:operator},
            beforeSend: function() {
                $('#history-content').css('opacity','0.8');
            },
            success: function (response) {
                $('#history-content').css('opacity','1');
                $('#history-content').html('');
                $('#history-content').append(response);
                $('.shift_slots').slick({
                    dots:false,
                    infinite: false,
                    slidesToShow: 3,
                    adaptiveHeight:false
                });
            }
        });
    }
    else{
        var action = 'get-date-shifts';
        $.ajax({
            url: baseurl + '/'+ userType +'/'+action,
            dataType: 'html',
            method: 'POST',
            data: { user_id:doctor_id,type:type,date :date,plus:plus,operator:operator},
            beforeSend: function() {
                $('#appointment_shift_slots').css('opacity','0.8');
            },
            success: function (response) {
                $('#appointment_shift_slots').css('opacity','1');
                $('#appointment_shift_slots').html('');
                $('#appointment_shift_slots').append(response);
                $('.shift_slots').slick({
                    dots:false,
                    infinite: false,
                    slidesToShow: 3,
                    adaptiveHeight:false
                });
            }
        });
    }


}



new WOW().init();


// left menu

$(document).ready(function () {
    var trigger = $('.hamburger'),
        overlay = $('.overlay'),
       isClosed = false;

    function buttonSwitch() {
        if (isClosed === true) {
            overlay.hide();
            trigger.removeClass('is-open');
            trigger.addClass('is-closed');
            isClosed = false;
        } else {
            overlay.show();
            trigger.removeClass('is-closed');
            trigger.addClass('is-open');
            isClosed = true;
        }
    }

    function buttonSwitchCheck() {
        if($('.hamburger').hasClass('is-open')){
            overlay.hide();
            trigger.removeClass('is-open');
            trigger.addClass('is-closed');
            isClosed = false;
            $('#wrapper').toggleClass('toggled');
        }
    }

    $('body').click(function(evt){
        if(evt.target.id == "sidebar_btn")
            return;
        //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
        if($(evt.target).closest('#wrapper').length)
            return;

        buttonSwitchCheck();

    });

    trigger.click(function () {
        buttonSwitch();
    });

    $('#sidebar_btn').mouseover(function(){
        if($('.hamburger').hasClass('is-closed')){
            $('[data-toggle="offcanvas"]').trigger('click');
        }
    });

    $( "#sidebar-wrapper ul.nav.sidebar-nav" )
        .mouseenter(function() {
            console.log('enter');
        })
        .mouseleave(function() {
            console.log('leave');
            $('.overlay').hide();
            $('.hamburger').removeClass('is-open');
            $('.hamburger').addClass('is-closed');
            isClosed = false;
            $('#wrapper').toggleClass('toggled');
        });


    $('[data-toggle="offcanvas"]').click(function () {
        $('#wrapper').toggleClass('toggled');
    });

    $(document).on('click','.profileimageupload',function(){

        var ids = $(this).attr('data-slug');
        if(typeof ids === "undefined" || ids=='')
        {
          $('#uploadfile').click();
        } else{
            $('#'+ids).click();
        }
    });
});



function readImageURL(input) {
    var fileTypes = ['jpg', 'jpeg', 'png'];
    if (input.files && input.files[0]) {

        var extension = input.files[0].name.split('.').pop().toLowerCase(),  //file extension from input file
            isSuccess = fileTypes.indexOf(extension) > -1;  //is extension in acceptable types

        if (isSuccess) { //yes
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.doc_profile_img img')
                    .attr('src', e.target.result);
                $('.doc_profile_img').css('display','block');
            }
            reader.readAsDataURL(input.files[0]);
        }
        else { //no
            alert('Please upload correct file');
        }
    }
}

function refreshPage(){
    window.location.reload();
}

/****************************Social Sharing popup Js******************************/
$(".ssbp-btn").click(function (a) {
    a.preventDefault();

    var d = {

        href: jQuery(this).attr("href")};

    var e = 575, f = 520, g = (jQuery(window).width() - e) / 2, h = (
        jQuery(window).height() - f) / 2,
        i = "status=1,width=" + e + ",height=" + f + ",top=" + h + ",left=" + g;
    window.open(d.href, "SSBP", i)
});


$(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var f = files[i]
                var reader = new FileReader();
                reader.onload = function(event) {
                    var file = event.target;
                    $("<span class=\"pip\">" +
                        "<img class=\"imageThumb\" src=\"" + event.target.result + "\" title=\"" + file.name + "\"/>" +
                        "<br/><span class=\"remove\">Remove image</span>" +
                        "</span>").appendTo(placeToInsertImagePreview);
                    $(".remove").click(function(){
                        $(this).parent(".pip").remove();
                    });


                   /* $($.parseHTML('<img>')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);*/
                }

                reader.readAsDataURL(f);
               // reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#useraddressimages-image').on('change', function(e) {
        var numItems = $('.address_gallery .address_img_attac').length;
        var files = e.target.files,
            filesLength = files.length;
        var total = numItems + filesLength;
        if(total == 0){
            $('div.address_gallery').removeClass('gallary_images');
        }
        if(total > 8) {
            alert("You can select maximum 8 images");
        }
        else{
            $('div.address_gallery').addClass('gallary_images');
            for (var i = 0; i < filesLength; i++) {
                var f = files[i]
                var fileReader = new FileReader();
                fileReader.onload = (function(e) {
                    var file = e.target;
                    $("<div class=\"address_img_attac\">" +
                        "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
                        "<span class=\"remove\"><i class='fa fa-trash'></i></span>" +
                        "</div>").appendTo("div.address_gallery");
                    $(".remove").click(function(){
                        $(this).parent(".address_img_attac").remove();
                        var numItems = $('.address_gallery .address_img_attac').length;
                        if(numItems == 0){
                            $('div.address_gallery').removeClass('gallary_images');
                        }
                    });

                });
                fileReader.readAsDataURL(f);
            }
            //imagesPreview(this, 'div.address_gallery');
        }
    });

    $(document).on('click', '.address_attachment_upload',function() {
        $('#useraddressimages-image').click();
    });

    $('.schedule_form').on('click', function () {
        var numItems = $('.shift_time_section').length;
        for (var i = 0; i < numItems; i++) {
            if(i == 0){
                var mrg_error = $('.field-addscheduleform-start_time .help-block').text();
                var eve_error = $('.field-addscheduleform-end_time .help-block').text();
            }
            else{
                var mrg_error = $('.field-addscheduleform-start_time-'+i+' .help-block').text();
                var eve_error = $('.field-addscheduleform-end_time-'+i+' .help-block').text();
            }
            if (mrg_error != '' || eve_error != '') {
                return false;
            }
        }
        $('#shiftform').submit();
    });

    $('.schedule_form_edit').on('click', function () {
        var numItems = $('.shift_time_section').length;
        for (var i = 0; i < numItems; i++) {
            var mrg_error = $('.field-addscheduleform-start_time-'+i+' .help-block').text();
            var eve_error = $('.field-addscheduleform-end_time-'+i+' .help-block').text();
            if (mrg_error != '' || eve_error != '') {
                return false;
            }
        }
        $('#shiftform').submit();
    });

    $(document).on('click','.schedule_today_edit', function () {
        var mrg_error = $('.field-addscheduleform-start_time .help-block').text();
        var eve_error = $('.field-addscheduleform-end_time .help-block').text();
        if (mrg_error != '' || eve_error != '') {
            return false;
        }
        $('#shift-update-form').submit();
    });



    
});

function addValidationRules(formid,getId,pagetype){
    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-weekday-'+getId,
        name: 'addscheduleform-weekday-'+getId,
        container: '.field-addscheduleform-weekday-'+getId, //or your class container
        input: '#addscheduleform-weekday-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Select Days cannot be blank."});
        }
    });

    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-start_time-'+getId,
        name: 'addscheduleform-start_time-'+getId,
        container: '.field-addscheduleform-start_time-'+getId, //or your class container
        input: '#addscheduleform-start_time-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "From cannot be blank."});
        }
    });

    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-end_time-'+getId,
        name: 'addscheduleform-end_time-'+getId,
        container: '.field-addscheduleform-end_time-'+getId, //or your cllass container
        input: '#addscheduleform-end_time-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "To cannot be blank."});
        }
    });

    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-appointment_time_duration-'+getId,
        name: 'addscheduleform-appointment_time_duration-'+getId,
        container: '.field-addscheduleform-appointment_time_duration-'+getId, //or your cllass container
        input: '#addscheduleform-appointment_time_duration-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Appointment Time Duration cannot be blank."});
        }
    });

    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-consultation_fees-'+getId,
        name: 'addscheduleform-consultation_fees-'+getId,
        container: '.field-addscheduleform-consultation_fees-'+getId, //or your cllass container
        input: '#addscheduleform-consultation_fees-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Consultancy Fee cannot be blank."});
        }
    });

    $('#'+formid).yiiActiveForm('add', {
        id: 'addscheduleform-emergency_fees-'+getId,
        name: 'addscheduleform-emergency_fees-'+getId,
        container: '.field-addscheduleform-emergency_fees-'+getId, //or your cllass container
        input: '#addscheduleform-emergency_fees-'+getId,
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Emergency Fee cannot be blank."});
        }
    });
}

function removeValidationRules(formid,getId,pagetype){
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-weekday-'+getId);
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-start_time-'+getId);
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-end_time-'+getId);
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-appointment_time_duration-'+getId);
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-consultation_fees-'+getId);
    $('#'+formid).yiiActiveForm('remove', 'addscheduleform-emergency_fees-'+getId);

    $('#'+formid+' .field-addscheduleform-weekday-'+getId).removeClass('has-error');
    $('#'+formid+' .field-addscheduleform-start_time-'+getId).removeClass('has-error');
    $('#'+formid+' .field-addscheduleform-end_time-'+getId).removeClass('has-error');
    $('#'+formid+' .field-addscheduleform-appointment_time_duration-'+getId).removeClass('has-error');
    $('#'+formid+' .field-addscheduleform-consultation_fees-'+getId).removeClass('has-error');
    $('#'+formid+' .field-addscheduleform-emergency_fees-'+getId).removeClass('has-error');

    $('#'+formid+' .field-addscheduleform-weekday-'+getId+' div.help-block').empty();
    $('#'+formid+' .field-addscheduleform-start_time-'+getId+' div.help-block').empty();
    $('#'+formid+' .field-addscheduleform-end_time-'+getId+' div.help-block').empty();
    $('#'+formid+' .field-addscheduleform-appointment_time_duration-'+getId+' div.help-block').empty();
    $('#'+formid+' .field-addscheduleform-consultation_fees-'+getId+' div.help-block').empty();
    $('#'+formid+' .field-addscheduleform-emergency_fees-'+getId+' div.help-block').empty();
}

function feesvalidation(formid,field,getId,value,pagetype){
    var newvalue= value - 1;
    if(getId == '0' && pagetype == 'add'){
       var checkvalue = $('#'+formid+' #addscheduleform-'+field+'_discount').val();
       if(checkvalue > newvalue){
           $('#'+formid+' #addscheduleform-'+field+'_discount').val('');
       }
       $('#'+formid+' #addscheduleform-'+field+'_discount').attr('max',newvalue);
   }
    else if(getId == '0' && pagetype == 'today_timing'){
        var checkvalue = $('#'+formid+' #addscheduleform-'+field+'_discount').val();
        if(checkvalue > newvalue){
            $('#'+formid+' #addscheduleform-'+field+'_discount').val('');
        }
        $('#'+formid+' #addscheduleform-'+field+'_discount').attr('max',newvalue);
    }
   else{
        var checkvalue = $('#'+formid+' #addscheduleform-'+field+'_discount-'+getId).val();
        if(checkvalue > newvalue){
            $('#'+formid+' #addscheduleform-'+field+'_discount-'+getId).val('');
        }
       $('#'+formid+' #addscheduleform-'+field+'_discount-'+getId).attr('max',newvalue);
   }
}

function shiftOneValue(formid,getId,pagetype) {
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    if(getId == '0' && pagetype == 'add'){
        var start_field='#'+formid+' #addscheduleform-start_time';
        var end_field='#'+formid+' #addscheduleform-end_time';
        var duration_field='#'+formid+' #addscheduleform-appointment_time_duration';
        var limit_field='#'+formid+' #addscheduleform-patient_limit';
    }
    else if(getId == '0' && pagetype == 'today_timing'){
        var start_field='#'+formid+' #addscheduleform-start_time';
        var end_field='#'+formid+' #addscheduleform-end_time';
        var duration_field='#'+formid+' #addscheduleform-appointment_time_duration';
        var limit_field='#'+formid+' #addscheduleform-patient_limit';
    }
    else{
        var start_field='#'+formid+' #addscheduleform-start_time-'+getId;
        var end_field='#'+formid+' #addscheduleform-end_time-'+getId;
        var duration_field='#'+formid+' #addscheduleform-appointment_time_duration-'+getId;
        var limit_field='#'+formid+' #addscheduleform-patient_limit-'+getId;
    }

    var start_time = $(start_field).val();
    var end_time =  $(end_field).val();

    var d = new Date();
    var date = monthNames[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    var stt = new Date(date + ' ' + start_time);

    var endt = new Date(date + ' ' + end_time);
    stt = stt.getTime();
    endt = endt.getTime();

    if (stt == endt) {
        $(start_field).next('.help-block').addClass('errortime').text('Start-time must be smaller then End-time.');
        $(end_field).next('.help-block').addClass('errortime').text('End-time must be bigger then Start-time.');
        return false;
    }
    else if (stt > endt) {
        $(start_field).next('.help-block').addClass('errortime').text('Start-time must be smaller then End-time.');
        $(end_field).next('.help-block').addClass('errortime').text('End-time must be bigger then Start-time.');
        return false;
    } else {
        var diff =  (endt- stt)/1000;
        if (diff < 0) return false;
        diff /= 60;
        var finaldiff= Math.abs(Math.round(diff));
        if(finaldiff > 0){
            $(duration_field).attr('max',finaldiff);
            $(limit_field).attr('max',finaldiff);
        }
        $(start_field).next('.help-block').removeClass('errortime').text('');
        $(end_field).next('.help-block').removeClass('errortime').text('');
        maxvalidation(formid,'appointment_time_duration',getId,pagetype);
    }
}

function maxvalidation(formid,field,getId,pagetype){
    if(getId == '0' && pagetype == 'add'){
        var checkvalue = $('#'+formid+' #addscheduleform-'+field).val();
        var checkmax = $('#'+formid+' #addscheduleform-'+field).attr('max');
        var checkmin = $('#'+formid+' #addscheduleform-'+field).attr('min');
        var value = parseInt(checkvalue, 10);
        var max = parseInt(checkmax, 10);
        var min = parseInt(checkmin, 10);

        if (value > max) {
            $('#'+formid+' #addscheduleform-'+field).val(max);
            var diff =  (checkmax)/(max);

        } else if (value < min) {
            $('#'+formid+' #addscheduleform-'+field).val(min);
            var diff =  (checkmax)/(min);
        }
        else{
            var diff =  (checkmax)/(value);
        }
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-patient_limit').val(max);
    }
    else if(getId == '0' && pagetype == 'today_timing'){
        var checkvalue = $('#'+formid+' #addscheduleform-'+field).val();
        var checkmax = $('#'+formid+' #addscheduleform-'+field).attr('max');
        var checkmin = $('#'+formid+' #addscheduleform-'+field).attr('min');
        var value = parseInt(checkvalue, 10);
        var max = parseInt(checkmax, 10);
        var min = parseInt(checkmin, 10);

        if (value > max) {
            $('#'+formid+' #addscheduleform-'+field).val(max);
            var diff =  (checkmax)/(max);

        } else if (value < min) {
            $('#'+formid+' #addscheduleform-'+field).val(min);
            var diff =  (checkmax)/(min);
        }
        else{
            var diff =  (checkmax)/(value);
        }
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-patient_limit').val(max);
    }
    else{
        var checkvalue = $('#'+formid+' #addscheduleform-'+field+'-'+getId).val();
        var checkmax = $('#'+formid+' #addscheduleform-'+field+'-'+getId).attr('max');
        var checkmin = $('#'+formid+' #addscheduleform-'+field+'-'+getId).attr('min');
        var value = parseInt(checkvalue, 10);
        var max = parseInt(checkmax, 10);
        var min = parseInt(checkmin, 10);

        if (value > max) {
            $('#'+formid+' #addscheduleform-'+field+'-'+getId).val(max);
            var diff =  (checkmax)/(max);

        } else if (value < min) {
            $('#'+formid+' #addscheduleform-'+field+'-'+getId).val(min);
            var diff =  (checkmax)/(min);
        }
        else{
            var diff =  (checkmax)/(value);
        }
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-patient_limit-'+getId).val(max);
        $('#'+formid+' #addscheduleform-patient_limit-'+getId).attr('max',max);
    }
}

function patientcount(formid,field,getId,pagetype){
    if(getId == '0' && pagetype == 'add'){
        var limit = $('#'+formid+' #addscheduleform-'+field).val();
        var maxlimit = $('#'+formid+' #addscheduleform-'+field).attr('max');
        var checkmax = $('#'+formid+' #addscheduleform-appointment_time_duration').attr('max');
        var max = parseInt(checkmax, 10);

        if (limit > maxlimit) {
            $('#'+formid+' #addscheduleform-'+field).val(maxlimit);
            limit = maxlimit;
        }
        var diff =  (max)/(limit);
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-appointment_time_duration').val(max);

    }
    else if(getId == '0' && pagetype == 'today_timing'){
        var limit = $('#'+formid+' #addscheduleform-'+field).val();
        var maxlimit = $('#'+formid+' #addscheduleform-'+field).attr('max');
        var checkmax = $('#'+formid+' #addscheduleform-appointment_time_duration').attr('max');
        var max = parseInt(checkmax, 10);

        if (limit > maxlimit) {
            $('#'+formid+' #addscheduleform-'+field).val(maxlimit);
            limit = maxlimit;
        }
        var diff =  (max)/(limit);
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-appointment_time_duration').val(max);

    }
    else{
        var limit = $('#'+formid+' #addscheduleform-'+field+'-'+getId).val();
        var maxlimit = $('#'+formid+' #addscheduleform-'+field+'-'+getId).attr('max');
        var checkmax = $('#'+formid+' #addscheduleform-appointment_time_duration-'+getId).attr('max');
        var max = parseInt(checkmax, 10);

        if (limit > maxlimit) {
            $('#'+formid+' #addscheduleform-'+field+'-'+getId).val(maxlimit);
            limit = maxlimit;
        }
        var diff =  (max)/(limit);
        var max = parseInt(diff, 10);
        $('#'+formid+' #addscheduleform-appointment_time_duration-'+getId).val(max);
    }
}
