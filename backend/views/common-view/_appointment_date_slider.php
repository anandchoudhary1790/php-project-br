<?php
$firstKey=strtotime($dates_range[0]);
$currentdate=strtotime(date('Y-m-d'));
?>

<style>
    .day_name h3{font-size: 25px;}
    .day_date{font-size:18px;}

</style>

<div class="appointment_calendar clearfix">
    <?php if($type == 'history' || $type == 'user_history') { ?>
        <div class="cal_prev prev_slot_calender" id="prevdate_<?php echo $doctor_id; ?>_<?php echo $firstKey;?>" data-type="<?php echo $type; ?>" data-userType="<?php echo $userType; ?>">
            <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-1.png" class="img-responsive" alt="img"/>
        </div>
    <?php } else { ?>
        <?php if($firstKey > $currentdate) { ?>
            <div class="cal_prev prev_slot_calender" id="prevdate_<?php echo $doctor_id; ?>_<?php echo $firstKey;?>" data-type="<?php echo $type; ?>" data-userType="<?php echo $userType; ?>">
                <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-1.png" class="img-responsive" alt="img"/>
            </div>
        <?php } else { ?>
            <div class="cal_prev cal_prev_disabled">
                <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-1.png" class="img-responsive" alt="img"/>
            </div>
        <?php } ?>
    <?php } ?>
    <ul>
        <?php foreach($dates_range as $datekey){ ?>
            <li>
                <div class="day_blk">
                    <div class="day_name">
                        <?php if($type =='history') { ?>
                            <h3> Patient History</h3>
                        <?php } elseif($type =='user_history') { ?>
                            <h3> User Statistics Data</h3>
                        <?php } elseif($type =='shifts') { ?>
                            <h3>Timing</h3>
                        <?php } else { ?>
                            <h3>Appointments</h3>
                        <?php } ?>
                    </div>
                    <div class="day_date">
                        <?php  echo date('d M Y',strtotime($datekey)); ?>
                    </div>
                </div>
            </li>
        <?php }
        $lastKey=strtotime($datekey);
        ?>
    </ul>
    <div class="cal_next next_slot_calender" id="date_<?php echo $doctor_id; ?>_<?php echo $lastKey;?>" data-type="<?php echo $type; ?>" data-userType="<?php echo $userType; ?>">
        <img src="<?php echo Yii::getAlias('@frontendUrl'); ?>/images/arrow-2.png" class="img-responsive" alt="img"/>
    </div>
</div>