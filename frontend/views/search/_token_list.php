
    <?php

    if(count($slots)>0 && !empty($slots)){ ?>
        <div class="row">
            <?php  foreach ($slots as $key => $slot) {
                if($slot['status'] == 'booked'){
                    $token_class='emergency';
                    $status='Booked';
                    $class_click= 'get-slot-booked';
                }
                else{
                    if($slot['type'] == 'consultation'){
                        $token_class='avail';
                        $status='Available';
                        $class_click= 'get-slot';
                    }else if($slot['type']=='emergency'){
                        $status='Emergency';
                        $token_class='emergency';
                        $class_click= 'get-slot';
                    }else{
                        $token_class='avail';
                        $status='Available';
                        $class_click= 'get-slot';
                    }
                }

                ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div id="slot-<?php echo $slot['id']; ?>" class="token_allover token_allover_book <?php echo $class_click; ?>">
                        <div class="token <?php echo $token_class; ?>">
                            <h4> <?php echo $slot['token']; ?> </h4>
                        </div>
                        <div class="token-rightdoctor">
                            <div class="token-timingdoc <?php echo $token_class; ?>">
                                <h3> <?php echo $status; ?> </h3>
                                <span class="time-btnpart"> <?php echo $slot['shift_name']; ?></span> </div>
                        </div>
                    </div>
                </div>
            <?php }   ?>
        </div>
    <?php }
    else {
        echo '<div class="row"><div class="col-sm-12 text-center">Token Not Available</div></div>';
    } ?>
