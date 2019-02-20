var baseurl=$('#uribase').val();

function shiftOneValue(formId,fieldID) { /*
    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

    var start_time = $('#'+formId+' #addscheduleform-'+'start_time').val();
    var end_time = $('#'+formId+' #addscheduleform-'+'end_time').val();
    var d = new Date();
    var date = monthNames[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    var stt = new Date(date + ' ' + start_time);

    var endt = new Date(date + ' ' + end_time);
    stt = stt.getTime();
    endt = endt.getTime();
    if (stt > endt) {
        $('#'+formId+' #addscheduleform-start_time').next('.help-block').addClass('errortime').text('Start-time must be smaller then End-time.');
        $('#'+formId+' #addscheduleform-end_time').next('.help-block').addClass('errortime').text('End-time must be bigger then Start-time.');
        return false;
    } else {
        $('#'+formId+' #addscheduleform-start_time').next('.help-block').removeClass('errortime').text('');
        $('#'+formId+' #addscheduleform-end_time').next('.help-block').removeClass('errortime').text('');
    } */
}

$(document).ready(function(){




    $('[data-toggle="tooltip"]').tooltip();

    $('.schedule_form_btn').on('click', function () {
        var formid=$(this).closest('form').attr('id');
        var mrg_error = $('#'+formid+' .field-addscheduleform-shift_one_end .help-block').text();

        if (mrg_error != '' || after_error != '' || eve_error != '') {
            return false;
        } else {
            $('form.schedule-form').submit();
        }
    });

    $('.addscheduleform-shift_one_start').timepicker({defaultTime: '08:00 A'});
    $('.addscheduleform-shift_one_end').timepicker({defaultTime: '12:00 P'});
    $('.addscheduleform-shift_two_start').timepicker({defaultTime: '12:00 P'});
    $('.addscheduleform-shift_two_end').timepicker({ defaultTime: '5:00 P'});
    $('.addscheduleform-shift_three_start').timepicker({defaultTime: '5:00 P'});
    $('.addscheduleform-shift_three_end').timepicker({defaultTime: '10:00 P'});

    $(document).on('change','.add_shift_list',function(){
        var getId=this.id;
        var formid=$(this).closest('form').attr('id');

        if($("#"+formid+" #"+getId).is(":checked")){
            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_address',
                name: getId+'_address',
                container: '.field-'+getId+'_address', //or your cllass container
                input: '#'+getId+'_address',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Hospitals/Clinics cannot be blank."});
                }
            });

            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_patient',
                name: getId+'_patient',
                container: '.field-'+getId+'_patient', //or your cllass container
                input: '#'+getId+'_patient',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Patient Limit cannot be blank."});
                }
            });

            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_cfees',
                name: getId+'_cfees',
                container: '.field-'+getId+'_cfees', //or your cllass container
                input: '#'+getId+'_cfees',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Consultancy Fee cannot be blank."});
                }
            });

            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_cdays',
                name: getId+'_cdays',
                container: '.field-'+getId+'_cdays', //or your cllass container
                input: '#'+getId+'_cdays',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Valid Days cannot be blank."});
                }
            });

            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_efees',
                name: getId+'_efees',
                container: '.field-'+getId+'_efees', //or your cllass container
                input: '#'+getId+'_efees',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Emergency Fee cannot be blank."});
                }
            });

            $('#'+formid+'.schedule-form').yiiActiveForm('add', {
                id: getId+'_edays',
                name: getId+'_edays',
                container: '.field-'+getId+'_edays', //or your cllass container
                input: '#'+getId+'_edays',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Valid Days cannot be blank."});
                }
            });
        }
       else{
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_address');
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_patient');
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_cfees');
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_cdays');
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_efees');
            $('#'+formid+'.schedule-form').yiiActiveForm('remove', getId+'_edays');

            $('#'+formid+'.schedule-form .field-'+getId+'_address').removeClass('has-error');
            $('#'+formid+'.schedule-form .field-'+getId+'_patient').removeClass('has-error');
            $('#'+formid+'.schedule-form .field-'+getId+'_cfees').removeClass('has-error');
            $('#'+formid+'.schedule-form .field-'+getId+'_cdays').removeClass('has-error');
            $('#'+formid+'.schedule-form .field-'+getId+'_efees').removeClass('has-error');
            $('#'+formid+'.schedule-form .field-'+getId+'_edays').removeClass('has-error');

            $('#'+formid+'.schedule-form .field-'+getId+'_address div.help-block').empty();
            $('#'+formid+'.schedule-form .field-'+getId+'_patient div.help-block').empty();
            $('#'+formid+'.schedule-form .field-'+getId+'_cfees div.help-block').empty();
            $('#'+formid+'.schedule-form .field-'+getId+'_cdays div.help-block').empty();
            $('#'+formid+'.schedule-form .field-'+getId+'_efees div.help-block').empty();
            $('#'+formid+'.schedule-form .field-'+getId+'_edays div.help-block').empty();

        }
    });

    $(document).on('change','#admin_rating_type',function(){
        var getValue=this.value;
        if(getValue == 'Admin'){
            $('#admin_rating_number').css('display','block');
            $('#update-rating').yiiActiveForm('add', {
                id: 'adminrating-rating',
                name: 'adminrating-rating',
                container: '.field-adminrating-rating', //or your cllass container
                input: '#adminrating-rating',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {message: "Enter custom rating for doctor."});
                }
            });
        }
        else{
            $('#admin_rating_number').css('display','none');
            $('#update-rating').yiiActiveForm('remove', 'adminrating-rating');
            $('#update-rating .field-adminrating-rating').removeClass('has-error');
            $('#update-rating .field-adminrating-rating p.help-block').empty();
        }

    });

    $(document).on("change", ".booking_fee", function() {
        var sum = 0;
        $(".booking_fee").each(function () {
            sum += +$(this).val();
        });
        $('#booking-fees').val(sum);
        if(sum > 100 || sum < 100){
            $('#update-fee-percent').yiiActiveForm('add', {
                id: 'booking-fees',
                name: 'booking-fees',
                container: '.field-booking-fees', //or your cllass container
                input: '#booking-fees',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {
                        'message': 'Booking total must be 100.'
                    });
                    yii.validation.string(value, messages, {
                        'message': 'Booking total must be string.',
                        'min': 5,
                        'tooShort': 'Booking total must be 100.',
                        'max': 5,
                        'tooLong': 'Booking total must be 1000.',
                    });
                }
            });
        }
        else{
            $('#booking-fees').val('succs');
            $('#update-fee-percent').yiiActiveForm('remove', 'booking-fees');
            $('#update-fee-percent .field-booking-fees').removeClass('has-error');
            $('#update-fee-percent .field-booking-fees p.help-block').empty();
        }
    });

    $(document).on("change", ".cancel_fee", function() {
        var sum = 0;
        $(".cancel_fee").each(function () {
            sum += +$(this).val();
        });
        $('#cancel-fees').val(sum);
        if(sum > 100 || sum < 100){
            $('#update-fee-percent').yiiActiveForm('add', {
                id: 'cancel-fees',
                name: 'cancel-fees',
                container: '.field-cancel-fees', //or your cllass container
                input: '#cancel-fees',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {
                        'message': 'Cancellation total must be 100.'
                    });
                    yii.validation.string(value, messages, {
                        'message': 'Cancellation total must be string.',
                        'min': 5,
                        'tooShort': 'Cancellation total must be 100.',
                        'max': 5,
                        'tooLong': 'Cancellation total must be 1000.',
                    });
                }
            });
        }
        else{
            $('#cancel-fees').val('succs');
            $('#update-fee-percent').yiiActiveForm('remove', 'cancel-fees');
            $('#update-fee-percent .field-cancel-fees').removeClass('has-error');
            $('#update-fee-percent .field-cancel-fees p.help-block').empty();
        }
    });

    $(document).on("change", ".reschedule_fee", function() {
        var sum = 0;
        $(".reschedule_fee").each(function () {
            sum += +$(this).val();
        });
        $('#reschedule-fees').val(sum);
        if(sum > 100 || sum < 100){
            $('#update-fee-percent').yiiActiveForm('add', {
                id: 'reschedule-fees',
                name: 'reschedule-fees',
                container: '.field-reschedule-fees', //or your cllass container
                input: '#reschedule-fees',
                error: '.help-block',  //or your class error
                validate:  function (attribute, value, messages, deferred, $form) {
                    yii.validation.required(value, messages, {
                        'message': 'Reschedule total must be 100.'
                    });
                    yii.validation.string(value, messages, {
                        'message': 'Reschedule total must be string.',
                        'min': 5,
                        'tooShort': 'Reschedule total must be 100.',
                        'max': 5,
                        'tooLong': 'Reschedule total must be 1000.',
                    });
                }
            });
        }
        else{
            $('#reschedule-fees').val('succs');
            $('#update-fee-percent').yiiActiveForm('remove', 'reschedule-fees');
            $('#update-fee-percent .field-reschedule-fees').removeClass('has-error');
            $('#update-fee-percent .field-reschedule-fees p.help-block').empty();
        }
    });

    $(document).on('click','.submit_fee_form',function(){
        $('.booking_fee').change();
        $('.cancel_fee').change();
        $('.reschedule_fee').change();
        $('#update-fee-percent').submit();
    });

    $(document).on('change','#dailypatientlimitform-date',function(){
        $("#main-js-preloader").fadeIn();
        var date_sel=this.value;
        $.ajax({
            url: 'ajax-daily-shift',
            dataType:   'html',
            method:     'POST',
            data: { id:7,date: date_sel},
            success: function(response){
                $('#ajaxLoadShiftDetails').empty();
                $('#ajaxLoadShiftDetails').append(response);
                $("#main-js-preloader").fadeOut();
                $('.addscheduleform-shift_one_start').timepicker({defaultTime: '08:00 A'});
                $('.addscheduleform-shift_one_end').timepicker({defaultTime: '12:00 P'});
                $('.addscheduleform-shift_two_start').timepicker({defaultTime: '12:00 P'});
                $('.addscheduleform-shift_two_end').timepicker({ defaultTime: '5:00 P'});
                $('.addscheduleform-shift_three_start').timepicker({defaultTime: '5:00 P'});
                $('.addscheduleform-shift_three_end').timepicker({defaultTime: '10:00 P'});
            }
        });
    });

    $(document).on('change','#addappointmentform-date',function(){
        $("#main-js-preloader").fadeIn();
        var date_sel=this.value;
        $.ajax({
            url: 'ajax-appointments',
            dataType:   'html',
            method:     'POST',
            data: { id:7,date: date_sel},
            success: function(response){
                $('#ajaxLoadBookingDetails').empty();
                $('#ajaxLoadBookingDetails').append(response);
                $("#main-js-preloader").fadeOut();

            }
        });
    });

});


