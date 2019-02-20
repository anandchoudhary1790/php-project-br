<?php
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
?>
<div class="doc-boxespart-book">
    <div class="row">
        <?php if(count($bookings)>0){
            foreach ($bookings as $key => $booking) { ?>
                <?php
                if($booking['booking_type'] == 'offline'){
                    $token_class='avail';
                } else{
                    $token_class='';
                }
                ?>
                <div class="col-sm-4">
                    <div class="token_allover">
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
                            </div>
                        </div>
                    </div>
                </div>


            <?php }
        } else{
            echo "No Appointments Booked";
        }?>
    </div>
</div>