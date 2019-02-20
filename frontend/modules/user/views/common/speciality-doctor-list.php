<?php
$baseUrl= Yii::getAlias('@frontendUrl');
if($actionType == 'appointment') {
    $this->title = Yii::t('frontend','DrsPanel :: Patient History');
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
                        <?php if($actionType == 'appointment') { ?>
                            <h3> Appointment</h3>
                        <?php }else {  ?>
                            <h3> <?php echo isset($page_heading)?$page_heading:'Patient History'?> </h3>
                        <?php } ?>
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
                            <?php echo $this->render('/common/_doctors_list',['data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>$actionType,'userType'=>$userType]);?>

                        </div>

                        <div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
