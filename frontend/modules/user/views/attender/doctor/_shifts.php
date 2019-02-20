<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 
$this->title = Yii::t('frontend','DrsPanel :: Doctor Appoinment'); 

$getToken="'ajax-token'";
$getAppointment="'ajax-appointment'";
$getHistory="'ajax-history-appointment'";
$getUserHistory="'ajax-statistics-data'";
$js="
    $(document).on('click','.get-shift-token', function () {
        shiftid=$(this).attr('data-shift');
        type=$(this).attr('data-type');
        doctorid=$(this).attr('data-doctorid');
        if(type == 'current_appointment'){
            $.ajax({
              method:'POST',
              url: $getAppointment,
              data: {user_id:doctorid,shift_id:$(this).attr('data-shift'),date:$(this).attr('shift-date')}
            })
           .done(function( json_result ) { 
            if(json_result){
              $('.doc-timingslot li').removeClass('active');
              $('#shift-tokens').empty();
              $('#shift-tokens').append(json_result);  
              $('.doc-timingslot li#shiftslot_'+shiftid).addClass('active');         
            } 
            });
        }
        else if(type == 'history'){
            $.ajax({
              method:'POST',
              url: $getHistory,
              data: {user_id:doctorid,shift_id:$(this).attr('data-shift'),date:$(this).attr('shift-date')}
            })
           .done(function( json_result ) { 
            if(json_result){
              $('.doc-timingslot li').removeClass('active');
              $('#shift-tokens').empty();
              $('#shift-tokens').append(json_result);  
              $('.doc-timingslot li#shiftslot_'+shiftid).addClass('active');         
            } 
            });
        }
        else if(type == 'user_history'){
            type='online';
            $.ajax({
              method:'POST',
              url: $getUserHistory,
              data: {user_id:doctorid,shift_id:$(this).attr('data-shift'),date:$(this).attr('shift-date')}
            })
           .done(function( json_result ) {             
                var obj = jQuery.parseJSON(json_result);
                if(obj.status){ 
                $('.doc-timingslot li').removeClass('active');
                $('#statistics-appointments').html('');
                $('#statistics-appointments').html(obj.appointments);
                $('.doc-timingslot li#shiftslot_'+shiftid).addClass('active');  
                }
            });
        }
        else{
            $.ajax({
              method:'POST',
              url: $getToken,
              data: {shift_id:$(this).attr('data-shift'),date:$(this).attr('shift-date'),doctorid:$(this).attr('data-doctorid')}
            })
           .done(function( json_result ) { 
            if(json_result){
              $('.doc-timingslot li').removeClass('active');
              $('#shift-tokens').empty();
              $('#shift-tokens').append(json_result);  
              $('.doc-timingslot li#shiftslot_'+shiftid).addClass('active');         
            } 
            });
        }
        
      
    });
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="shift_slots slider">

<?php
if($shifts){
    foreach ($shifts as $key => $shift) { ?>
        <li id="<?php echo 'shiftslot_'.$shift['schedule_id']; ?>" class="<?php echo ($shift['schedule_id'] == $current_shifts)?'active':''?>">
            <a href="javascript:void(0)" shift-date="<?php echo isset($shift['date'])?$shift['date']:'' ?>" class="get-shift-token" data-shift="<?php echo $shift['schedule_id']; ?>" data-type="<?php echo $type; ?>" data-doctorid="<?php echo $doctor->id; ?>">
                <?php echo $shift['shift_name']; ?>
                <?php if($type  == 'history') { ?>
                    <span><?php echo ucwords($shift['hospital_name']).',';?> <?php echo $shift['city'];?></span>
                <?php } ?>
            </a>
        </li>
    <?php }
}else{ ?>
    <li> Shifts not available for selected date, Please add shift first.</li>
<?php }
?>
</div>
 	