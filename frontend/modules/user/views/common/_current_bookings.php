<?php
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
?>

    <div class="row">
        <?php if(count($bookings)>0){
            foreach ($bookings as $key => $booking) { ?>
                <?php
                if($booking['booking_type'] == 'offline'){
                    $token_class='avail';
                } else{
                    $token_class='';
                }

                if($booking['status'] == 'active'){
                    $bg_class='active_app';
                }
                else{
                    $bg_class='';
                }
                ?>
                <div class="col-sm-4">
                    <div class="token_allover <?php echo $bg_class; ?>">
                        <div class="token <?php echo $token_class; ?>">
                            <h4> <?php echo $booking['token']; ?> </h4>
                        </div>
                        <div class="token-rightdoctor">
                            <div class="tockenimg-right">
                                <?php $imageUrl=DrsPanel::getUserAvator($booking['user_id']); ?>
                                <img src="<?php echo $imageUrl; ?>" alt="image">
                            </div>
                            <div class="token-timingdoc">
                                <h3> <?php echo $booking['name']; ?> </h3>
                                <span class="number-partdoc"> <?php echo $booking['phone']; ?> </span>
                                <p><strong>Booking ID:</strong> <?php echo $booking['booking_id']; ?> </p>
                                <p><strong>Status:</strong> <?php echo $booking['status']; ?> </p>
                            </div>
                        </div>
                    </div>
                </div>


            <?php }
        } else{
            if($is_started){
                echo "No Appointments Booked";
            }
        }?>
    </div>

<?php
//if($current_shifts == $schedule_id) { ?>
    <?php if($is_started){ ?>
        <?php if($is_completed == 1){ ?>
            <div class="bookappoiment-btn">
                <input data-value="<?php echo $schedule_id; ?>" value="Shift Completed" class="bookinput complete_shift" type="button"/>
            </div>
        <?php } else{ ?>
            <div class="bookappoiment-btn">
                <input value="Skip" class="bookinput" type="button" id="skip-btn" data-doctor_id="<?php echo $doctor->id; ?>" data-value="<?php echo $schedule_id; ?>">
                <input value="Next" class="bookinput" type="button" id="next-btn" data-doctor_id="<?php echo $doctor->id; ?>" data-value="<?php echo $schedule_id; ?>">
            </div>
            <div class="text-center">
				<button type="button" class="login-sumbit btn btn-primary" id="end-btn" data-doctor_id="<?php echo $doctor->id; ?>" data-value="<?php echo $schedule_id; ?>">End Shift</button>
			</div>
        <?php } ?>
    <?php }else { ?>
        <div class="bookappoiment-btn">
            <input data-doctor_id="<?php echo $doctor->id; ?>" data-value="<?php echo $schedule_id; ?>" id="start-shift" value="Start Shift" class="bookinput start_shift" type="button">                                  </div>
    <?php } ?>
<?php //} ?>
</div>