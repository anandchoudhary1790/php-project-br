<?php
use common\models\UserAddressImages;
use common\models\UserAddress;

$base_url= Yii::getAlias('@frontendUrl');

$addressDelete="'".$base_url."/doctor/address-delete'";

$js="
     $('#get_addaddress_model').on('click', function () {
        $.ajax({
            url: baseurl + '/doctor/add-update-address',
            dataType:   'html',
            method:     'POST',
            data: {type:'add'},
            success: function(response){
                $('#addressmodal').empty();
                $('#addressmodal').append(response);
                $('#addressmodal').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
            }
        });
    }); 


    $('.call-delete-modal').on('click', function () {
    id=$(this).attr('data-id');
    name= $('#hospital-name-'+id).html();
    $('#ConfirmModalHeading').html('Confirm Hospitals Delete');
    $('#ConfirmModalContent').html('<p>Are You sure to delete '+name+'</p>');
    $('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false,show: true})
    
     .one('click', '#confirm_ok', function(e) {
             $.ajax({
                method:'POST',
                url: $addressDelete,
                data: { id: id,}
          })
            .done(function( msg ) { 
  
              $('#ConfirmModalShow').modal('hide');
              $('#SuccessModalHeading').html('Hospitals Delete');
              $('#SuccessModalContent').html(msg);
              $('#success-modal').modal({show: true});


            });
            //ajax close
         }) //confirm alert box close

   
       });

   
   function updateDoctorAddress(id){
    $.ajax({
        url: baseurl + '/doctor/add-update-address',
        dataType:   'html',
        method:     'POST',
        data: { id: id , type:'edit'},
        success: function(response){
            $('#addressmodal').empty();
            $('#addressmodal').append(response);
            $('#addressmodal').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
    });


}
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<?php $base_url= Yii::getAlias('@frontendUrl'); ?>


<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="today-appoimentpart mb-3">
            <h3> Hospitals </h3>
            <div class="calender_icon_main location pull-right"> <a href="javascript:void(0)" title="Add More Addresses" id="get_addaddress_model"><i class="fa fa-plus"></i></a> </div>
          </div>
          <div class="row">
          <?php if(count($list)>0){ 
          foreach ($list as $key => $item) { ?>
            <div class="col-sm-6">
              <div class="hospitalall-listhe">
                <div class="pace-part">
                  <div class="pace-left hos-clinics"> 
                  <?php if($item['image']){ ?> <img src="<?php echo $item['image']; ?>" alt="image"><?php } else { ?>
                  <img src="<?php echo $base_url ?>/images/hospital_default.png" alt="image"><?php } ?></div>
                  <div class="pace-right">
                    <h4><?php echo $item['name'] ?>
                    <?php if($item['can_edit']){?>
                      <a href="javascript:void(0)" onclick="return updateDoctorAddress(<?php echo $item['id']; ?>);">
                      <div class="pull-right icon-border"> <i class="fa fa-pencil aand" aria-hidden="true"></i> </div></a>
                      <?php } ?>
                    </h4>
                    <br>
                    <h4 class="pull-right hide">
                    <a href="javascript:void(0)">
                       <div class="pull-right icon-border"> <i class="fa fa-trash-o call-delete-modal" data-id="<?php echo $item['id']?>" aria-hidden="true"></i> </div>
                    </a>
                    </h4>
                    <p> <i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo $item['address_line'] ?> </p>
                    <p> <i class="fa fa-phone" aria-hidden="true"></i> <?php echo $item['mobile'] ?> </p>
                  </div>
                </div>
              </div>
            </div>
            <?php } } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade model_opacity" id="addressmodal" tabindex="-1" role="dialog" aria-labelledby="addressmodal" aria-hidden="true">
</div>

  <div class="register-section">
    <div class="modal fade model_opacity" id="updateaddress" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
    </div>
  </div>


         