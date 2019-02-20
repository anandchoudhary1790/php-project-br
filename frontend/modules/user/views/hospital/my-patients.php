<?php $base_url= Yii::getAlias('@frontendUrl'); ?>
<?php  $this->title = Yii::t('frontend', 'Hospital::Mypatients', [
  'modelAddressClass' => 'Hospital',
  ]);?>
    
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-10 mx-auto">
          <div class="today-appoimentpart">
            <div class="col-md-6 mx-auto calendra_slider">
              <h3> Patients List</h3>
            </div>
          </div>
          <div class="appointment_part">
            <div class="appointment_details">
              <div class="row">
                <?php 
                // pr($lists);die;
                if(!empty($lists['data'])){ 
                foreach ($lists['data'] as $list) { ?>
                <div class="col-sm-6">
                  <div class="pace-part main-tow">
                    <div class="pace-left"> <img src="<?php echo $base_url?>/images/dr_pick.jpg" alt="image"></div>
                    <div class="pace-right">
                      <h4> <?php echo isset($list['patient_name'])?$list['patient_name']:''?></h4>
                      <p><span class="first_textB">Phone No:</span> +91 <?php echo isset($list['patient_phone'])?$list['patient_phone']:''?> </p>
                      <?php if($list['patient_id']!=0){ ?>
                      <p><span class="first_textB">Email ID:</span> sherley@gmail.com </p>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <?php } } else { ?> <h4>still there is no any patient </h4><?php }?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<div class="register-section">
<div id="shift-update-modal" class="modal fade model_opacity"  role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
       <h4 class="modal-title" id="myModalContact">Update Shifts</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               
            </div>
            <div class="modal-body" id="shift-content">

            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
</div>

