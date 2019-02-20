<?php
use common\components\DrsPanel;
?>
<div class="pace-part main-tow">
    <?php
    echo $this->render('_doctor_detail',['date'=>$date,'doctor'=>$doctor,'scheduleDay'=>$scheduleDay,'schedule'=>$schedule,'slots'=>$slots,'doctorProfile'=>$doctorProfile]);
    ?>
</div>

<div class="col-sm-12">
    <?php
    $dates_range=DrsPanel::getSliderDates(date('Y-m-d'),6);
    echo $this->render('_appointment_time_calender',['dates_range'=>$dates_range,'doctor_id'=>$doctor->id,'type'=>'appointment','userType'=>'patient','slug' => $doctor['userProfile']['slug'],'schedule_id' => $schedule['id'],'date_filter' => $date,'schedule'=>$schedule,'scheduleDay'=>$scheduleDay]);
    ?>
</div>
<div class="doc-boxespart-book">
    <?php
    echo $this->render('_token_list',['slots'=>$slots,'doctor_id'=>$doctor->id]);
    ?>
</div>