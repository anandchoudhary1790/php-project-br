<?php
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
$loginUser=Yii::$app->user->identity;
$this->title = Yii::t('frontend','DrsPanel :: Doctor Appoinment');
?>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="appointments_section">
                    <div class="today-appoimentpart">
                        <div id="appointment_date_select" class="appointment_date_select mx-auto calendra_slider">
                            <?php
                            $dates_range=DrsPanel::getSliderDates();
                            echo $this->render('/common/_appointment_date_slider',['dates_range'=>$dates_range,'doctor_id'=>$doctor->id,'type'=>$type,'userType'=>'doctor']);
                            ?>
                        </div>
                        <div class="calender_icon_main pull-right">
                            <?php echo DatePicker::widget([
                                'name' => 'appointment_date',
                                'type' => DatePicker::TYPE_BUTTON,
                                'value' => date('d M Y',$defaultCurrrentDay),
                                'id'=>  'appointment-date',
                                'buttonOptions'=>[
                                    'label' => '<img src="'.$baseUrl.'/images/celander_icon.png" alt="image"/>',
                                ],
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd M yyyy',
                                    'startDate' => date('d M Y',$defaultCurrrentDay),
                                ],
                                'pluginEvents' => [
                                    "change" => "function(){
                                            bookingsDate($('#appointment-date').val(),'$type','doctor',$doctor->id);
                                        }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="hospitals-detailspt appointment_list">
                        <div class="docnew-tab">
                            <ul class="resp-tabs-list">

                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'book']); ?>'" class="<?php echo ($type == 'book')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['/'.$userType.'/appointments/'.$doctorProfile->slug,'type'=>'book']); ?>">
                                        <?= Yii::t('db','Book Appointment'); ?>
                                    </a>
                                </li>
                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'current_shift']); ?>'" class="<?php echo ($type == 'current_shift')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['/'.$userType.'/appointments/'.$doctorProfile->slug,'type'=>'current_shift']); ?>">
                                        <?= Yii::t('db','Current Appointment Affair'); ?>
                                    </a>
                                </li>
                                <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'current_appointment']); ?>'" class="<?php echo ($type == 'current_appointment')?'resp-tab-active':'resp-tab-inactive'; ?>">
                                    <a href="<?php echo yii\helpers\Url::to(['/'.$userType.'/appointments/'.$doctorProfile->slug,'type'=>'current_appointment']); ?>">
                                        <?= Yii::t('db','Appointments'); ?>
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div id="appointment_shift_slots">
                            <?php
                            echo $this->render('/common/_appointment_shift_slots',['appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'bookings'=>$bookings,'type'=>$type,'userType'=>'doctor']);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>