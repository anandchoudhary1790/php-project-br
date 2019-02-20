<?php

?>

<?php if(!empty($appointments)) {  ?>
    <?php  foreach ($appointments as $key => $appointment) {
        $online_class='';
        if($appointment['booking_type']=='online'){
            $online_class="avail";
        }
        ?>

        <div class="col-sm-4">
            <div class="token_allover">
                <div class="token <?php echo $online_class; ?>">
                    <h4> <?php echo $appointment['token']; ?> </h4>
                </div>
                <div class="token-rightdoctor">
                    <div class="tockenimg-right"> <?php if($appointment['patient_image']){ ?>
                            <img src="<?php echo $appointment['patient_image']; ?>" alt="image"> <?php } ?> </div>
                    <div class="token-timingdoc">
                        <h3> <?php echo $appointment['name'];?>  </h3>
                        <span class="number-partdoc"> <?php echo $appointment['phone'];?> </span>
                        <p><strong>Booking ID:</strong> <?php echo $appointment['booking_id'];?> </p>
                    </div>
                </div>
            </div>
        </div>

    <?php }  }else{ ?>
    <div class="col-sm-12">
        <p>You have no any appointment.</p>
    </div>
<?php } ?>


