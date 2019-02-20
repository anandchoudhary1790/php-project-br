<?php
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;
?>
<div class="row">
    <div class="col-sm-12">
        <div class="pace-left">
            <?php $image = DrsPanel::getUserAvator($doctor->id);?>
            <!--  <img src="<?php //echo $image; ?>" alt="image"/> -->
            <?php
            echo Lightbox::widget([
                'files' => [
                    [
                        'thumb' => $image,
                        'original' => $image,
                        'title' => 'optional title',
                    ],
                ]
            ]);
            ?>
        </div>
        <div class="pace-right">
            <h4><?= $doctor['userProfile']['name'] ?></h4>
            <p> <?= $doctor['userProfile']['speciality']; ?> </p>
            <p>
                <i class="fa fa-calendar"></i>
                <span id="doctor-date"> <?php  echo isset($date)?$date:''; ?> </span>
                <span class="pull-right">
                                                <strong><i class="fa fa-rupee"></i><?php if(isset($scheduleDay['consultation_fees_discount']) && $scheduleDay['consultation_fees_discount'] < $scheduleDay['consultation_fees'] && $scheduleDay['consultation_fees_discount'] > 0) { ?> <?= $scheduleDay['consultation_fees_discount']?>/- <span class="cut-price"><?= $scheduleDay['consultation_fees']?>/-</span> <?php } else { echo $scheduleDay['consultation_fees'].'/-'; } ?></strong>
                                            </span>
            </p>
            <p>
                <i class="fa fa-clock-o" aria-hidden="true"></i>
                <?php echo date('h:i a',$scheduleDay['start_time']); ?> - <?php echo date('h:i a',$scheduleDay['end_time']); ?>
            </p>
            <div class="pull-left">
                <p>
                    <strong><?php echo DrsPanel::getHospitalName($scheduleDay['address_id'])?></strong>
                    <small><br><?php echo DrsPanel::getUserAddress($scheduleDay['address_id']);?></small>
                    <?php echo isset($doctor['userAddress']['name'])?$doctor['userAddress']['name']:''?> <?php echo isset($doctor['userAddress']['city'])?$doctor['userAddress']['city']:''?>
                </p>
            </div>
            <div class="pull-right">
                <a href="#" data-toggle="modal" data-target="#myModal">0.8 km<i class="fa fa-location-arrow"></i>
                </a>
            </div>
        </div>
    </div>
</div>