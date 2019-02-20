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



            <?php
            $shifts=DrsPanel::getShiftListByAddress($doctor_id,$list['id']);
            foreach($shifts as $shift){ ?>
            <?php if(isset($allshifts[$shift['shift_id']])){ ?>
                <div class="doc-listboxes">
                    <div class="pull-left">
                        <p> <?php  echo $shift['shift_label']; ?></p>
                    </div>
                    <div class="pull-right text-right">
                        <p> <strong><?php  echo str_replace(',', ', ', $shift['shifts_list']); ?> </strong> </p>
                    </div>
                </div>
            <?php } } ?>
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