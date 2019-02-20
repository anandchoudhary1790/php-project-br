<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
$loginUser=Yii::$app->user->identity;
$this->title = Yii::t('frontend','DrsPanel :: Doctor Appointment History');
?>
<div class="youare-text">Patient History for Doctor: <?php echo DrsPanel::getUserName($doctor->id);?></div>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-12" id="appointments_section">
                    <div class="today-appoimentpart">
                        <div id="appointment_date_select" class="appointment_date_select mx-auto calendra_slider">
                            <?php
                            $dates_range=DrsPanel::getSliderDates();
                            echo $this->render('/common/_appointment_date_slider',['dates_range'=>$dates_range,'doctor_id'=>$doctor->id,'type'=>$type,'userType'=>$userType]);
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
                                ],
                                'pluginEvents' => [
                                    "change" => "function(){
                                    historyDate($('#appointment-date').val(),'$type','$userType',$doctor->id);
                            }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="doctormain-detailspt">
                        <div class="doc-boxespart-book" id="history-content">
                            <?php echo $this->render('_history-content',['typeCount'=>$typeCount,'history_count'=>$history_count,'appointments'=>$appointments,'shifts'=>$shifts,'doctor'=>$doctor,'current_selected'=>$current_selected,'type'=>$type,'userType'=>$userType])?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>