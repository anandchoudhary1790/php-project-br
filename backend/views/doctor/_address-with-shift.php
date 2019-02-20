<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
use common\models\UserScheduleDay;
$loginUser=Yii::$app->user->identity;
$baseUrl= Yii::getAlias('@frontendUrl'); 

$addressUrl="'".$baseUrl."/doctor/get-shift-details'";
 $js="
    $(document).on('click', '.update-shift-modal',function () {
    id=$(this).attr('data-id');
    shift_id=$(this).attr('data-shift');
    address_id=$(this).attr('data-address');
    dates=$('#appointment-date').val();  
    date=new Date(dates).getTime() / 1000  
    $.ajax({
          method: 'POST',
          url: $addressUrl,
          data: { id: id,address_id:address_id,date:date,shift_id:shift_id}
    })
      .done(function( msg ) { 
        if(msg){
        $('#edit-modal-form').html('');
        $('#edit-modal-form').html(msg);
        $('#shiftTimeEdit-modal').modal({backdrop: 'static',keyboard: false,show: true});
        $('.editscheduleform-start_time').timepicker();
        $('.editscheduleform-end_time').timepicker();
        }
      });
  });
"; $this->registerJs($js,\yii\web\VIEW::POS_END); 

if(count($appointments)>0) { 
  foreach ($appointments as $key => $appointment) {
 ?>
<div class="morning-parttiming">
  <div class="main-todbox">
    <div class="pull-left">
            <div class="moon-cionimg "><img src="<?php echo $baseUrl?>/images/doctor-profile-icon3.png" alt="image"> <span> <strong class="hospota_add"> <?php echo DrsPanel::getHospitalName($appointment['address_id'])?></strong> </span>
            </div>
          </div>
      <div class="pull-right">
        <label class="switch ">
          <input type="checkbox" <?php echo ($appointment['booking_closed'] == 0)?'checked':''; ?> id="toggle-<?php echo $appointment['schedule_id']?>">
          <span id="back-color_<?php echo $appointment['schedule_id']; ?>" class="slider-toggle round shift-toggle" data-shift="<?php echo $appointment['schedule_id']; ?>" data-userid="<?php echo $userid?>"></span> 
          </label>
      </div>
      </div>
      <div class="main-todbox no-pd">
        <div class="addressText">
           <div class="pull-left">
                <div class="moon-cionimg">
                  <p><img src="<?php echo $baseUrl ?>/images/shift_address_icon.png?>"><?php echo $appointment['address']?></p>
                </div>
              </div>
          <div class="pull-right">
            <div class="pull-left icon-border"> 
              <a href="javascript:void(0)" ><i class="fa fa-pencil update-shift-modal" aria-hidden="true" data-id="<?php echo $appointment['schedule_id']?>" data-shift="<?php echo $appointment['shift_id']?>" data-address=<?php echo $appointment['address_id']?>></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="main-todbox no-pd">
          <div class="row">
            <div class="col-sm-9 col-8">
              <div class="pull-left">
                <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-clock-icon.png" alt="image">
                  <span> <?php echo $appointment['shift_name']; ?></span> 
                </div>
              </div>
            </div>
            <div class="col-sm-3 col-4">
              <div class="pull-right color-texttheme"> (<?php echo $appointment['patient_limit']; ?>) patient </div>
            </div>
          </div>
        </div>
      </div>
      <?php } }else{?>
      <div class="morning-parttiming">
       <p> You have no any schedules.</p>
     </div>
     <?php } ?>


     <div class="register-section">
      <div class="time_edit_popup">
      <div class="modal fade model_opacity" id="shiftTimeEdit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="myModalContact">Shift Time Update </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
              </div>
              <div class="modal-body" id="edit-modal-form">
              </div>
            </div><!-- /.modal-content -->
          </div>
        </div>
      </div>
    </div>