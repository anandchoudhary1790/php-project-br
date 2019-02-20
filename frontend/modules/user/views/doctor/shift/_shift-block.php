<?php
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;
$js="
    $('.fancybox').fancybox();
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="pace-part patient-prodetials">
    <div class="row">
        <div class="col-sm-12">
            <div class="pace-icon">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
            </div>
            <div class="pace-right main-second">
                <h4>
                    <?php echo $list['name']?>
                </h4>
                <p><?php echo $list['address_line'] ?> </p>
            </div>

            <div class="shift_edit_icon_main location pull-right ">
                <a class="modal-call" href="/g/sites/drspanel/doctor/edit-shift/<?php echo $list['id']?>" title="Edit Shift">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>

            <?php
            if($list['user_id'] == $doctor_id){ $disable_field=0;}
            else{$disable_field=1;}

            if($disable_field == 0){ ?>
                <div class="shift_delete_icon_main location pull-right ">
                    <a class="call-delete-modal" href="javascript:void(0)" data-id="<?php echo $doctor_id ?>"  data-address_id="<?php echo $list['id']?>" title="Delete Shift with Address">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            <?php } ?>



            <?php
            $shifts=DrsPanel::getShiftListByAddress($doctor_id,$list['id']);
            foreach($shifts as $shift){ ?>
                <div class="doc-listboxes">
                    <div class="pull-left">
                        <p> <?php  echo $shift['shift_label']; ?></p>
                    </div>
                    <div class="pull-right text-right">
                        <p> <strong><?php  echo str_replace(',', ', ', $shift['shifts_list']); ?> </strong> </p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col-sm-12">
            <div class="pace-rightpart-show">
                <ul class="pacemain-list">
                    <?php
                    if(!empty($list['images_list'])) {
                        foreach ($list['images_list'] as $key => $hospital_img) {  ?>
                            <li <?php if(isset($class)?$class:''); ?>>
                                <div class="pace-list">
                                    <a class="fancybox" rel="gallery1" href="<?php echo $hospital_img['image'];?>">
                                    <img src="<?php echo $hospital_img['image']?>" alt="" />
                                    </a>
                                </div>
                            </li>
                        <?php } }  ?>
                </ul>
                <div class="away-part pull-right hide">
                    <a href="#" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-map-marker" aria-hidden="true"></i> 0.8 km away
                    </a>
                    <br>
                    <a href="#"> Show On Map
                        <i class="fa fa-location-arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>