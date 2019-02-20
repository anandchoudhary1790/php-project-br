<?php 

use common\models\User;
$this->title = Yii::t('frontend','DrsPanel :: My Doctors');

$baseUrl=Yii::getAlias('@frontendUrl');

$addresList="'".$baseUrl."/search/doctor-address-list'";

$this->registerJs("
    $('.doctor-addresss-list').on('click',function(){
    slug=$(this).attr('data-slug');
	$.ajax({
		method:'POST',
		url: $addresList,
		data: {slug:slug}
	})
	.done(function( responce_data ) { 
		$('#address-list-modal-content').html('');
		$('#address-list-modal-content').html(responce_data);
		$('#address-list-modal').modal({backdrop: 'static',keyboard: false,show: true})
	})// ajax close		

}); //close addresss List

$('.profile_detail_section').click(function(evt){
        url=$(this).attr('data-url');
        slug=$(this).attr('data-slug');
        if(evt.target.id == 'id_'+slug)
            return;  
            
        url_return(url);

    });
    
   function url_return(url){
        window.location.href = url;
   } 
",\yii\web\VIEW::POS_END); 
?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
  <div class="signup-part">
    <div class="container">
      <div class="row">
      <div class="col-md-9">
          <div class="today-appoimentpart">
              <h3 class="text-left mb-3"> My Doctor's </h3>
          </div>
          <div class="row">
            <?php if(count($doctors)>0) {
                foreach($doctors as $key=>$doctor_id) {
                    $doctor=User::findOne($doctor_id);
                    ?>
                    <?php echo $this->render('_my_doctor_block',['doctor'=>$doctor]); ?>
                <?php }
            } else{ ?>
              <div class="col-sm-12"><?php echo "No Results"; ?> </div>
            <?php  }?>
          </div>
        </div>
        <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
        </div>
        </div>
        </div>
        </section>

<!-- Model confirm message Sow -->
<div class="login-section ">
    <div id="address-list-modal" class="modal model_opacity" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="addressHeading">Doctor <span> Address list </span></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body addressListHospital" id="address-list-modal-content">

                </div>
            </div>
        </div>
    </div>
</div>
