<?php 
use common\components\DrsPanel;
use yii\helpers\ArrayHelper;
use branchonline\lightbox\Lightbox;

$baseUrl= Yii::getAlias('@frontendUrl'); 

$js="
    $('.fancybox').fancybox();
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>

<?php 
  if(!empty($hospitals))
  { 
    foreach ($hospitals as $hospital)
      { ?>
        <div class="pace-part patient-prodetials">
          <div class="row">
            <div class="col-sm-12">
              <div class="pace-icon"> 
                <i class="fa fa-map-marker" aria-hidden="true"></i> 
                </div>
              <div class="pace-right main-second">
                <h4>
                    <?php echo $hospital['name']?>
                    <span class="ratingpart pull-right">
                        <p> Fee <strong> <i class="fa fa-rupee"></i> <?php echo $hospital['consultation_fees'] ?></strong> </p>
                    </span> 
                </h4>
                  <p>
                      <span><?php echo $hospital['address'] ?></span>
                      <span class="green-text" style="float: right;"><?php echo $hospital['next_availablity'] ?></span>
                  </p>
              </div> 
              <div class="doc-listboxes">
                <div class="pull-left">
                <p> <?php  echo $hospital['shift_label']; ?></p>
                </div>
                <div class="pull-right text-right">
                <p> <strong><?php  echo $hospital['shifts_list'] ?> </strong> </p>
                </div>
              </div>
            </div>
            <div class="col-sm-12">
                <div class="pace-rightpart-show">
                  <ul class="pacemain-list">
                    <?php
                    // pr($hospital['hospital_images']);die;
                     if(!empty($hospital['hospital_images'])) {
                      
                      foreach ($hospital['hospital_images'] as $key => $hospital_img) {  ?>
                    
                        <li <?php if(isset($class)?$class:''); ?>>
                        <div class="pace-list"> 

                          <a class="fancybox" rel="gallery1" href="<?php echo $hospital_img['image'];?>">
                              <img src="<?php echo $hospital_img['image'];?>" alt="" />
                              </a>
                           </div>
                        </li>
                      <?php } }  ?>
                  </ul>
                  <div class="away-part pull-right"> 
                    <a href="#" data-toggle="modal" data-target="#myModal"> 
                      <i class="fa fa-map-marker" aria-hidden="true"></i> 0.8 km away 
                    </a>
                    <br>
                    <a href="#"> Show On Map 
                      <i class="fa fa-location-arrow"></i> 
                    </a>
                  </div>
                </div>
            </div>
          </div>
      </div>
    <?php 
    } 
  } 
?>