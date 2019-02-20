<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\components\DrsPanel;
$loginUser=Yii::$app->user->identity;
$baseUrl= Yii::getAlias('@frontendUrl');

$bookeing_confirm=$baseUrl."/appointment-time/".$doctorProfile->slug.'/';

$js="
$('.booking_shifts').click(function() {
    var checked = $(this).is(':checked');
    bookval = $(this).val();
    var urlLink= '".$bookeing_confirm."'"."+bookval;
    window.location.replace(urlLink);
}); 
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
if(count($appointments)>0) {

    foreach ($appointments as $key => $appointment) {
        //echo "<pre>"; print_r($appointment);die;
        if(!empty($appointment['address_id'])){ ?>
            <div class="all-listhospital">
                <h5 style="font-size: 2.25rem;">
                    <?php echo $appointment['name'];?>
                    <span class="price_text pull-right">
                        <i class="fa fa-rupee"></i>
                        <?php if(isset($appointment['consultation_fees_discount']) && $appointment['consultation_fees_discount'] < $appointment['consultation_fees'] && $appointment['consultation_fees_discount'] > 0) { ?> <?= $appointment['consultation_fees_discount']?>/- <span class="cut-price"><?= $appointment['consultation_fees']?>/-</span> <?php } else { echo $appointment['consultation_fees'].'/-'; } ?>
                    </span>
                </h5>
                <span>
                    <input name="radio-group" id="test<?php echo $appointment['address_id']?>" type="radio" class="booking_shifts" value="<?=$appointment['schedule_id']?>">
                    <label for="test<?php echo $appointment['address_id']?>"><?php echo $appointment['address']?></label>
                </span>
                <input name="slug" id="doctor_slug" type="hidden" value="<?=$doctorProfile->slug?>">
                <span class="pull-right green-text"><?= $appointment['next_availablity']; ?></span>
                <div class="hos-lr-part">
                    <div class="pull-left"><i class="fa fa-map-marker" aria-hidden="true"></i> 0.8 km away</div>
                    <div class="pull-right"><i class="fa fa-clock"></i>
                    <?php echo $appointment['start_time'].' - '.$appointment['end_time']?></div>
                </div>
            </div>
        <?php }
    }
}else { ?>
    <div class="morning-parttiming">
        <p> Docotr not available for online appointments. Please contact adminstrator.</p>
    </div>
<?php } ?>