function addValidationRules(formid,getId){
    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_address',
        name: getId+'_address',
        container: '.field-'+getId+'_address', //or your cllass container
        input: '#'+getId+'_address',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Hospitals/Clinics cannot be blank."});
        }
    });

    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_patient',
        name: getId+'_patient',
        container: '.field-'+getId+'_patient', //or your cllass container
        input: '#'+getId+'_patient',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Patient Limit cannot be blank."});
        }
    });

    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_cfees',
        name: getId+'_cfees',
        container: '.field-'+getId+'_cfees', //or your cllass container
        input: '#'+getId+'_cfees',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Consultancy Fee cannot be blank."});
        }
    });

    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_cdays',
        name: getId+'_cdays',
        container: '.field-'+getId+'_cdays', //or your cllass container
        input: '#'+getId+'_cdays',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Valid Days cannot be blank."});
        }
    });

    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_efees',
        name: getId+'_efees',
        container: '.field-'+getId+'_efees', //or your cllass container
        input: '#'+getId+'_efees',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Emergency Fee cannot be blank."});
        }
    });

    $('#'+formid+'.schedule-form').yiiActiveForm('add', {
        id: getId+'_edays',
        name: getId+'_edays',
        container: '.field-'+getId+'_edays', //or your cllass container
        input: '#'+getId+'_edays',
        error: '.help-block',  //or your class error
        validate:  function (attribute, value, messages, deferred, $form) {
            yii.validation.required(value, messages, {message: "Valid Days cannot be blank."});
        }
    });
}




function updateAddress(id){
    $.ajax({
        url: 'update-address-modal',
        dataType:   'html',
        method:     'POST',
        data: { id: id},
        success: function(response){
            $('#updateaddress').empty();
            $('#updateaddress').append(response);
            $('#updateaddress').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
    });

    
}

$(document).on('click','.remove_shiftbox_div',function(){ 
      $(this).parent().parent().remove();
      ShiftCount--;
  });

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

 $('.addscheduleform-start_time').timepicker({defaultTime: '08:00 A'});
$('.addscheduleform-end_time').timepicker({defaultTime: '12:00 P'});

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