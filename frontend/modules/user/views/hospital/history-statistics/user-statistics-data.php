<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl');
$loginUser=Yii::$app->user->identity;
$this->title = Yii::t('frontend','DrsPanel :: User Statistics Data');
?>
<div class="youare-text">User Statistics Data for Doctor: <?php echo DrsPanel::getUserName($doctor->id);?></div>
<section class="mid-content-part">
    <div class="signup-part roll_box">
        <div class="container"> <?php //pr($appointments); ?>
            <div class="row">
                <div class="col-md-12" id="appointments_section">
                    <div class="today-appoimentpart">
                        <div id="appointment_date_select" class="appointment_date_select mx-auto calendra_slider">
                            <?php
                            $dates_range=DrsPanel::getSliderDates();
                            echo $this->render('/common/_appointment_date_slider',['dates_range'=>$dates_range,'doctor_id'=>$doctor->id,'type'=>'user_history','userType'=>'hospital']);
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
                                    'format' => 'dd M yyyy'
                                ],
                                'pluginEvents' => [
                                    "change" => "function(){
                                    historyDate($('#appointment-date').val(),'user_history','hospital',$doctor->id);
                            }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>

                    <div class="hospitals-detailspt appointment_list">
                        <div class="doc-boxespart-book" id="history-content">
                            <?php echo $this->render('_user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shifts,'doctor'=>$doctor,'current_shifts'=>$current_selected,'type'=>'user_history','userType'=>'hospital'])?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>