<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@backendUrl');
$loginUser=Yii::$app->user->identity;
// pr($loginUser['userProfile']['slug']);die;
$updateStatus="'".$baseUrl."/doctor/update-shift-status'";
$this->title = Yii::t('frontend','Today Timing');
$slug="'".$loginUser['userProfile']['slug']."'";
$getlist="'".$baseUrl."/ajax-address-list'";
$js="
    $(document).on('click', '.shift-toggle',function () {
    $('#main-js-preloader').show();   
      shift_id=$(this).attr('data-shift');
      userid=$(this).attr('data-userid');
      dates=$('#appointment-date').val();  
      date=new Date(dates).getTime() / 1000    
      status=$('#toggle-'+shift_id).is(':checked');      
      if(status=='true'){
        $('#toggle-'+shift_id).attr('checked',false);
        $('#back-color_'+shift_id).css('background-color', '#ccc');
        statusValue=1;
      }else{
         $('#toggle-'+shift_id).attr('checked',true);
         $('#back-color_'+shift_id).css('background-color', '#4cd964');
         statusValue=0;
      }       
       $.ajax({
          method:'POST',
          url: $updateStatus,
          data: {id:shift_id,status:statusValue,date:date,userid:userid}
        })
      .done(function( msg ) { 
            getShiftSlots($doctor->id,'doctor','shifts',date,0,'+');
            $('#main-js-preloader').hide();                           
      });
    }); 
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>


<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto" id="appointments_section">
                    <div class="today-appoimentpart">
                        <div id="appointment_date_select" class="appointment_date_select mx-auto calendra_slider">
                            <?php
                            $dates_range=DrsPanel::getSliderDates();
                            echo $this->render('/common-view/_appointment_date_slider',['dates_range'=>$dates_range,'doctor_id'=>$doctor->id,'type'=>'shifts','userType'=>'doctor']);
                            ?>
                        </div>

                        <div class="calender_icon_main pull-right">
                            <?php echo DatePicker::widget([
                                'name' => 'appointment_date',
                                'type' => DatePicker::TYPE_BUTTON,
                                'value' => date('d M Y',$defaultCurrrentDay),
                                'id'=>  'appointment-date',
                                'buttonOptions'=>[
                                    'label' => '<img src="'.$baseUrl.'/img/celander_icon.png" alt="image"/>',
                                ],
                                'pluginOptions' => [
                                    'autoclose'=>true,
                                    'format' => 'dd M yyyy',
                                    'startDate' => date('d M Y',$defaultCurrrentDay),
                                ],
                                'pluginEvents' => [
                                    "change" => "function(){
                                            bookingsDate($('#appointment-date').val(),'shifts','doctor',$doctor->id);
                                        }",
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="doctor-timing-main" id="appointment_shift_slots">
                        <?php echo $this->render('_address-with-shift',['appointments'=>$shifts,'userid' => $userid])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>