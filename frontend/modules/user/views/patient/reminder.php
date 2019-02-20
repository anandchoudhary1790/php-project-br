<?php
$base_url= Yii::getAlias('@frontendUrl');
use common\components\DrsPanel;
use common\models\User;
/*echo '<pre>';
print_r($reminder);die;*/
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part patient_reminder">
    <div class="container">
      <div class="row">
        <div class="col-md-8 mx-auto">
          <div class="today-appoimentpart">
            <h3 class="text-left mb-3"> Reminder </h3>
          </div>

          <?php
          if(!empty($reminders)){
           foreach($reminders as $reminder){
              $doctor=User::findOne($reminder['doctor_id']);
              ?>
              <div class="pace-part main-tow reminder-col">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="pace-left">
                        <?php $image = DrsPanel::getUserAvator($reminder['doctor_id'],'thumb');?>
                        <img src="<?php echo $image; ?>" alt="image"/>
                    </div>
                    <div class="pace-right reminder_icon">

                        <h4>
                            <?=$doctor['userProfile']['prefix']?> <?=$doctor['userProfile']['name']?>  
                        </h4>
                        <div class="reminder-edit-del clearfix">
                            <a href="javascript:void(0)" data-id="<?php echo $reminder['appointment_id']?>" class="add-record delete_reminder"> <i class="fa fa-trash"></i></a>
                            <a href="javascript:void(0)" data-id="<?php echo $reminder['appointment_id']?>" class="add-record check_reminder"> <i class="fa fa-edit"></i></a>
                        </div>
                        <p> <?= $doctor['userProfile']['speciality']; ?></p>
                      <ul class="doctor_reminder doctor_reminder_edit">
                          <li>Token No.</li><li><?= $reminder['token'];?></li>
                          <li>Date</li><li><?= $reminder['appointment_date'];?></li>
                          <li>Time</li><li><?= $reminder['appointment_time'];?></li>
                          <li>Reminder Date</li><li><?= $reminder['reminder_date'];?></li>
                          <li>Reminder Time</li><li><?= $reminder['reminder_time'];?></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
          <?php } } else { ?>  <div class="">
                <div class="row">
                  <div class="col-sm-12 text-center">Records not found</div></div></div> <?php }?>

        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
      </div>
    </div>
  </div>
</section>

<?php $js="
  $('.check_reminder').on('click',function(){
        appointment_id = $(this).attr('data-id');
        $.ajax({
            url: 'ajax-check-reminder-list',
            dataType:   'html',
            method:     'POST',
            data: { appointment_id: appointment_id},
            success: function(response){
                $('#addupdatereminder').empty();
                $('#addupdatereminder').append(response);
                $('#addupdatereminder').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
                $('.reminder_time_check').timepicker({defaultTime: '08:00 A'});
            }
        });
    });

      $('.delete_reminder').on('click',function(){
        appointment_id = $(this).attr('data-id');
        $.ajax({
            url: 'ajax-check-reminder-delete',
            dataType:   'html',
            method:     'POST',
            data: { appointment_id: appointment_id},
            success: function(response){
                $('#addupdatereminder').empty();
                $('#addupdatereminder').append(response);
                $('#addupdatereminder').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
                $('.reminder_time_check').timepicker({defaultTime: '08:00 A'});
            }
        });
    });

";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>


<div class="register-section">
    <div class="modal fade model_opacity" id="addupdatereminder" tabindex="-1" role="dialog" aria-labelledby="addupdatereminder" aria-hidden="true">
    </div>
</div>