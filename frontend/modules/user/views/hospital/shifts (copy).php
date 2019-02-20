<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
 use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$baseUrl= Yii::getAlias('@frontendUrl'); 
$loginUser=Yii::$app->user->identity; 
$updateStatus="'".$baseUrl."/appointment/status-update'";
$this->title = Yii::t('frontend','DrsPanel :: Doctor Appoinments'); 
$js="
    $('.shift-toggle').on('click', function () {
      shift_id=$(this).attr('data-shift');
      status=$('#toggle-'+shift_id).is(':checked');
      if(status=='true'){
        $('#toggle-'+shift_id).removeAttr('checked');
        statusValue=0;
      }else{
         $('#toggle-'+shift_id).attr('checked',true);
         statusValue=1;
      }

       $.ajax({
          method:'POST',
          url: $updateStatus,
          data: {id:shift_id,status:statusValue}
    })
      .done(function( msg ) { 

      });
    }); 
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="today-appoimentpart">
             <div class="col-md-6 mx-auto calendra_slider">
            	<div class="slider one-time">
            	 <div>
                     <h3> Appoinments </h3>
                     <p> <?php //05 June 2018 ?> </p>
                 </div>
                
            </div>
            </div>
          </div>
          <div class="doctor-timing-main">
            <?php /*<h2> <a class="clander_icon"><img src="<?php echo $baseUrl?>/images/celander_icon.png"></a></h2> */ ?>
            <?php if(count($hospitals)>0) { foreach ($hospitals as $key => $hospital) { ?>
            <div class="morning-parttiming">
              <div class="main-todbox no-pd">
                <div class="pull-left">
                  <div class="moon-cionimg "><img src="<?php echo $baseUrl?>/images/doctor-profile-icon3.png" alt="image"> <span> <strong class="hospota_add"> <?php echo $hospital['name']?> </strong></span> </div>
                </div>
              <?php /*  <div class="pull-right icon-border"> <a href="#" data-toggle="modal" data-target="#basicExampleModal"><i class="fa fa-pencil" aria-hidden="true"></i></a> </div> */ ?>
              </div>
              <div class="main-todbox no-pd">
                <div class="row">
                	<div class="col-sm-9">
                    	<div class="pull-left">
                          <div class="moon-cionimg">
                            <p><?php echo $hospital['address_line'] ?> </p>
                          </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                    	<div class="pull-right color-texttheme"> (<?php echo DrsPanel::userBookingCount($hospital['user_id']); ?>) patient </div>
                    </div>
                </div>
              </div>
              <?php 
        $shiftTimes=DrsPanel::getDoctorShifts($hospital['user_id'],$hospital['id']);
        if($shiftTimes){
        foreach ($shiftTimes as $key => $value) { ?>
              <div class="main-todbox">
                <div class="pull-left">
                  <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-clock-icon.png" alt="image"><span> <?php echo $value['shift_time'];?> </span> </div>
                </div>
                <div class="pull-right">

            <span><?php  echo implode(',',ArrayHelper::getColumn($value['day'], "weekday")); ?> </span>
              <?php $attr=($value['status'])?'checked="checked"':''; ?>
                  <label class="switch ">
                    <input type="checkbox" <?php echo $attr=($value['status'])?'checked="checked"':''; ?> id="toggle-<?php echo $value['shift_id']?>">
                    <span class="slider-toggle round shift-toggle" data-shift="<?php echo $value['shift_id']; ?>"></span> </label>



                </div>
              </div>
              <?php } } ?>
            </div>
            <?php } } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>