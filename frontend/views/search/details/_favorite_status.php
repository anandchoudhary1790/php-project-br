<?php
if($status == 1){ ?>
    <a href="javascript:void(0);" class="red-like update-status" data-value="<?php echo $status ?>"> <i class="fa fa-heart"></i></a>
<?php }else { ?>
    <a href="javascript:void(0);" class="update-status" data-value="<?php echo $status ?>"><i class="fa fa-heart-o" style="font-size:20px;color:#a42127"></i>
    </a>
<?php } ?>