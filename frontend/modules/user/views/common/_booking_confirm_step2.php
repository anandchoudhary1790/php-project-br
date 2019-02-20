<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  common\components\DrsPanel ;
$baseUrl= Yii::getAlias('@frontendUrl');
$getAppointment="'".$baseUrl."/$userType/get-appointment-detail'";
$js="    
    $(document).on('submit', 'form', function(event){
    event.preventDefault();        
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        dataType: 'JSON',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function (data, status){
            $('li.active a.get-shift-token').click();
            if(data.status == 1){
                $.ajax({
                  method:'POST',
                  url: $getAppointment,
                  data: {appointment_id:data.appointment_id}
                })
               .done(function( json_result ) { 
                    if(json_result){
                      $('#pslotTokenContent').html('');
                      $('#pslotTokenContent').html(json_result); 
                      $('.modal-header').html('<h3 >Booking <span>Detail</span></h3>');
                      $('#patientbookedShowModal').modal({backdrop: 'static',keyboard: false});
                    } 
                }); 
            } 
            else{
                $('#patientbookedShowModal').modal('hide');
                setTimeout(function () {
                    swal({            
                    type:'error',
                    title:'Error!',
                    text:data.message,            
                    timer:5000,
                    confirmButtonColor:'#a42127'
                })},300);            
            }           
            
        },
        error: function (xhr, desc, err){
             $('#patientbookedShowModal').modal('hide');
             setTimeout(function () {
                swal({            
                type:'error',
                title:'Error!',
                text:'Please try again!',            
                timer:3000,
                confirmButtonColor:'#a42127'
            })},300);
        }
    });        
});
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>
<div class="col-md-12 mx-auto">
    <div class="youare-text"> You are Booking an appointment with </div>
    <?php if($userType == 'hospital' || $userType == 'attender') { ?>
        <div class="pace-part main-tow">
            <div class="row">
                <div class="col-sm-12">
                    <div class="pace-left">
                        <?php $image = DrsPanel::getUserAvator($doctor->id);?>
                        <img src="<?php echo $image; ?>" alt="image"/>
                    </div>
                    <div class="pace-right">
                        <h4><?=$doctor['userProfile']['prefix']?> <?=$doctor['userProfile']['name']?></h4>
                        <p> <?= $doctor['userProfile']['speciality']; ?></p>
                        <p> <i class="fa fa-calendar"></i> <?php echo date('d M Y',strtotime($slot->date)); ?> <span class="pull-right"> <strong><i class="fa fa-rupee"></i> <?=$slot->fees?></strong></span></p>
                        <p><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $slot->shift_label; ?> </p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="workingpart">
        <input id="slot_date" type="hidden" value="<?php echo $slot->date; ?>">
        <div class="form-group">
            <div class="pull-left">
                <h5> <?php echo $address->address; ?> </h5>
                <p><?php echo DrsPanel::getAddressLine($address); ?> </p>
            </div>
            <div class="pull-right">
                <a href="#" data-toggle="modal" data-target="#myModal"> 0.8 km
                    <i class="fa fa-location-arrow"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="workingpart">
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

    <?php $form = ActiveForm::begin(
        ['action'=>"$baseUrl/$userType/appointment-booked",
            'id'=>'bookeing-confirm','enableClientValidation'=>true,
            'options' => ['class' => 'appoiment-form-part mt-0 mt-nill']
        ]);
    echo $form->field($model,'doctor_id')->hiddenInput()->label(false);
    echo $form->field($model,'slot_id')->hiddenInput()->label(false);
    echo $form->field($model,'schedule_id')->hiddenInput()->label(false);
    echo $form->field($model,'user_name')->hiddenInput()->label(false);
    echo $form->field($model,'user_phone')->hiddenInput()->label(false);
    echo $form->field($model,'user_gender')->hiddenInput()->label(false);?>

    <div class="btdetialpart">
        <div class="pull-left"><?= $model->user_name; ?></div>
        <div class="pull-right"><?= $model->user_phone; ?> </div>
    </div>
    <div class="btdetialpart">
        <div class="pull-left">Fees</div>
        <div class="pull-right">
            <strong><i class="fa fa-rupee"></i>
                <?php if(isset($slot->fees_discount) && $slot->fees_discount < $slot->fees && $slot->fees_discount > 0) { ?>            <?= $slot->fees_discount?>/- <span class="cut-price"><?= $slot->fees?>/-</span>

                <?php } else { echo $slot->fees.'/-'; } ?>
            </strong> </div>
    </div>

    <div class="form-group">
        <div class="new_confirmbtn m-0">
            <?= Html::submitButton('Confirm Now', ['class' => 'confirm-theme']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
