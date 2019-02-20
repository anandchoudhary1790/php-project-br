<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
$loginUser=Yii::$app->user->identity;
$baseUrl= Yii::getAlias('@frontendUrl');
$doctor_id = $doctorProfile->user_id;
$bookeing_confirm=$baseUrl."/get-shift-booking-days";
// address_id,doctor_id,start_time,end_time,next_date
// var urlLink= '".$bookeing_confirm."'"."+bookval;
// echo '<pre>';
// print_r($appointments);die;
$js="
$('.booking_shifts').on('change',function() {
    var checked = $(this).is(':checked');
    bookval = $(this).val();
    getaddress_id = $('#getaddress_id_'+bookval).val();
    getstart_time = $('#getstart_time_'+bookval).val();
    getend_time = $('#getend_time_'+bookval).val();
    getnext_date = $('#getnext_date_'+bookval).val();
    
    if(this.checked){
        $.ajax({
            type: 'POST',
            url: '".$bookeing_confirm."',
            data: {address_id:getaddress_id,doctor_id:$doctor_id,start_time:getstart_time,end_time:getend_time,next_date:getnext_date},
            success: function(data) {
                alert('it worked');
                $('#container').html(data);
            },
        });

    }
});


";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>

<?php if(count($appointments)>0) { foreach ($appointments as $key => $appointment) {  ?>
        
    <div class="morning-parttiming hide">
        <div class="main-todbox">
            <div class="pull-left">
                <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-clock-icon.png" alt="image">
                    <span> <?php echo $appointment['shift_name']; ?></span> </div>
            </div>
        </div>
        <div class="main-todbox no-pd hide">
            <div class="pull-left">
                <div class="moon-cionimg "><img src="<?php /*echo $baseUrl*/?>/images/doctor-profile-icon3.png" alt="image"> <span> <strong class="hospota_add"> <?php /*echo $appointment['name']*/?></strong></span> </div>
            </div>
        </div>
        <div class="main-todbox no-pd">
            <div class="row">
                <div class="col-sm-9">
                    <div class="pull-left">
                        <div class="moon-cionimg">
                            <p><?php echo $appointment['address']?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a href="<?php echo $baseUrl.'/appointment-time/'.$doctorProfile->slug.'/'.$appointment['schedule_id']?>" class="view_pro_appoint new_bookbtn mb-3 mb-3" >Book</a>
    </div>
    <div class="all-listhospital"> 
        <h5 style="font-size: 2.25rem;"><?php echo $appointment['name'];?><span class="price_text pull-right"><i class="fa fa-rupee"></i> <?php echo $appointment['consultation_fees'] ?>/-</span></h5>
        <span>
          <input name="radio-group" id="test<?php echo $appointment['address_id']?>" type="radio" class="booking_shifts" value="<?=$key?>">
          <label for="test<?php echo $appointment['address_id']?>"><?php echo $appointment['address']?></label>
          </span>
           <input name="slug" id="doctor_slug" type="hidden" value="<?=$doctorProfile->slug?>">
            <input name="slug" id="getaddress_id_<?=$key?>" type="hidden" value="<?php echo $appointment['address_id']?>">   
            
            <input name="slug" id="getstart_time_<?=$key?>" type="hidden" value="<?php echo $appointment['start_time']?>">   
            
            <input name="slug" id="getend_time_<?=$key?>" type="hidden" value="<?php echo $appointment['end_time']?>">   
            
            <input name="slug" id="getnext_date_<?=$key?>" type="hidden" value="<?php echo $appointment['next_date']?>">
          <div class="hos-lr-part">
            <div class="pull-left"><i class="fa fa-map-marker" aria-hidden="true"></i> 0.8 km away</div>
            <div class="pull-right"><i class="fa fa-clock"></i> <?php echo $appointment['start_time'].'-'. $appointment['end_time']?> </div>
          </div>
        </div>
        
<?php } }else{?>

    <div class="morning-parttiming">
        <p> You have no any schedules.</p>
    </div>
<?php } ?>