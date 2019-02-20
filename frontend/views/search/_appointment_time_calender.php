<?php
use common\models\UserScheduleDay;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
$firstKey=strtotime($dates_range[0]);
$currentdate=strtotime(date('Y-m-d'));

$starttime=date('h:i a',$scheduleDay['start_time']);
$endtime=date('h:i a',$scheduleDay['end_time']);


?>

<style>
    .day_name h3{font-size: 25px;}
    .day_date{font-size:18px;}

</style>

<div class="appointment_calendar clearfix">

    <?php if($firstKey > $currentdate) { ?>
        <div class="cal_prev prev_slot_calender" id="prevdate_<?php echo $doctor_id; ?>_<?php echo $firstKey;?>" data-type="<?php echo $type; ?>" data-userType="<?php echo $userType; ?>">
            <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-1.png" class="img-responsive" alt="img"/>
        </div>
    <?php } else { ?>
        <div class="cal_prev cal_prev_disabled">
            <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-1.png" class="img-responsive" alt="img"/>
        </div>
    <?php } ?>

    <div class="date_calender">
        <?php foreach($dates_range as $datekey){
            $dbstart_time=date('Y-m-d',strtotime($datekey));
            $ostart_time = $dbstart_time.' '.$starttime;
            $oend_time = $dbstart_time.' '.$endtime;
            $startTimeDb = strtotime($ostart_time);
            $endTimeDb = strtotime($oend_time);
            $weekday_date=DrsPanel::getDateWeekDay($dbstart_time);
            $userschedule_day=UserScheduleDay::find()->where(['user_id'=>$doctor_id,'start_time'=> $startTimeDb,'end_time'=>$endTimeDb,'weekday'=>$weekday_date,'booking_closed'=>0])->one();
            ?>
            <div class="calender_date_list">
                <?php if(!empty($userschedule_day)){ ?>
                    <a class="fetch_date_schedule" href="javascript:void(0);" data-doctor_id="<?php echo $doctor_id; ?>" data-schedule_id="<?php echo $userschedule_day->schedule_id?>" data-nextdate="<?php echo $datekey?>">
                        <div class="date-col <?php if(isset($datekey) && isset($date_filter) && ($datekey == $date_filter)) { ?> active <?php }?>">
                            <h5><?php  echo \common\components\DrsPanel::getDateWeekDay($datekey) ?></h5>
                            <p><?php  echo date('d',strtotime($datekey)); ?></p>
                        </div>
                    </a>
                <?php } else { ?>
                    <a class="fetch_date_schedule" href="javascript:void(0);" data-doctor_id="<?php echo $doctor_id; ?>" data-schedule_id="<?php echo $schedule_id; ?>" data-nextdate="<?php echo $datekey?>"><div class="date-col <?php if(isset($datekey) && isset($date_filter) && ($datekey == $date_filter)) { ?> active <?php }?>">
                            <h5><?php  echo \common\components\DrsPanel::getDateWeekDay($datekey) ?></h5>
                            <p><?php  echo date('d',strtotime($datekey)); ?></p>
                        </div>
                    </a>
                <?php }?>
            </div>
        <?php }
        $lastKey=strtotime($datekey);
        ?>
    </div>
    <div class="cal_next next_slot_calender" id="date_<?php echo $doctor_id; ?>_<?php echo $lastKey;?>" data-type="<?php echo $type; ?>" data-userType="<?php echo $userType; ?>">
        <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-2.png" class="img-responsive" alt="img"/>
    </div>
</div>