<div class="doc-timingslot">
    <ul>
        <?php echo $this->render('/common/_shifts',['shifts'=>$appointments['shifts'],'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type,'userType'=>$userType]);?>
    </ul>
</div>
<div class="doc-boxespart-book" id="shift-tokens">
    <?php
    if(($type == 'current_appointment')){
        echo $this->render('/common/_bookings',['bookings'=>$bookings,'doctor_id'=>$doctor->id,'userType'=>$userType]);
    }
    else{
        echo $this->render('/common/_slots',['slots'=>$slots,'doctor_id'=>$doctor->id,'userType'=>$userType]);
    }
    ?>
</div>