<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;

$this->title = Yii::t('frontend', 'Patient::Profile');
$baseUrl=Yii::getAlias('@frontendUrl');
$js="
    $('.check_reminder').on('click',function(){
        appointment_id = $(this).attr('data-id');
        $.ajax({
            url: '".$baseUrl."/patient/ajax-check-reminder',
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
    });";
$this->registerJs($js,\yii\web\VIEW::POS_END);
?>

<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-sm-9">
                    <div class="hospitals-detailspt">
                        <?php echo $this->render('_appointment',['appointments'=>$appointments]); ?>
                    </div>
                </div>
                <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
            </div>
        </div>
    </div>
</section>

<div class="register-section">
    <div class="modal fade model_opacity" id="addupdatereminder" tabindex="-1" role="dialog" aria-labelledby="addupdatereminder" aria-hidden="true">
    </div>
</div>

