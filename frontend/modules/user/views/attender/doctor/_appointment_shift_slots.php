<div class="doc-timingslot">
    <ul>
        <?php echo $this->render('_shifts',['shifts'=>$appointments['shifts'],'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type]);?>
    </ul>
</div>
<div class="doc-boxespart-book" id="shift-tokens">
    <?php
    if(($type == 'current_appointment')){
        echo $this->render('_bookings',['bookings'=>$bookings,'doctor_id'=>$doctor->id]);
    }
    else{
        echo $this->render('_slots',['slots'=>$slots,'doctor_id'=>$doctor->id]);
    }
    ?>
</div>