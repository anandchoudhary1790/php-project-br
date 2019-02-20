<div class="doc-timingslot">
    <ul>
        <?php echo $this->render('/common/_shifts',['shifts'=>$shifts,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>'history','userType'=>'hospital']);?>
    </ul>
</div>
<?php if(count($shifts) > 0) { ?>
    <div class="doc-boxespart-book" id="shift-tokens">
        <?php
        echo $this->render('_history-patient',['appointments'=>$appointments,'doctor_id'=>$doctor->id,'userType'=>'hospital']);
        ?>
    </div>
<?php } ?>