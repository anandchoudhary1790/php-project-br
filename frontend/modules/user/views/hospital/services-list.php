 <?php 
$baseUrl= Yii::getAlias('@frontendUrl');
if(!empty($servicesList[0]['services'])) {
  $Servicedata = explode(',', $servicesList[0]['services']);
  foreach ($Servicedata as $list) { ?>
      <div class="morning-parttiming">
          <div class="main-todbox">
              <div class="pull-left">
                  <div class="moon-cionimg">
                      <img src="<?php echo $baseUrl?>/images/doctor-bag-icon.png" alt="image">
                      <span id="hospital-name-<?php echo $list ?>"><?php echo $list ?></span>
                  </div>
            </div>
          </div>
      </div>
  <?php } 
}
else { ?>
    You have no services
<?php } ?>

<div class="register-section">
    <div class="modal fade model_opacity" id="attenderEdit-modal" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalContact">Update Experience </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body" id="edit-modal-form"></div>
            </div><!-- /.modal-content -->
        </div>
    </div>
</div>