<?php
$baseUrl= Yii::getAlias('@frontendUrl');
if($actionType == 'appointment') {
    $this->title = Yii::t('frontend','DrsPanel :: Doctor Appointment');
}
else{
    $this->title = Yii::t('frontend','DrsPanel :: Patient History');
}
?>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="today-appoimentpart" style="margin-bottom: 15px;">
                        <h3> History </h3>
                    </div>
                    <div class="hospitals-detailspt appointment_list">
                        <?php if($actionType == 'appointment') { ?>
                            <div class="docnew-tab2">
                                <ul class="resp-tabs-list hor_1">

                                    <li class="<?php echo ($type == 'book')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                        <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'book']); ?>">
                                            <?= Yii::t('db','Book Appointment'); ?>
                                        </a>
                                    </li>

                                    <li class="<?php echo ($type == 'current_appointment')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                        <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'current_appointment']); ?>">
                                            <?= Yii::t('db','Appointments'); ?>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        <?php } ?>
                        <div id="booked-appointment">
                            <?php echo $this->render('_doctors_list',['data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>$actionType]);?>

                        </div>

                        <div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
