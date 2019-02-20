<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\models\UserProfile;
$this->title = Yii::t('frontend','DrsPanel::Doctor List');
$base_url= Yii::getAlias('@frontendUrl');
$addresList="'".$base_url."/search/doctor-address-list'";

$js="
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
            
        if(evt.target.id == 'login-popup')
            return; 
            
        url_return(url);

    });
    
   function url_return(url){
        window.location.href = url;
   } 
";
$this->registerJs($js,\yii\web\VIEW::POS_END);
 ?>
<div class="inner-banner"> </div>
<section class="mid-content-part inner-part">
<div class="row">
    <div class="col-md-12">
   <?php echo $this->render('_search_filter');?>
    </div>
    </div>
    <div class="container">
    
        <div class="row">
            <div class="col-md-9">
                <h2 class="display-6 lg_pb_30"><?php echo count($lists); ?> results <!--offering <strong>Prime benefits</strong>--></h2>
                <div class="doctor_part">
                    <div class="doctoe_listing_main">
                        <div class="row">
                            <?php if(!empty($lists)) { ?>
                                <?php foreach ($lists as $doctor) { ?>
                                    <?php echo $this->render('_list_block',['doctor' => $doctor]); ?>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php echo $this->render('/layouts/rightside'); ?>
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