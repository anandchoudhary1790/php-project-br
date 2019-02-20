<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\PatientMemberFiles;
use branchonline\lightbox\Lightbox;

$baseUrl=Yii::getAlias('@frontendUrl');

$getrecord="'".$baseUrl."/patient/add-update-record'";
$deleterecord="'".$baseUrl."/patient/delete-record'";

$js="
    $('#record-popup').on('click',function(){
        $.ajax({
            url: $getrecord,
            dataType:   'html',
            method:     'POST',
            data: { member_id: $member_id,type : 'add'},
            success: function(response){
                $('#addupdaterecord').empty();
                $('#addupdaterecord').append(response);
                $('#addupdaterecord').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
        });
    });
    
    $('.delete_record').on('click',function(){
        recordid=$(this).attr('data-recordid');
        $.ajax({
            url: $deleterecord,
            dataType:   'html',
            method:     'POST',
            data: { member_id: $member_id,record_id:recordid},
            success: function(response){
                location.reload();
            }
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
        <div class="col-md-9">
          <div class="appointment_part patient_profile">
            <div class="record_part">
                <div class="today-appoimentpart">
                    <div class="col-md-12 calendra_slider">
                        <h3> Patient Record Files </h3>
                        <div class="calender_icon_main location pull-right "> <a class="modal-call" href="javascript:void(0)" title="Upload Record" id="record-popup"><i class="fa fa-plus-circle"></i></a> </div>
                    </div>
                </div>
                <div class="row">
                    <?php
                    if(!empty($records)){
                        foreach ($records as $key => $patientrecord) {
                        if(($patientrecord['image_type'])) { ?>
                            <div class="col-md-4 col-sm-6">
                              <div class="record_file_img">
                                  <a href="javascript:void(0)" class="delete_record" data-recordid="<?php echo $patientrecord['id']; ?>">
                                      <i class="fa fa-trash" aria-hidden="true"></i>
                                  </a>
                                <?php if($patientrecord['image_type']=='jpg' || $patientrecord['image_type']=='png' || $patientrecord['image_type']=='jpeg') { ?>
                                    <?php 
                                    echo Lightbox::widget([
                                    'files' => [
                                    [
                                    'thumb' => $patientrecord['image_base_url'].$patientrecord['image_path'].$patientrecord['image'],
                                    'original' => $patientrecord['image_base_url'].$patientrecord['image_path'].$patientrecord['image'],
                                    'title' => $patientrecord['image_name'],
                                    ],
                                    ]
                                    ]); 
                                    ?>

                                <?php } if($patientrecord['image_type']=='docx') { ?>
                                <a href="<?php echo $patientrecord['image_base_url'].$patientrecord['image_path'].$patientrecord['image']?>" title="Download Document" target="_blank" download><img src="<?php echo $baseUrl?>/images/doc_img.jpg"></a>

                                <?php  } if($patientrecord['image_type']=='pdf'){ ?>
                                <a href="<?php echo $patientrecord['image_base_url'].$patientrecord['image_path'].$patientrecord['image']?>" title="Download <?php echo $patientrecord['image_name']?> Files" target="_blank" download><img src="<?php echo $baseUrl?>/images/pdf_img.jpg"></a>
                                <?php }?>
                                <span><?php echo $patientrecord['image_name']; ?></span>
                              </div>
                            </div>
                        <?php }
                    }
                    } else{
                        echo "No Records Found";
                    }?>
                </div>
            </div>
          </div>
        </div>
          <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
      </div>
    </div>
  </div>
</section>

<div class="register-section">
    <div class="modal fade model_opacity" id="addupdaterecord" tabindex="-1" role="dialog" aria-labelledby="addupdaterecord" aria-hidden="true">
    </div>
</div>