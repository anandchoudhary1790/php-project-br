<?php
foreach ($tokens as $key => $token) {
    if($token['status']=='pending' || $token['status']=='booked' || $token['status']=='skip'){
        $class='emergency';
        $status=($type=='booked')?'Booked':(($type=='appointment')?'Skip':'Pending');
    }else if($token['status']=='completed'){
        $class='emergency';
        $status='Completed';
    }else if($token['status']=='cancelled'){
        $class='emergency';
        $status='Cancelled';
    }else if($token['status']=='deactivate'){
        $class='emergency';
        $status='Cancelled';
    }else{
        $class='avail';
        $status='Available';
    }

    ?>



    <div class="col-sm-6 <?php echo $type.'-'.$key; ?>" data-token="<?php echo $token['token']?>" id="token_<?php echo $key.'_'.$type.'_'.$token['id'];?>" token-type="<?php echo $type; ?>" >
        <div class="token_allover">
            <div class="token <?php echo $class; ?>"> <h4> <?php echo $token['token']; ?> </h4> </div>
            <div class="token-rightdoctor">
                <div class="tockenimg-right"> <img src="<?php echo $token['patient_image'];?>" alt="image"> </div>
                <div class="token-timingdoc <?php echo $class; ?>"> <h3> <?php echo $status; ?> </h3>  <span class="time-btnpart"> <?php echo $token['shift_name'];?> </span> </div>
            </div>
        </div>
    </div>

    <?php
}

?>

