<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;


$this->title = Yii::t('frontend', 'Patient::Profile');
$baseUrl=Yii::getAlias('@frontendUrl');



$getTabContent="'ajax-tab-content'";
$js="
      $('.appointment-tab').on('click', function () {
        type = $(this).attr('type');
        $.ajax({
          method:'POST',
          url: $getTabContent,
          data: {type:type}
        }).done(function( msg ) { 
          if(msg){
            $('#'+type).html('');
            $('#'+type).html(msg);
          }
        });
    }); 
    
    $('.check_reminder').on('click',function(){
        appointment_id = $(this).attr('data-id');
        $.ajax({
            url: 'ajax-check-reminder',
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

<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-sm-9">
          <div class="hospitals-detailspt">

              <div class="tablist-diserpthree appointment_list">
                  <ul class="resp-tabs-list">
                      <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'all']); ?>'" class="<?php echo ($type == 'all')?'resp-tab-active':'resp-tab-inactive'; ?> view_appointment">
                          <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'all']); ?>" class="appointment_click">
                              <?= Yii::t('db','All'); ?>
                          </a>
                      </li>
                      <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'upcoming']); ?>'" class="<?php echo ($type == 'upcoming')?'resp-tab-active':'resp-tab-inactive'; ?> view_appointment">
                          <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'upcoming']); ?>" class="appointment_click">
                              <?= Yii::t('db','Upcoming'); ?>
                          </a>
                      </li>
                      <li onclick="location.href='<?php echo yii\helpers\Url::to(['appointments','type'=>'past']); ?>'" class="<?php echo ($type == 'past')?'resp-tab-active':'resp-tab-inactive'; ?> view_appointment">
                          <a href="<?php echo yii\helpers\Url::to(['appointments','type'=>'past']); ?>" class="appointment_click">
                              <?= Yii::t('db','Past'); ?>
                          </a>
                      </li>
                  </ul>
              </div>
              <?php echo $this->render('_appointment',['appointments'=>$appointments]); ?>
          </div>
        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
      </div>
    </div>
  </div>
</section>
<div class="login-section">
    <div class="modal fade model_opacity" id="addupdatereminder" tabindex="-1" role="dialog" aria-labelledby="addupdatereminder" aria-hidden="true">
    </div>
</div>

