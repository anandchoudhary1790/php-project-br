<div class="slider-part homeslider">
    <div class="slider fade1">
        <?php
        if(!empty($sliders)){
            foreach ($sliders as $slide) { ?>
                <div>
                    <div class="image">
                        <img src="<?php echo  $slide->base_path.$slide->file_path.$slide->image?>" />
                    </div>
                </div>
            <?php }
        } ?>
    </div>
</div>