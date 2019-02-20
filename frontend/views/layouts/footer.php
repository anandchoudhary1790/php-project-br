<?php 
use yii\helpers\Html;
use common\models\MetaValues;
$social_links = MetaValues::socialLinks();
$baseUrl= Yii::getAlias('@frontendUrl');
?>
<!-- Footer -->
<footer class="bg-black" >
  <div class="container">
     <div class="row">
       <div class="col-sm-4">
           <h3 class="lg_pb_20">MakeCall Care</h3>
           <ul class="footer-link">
               <li><a href="<?php echo $baseUrl ?>/page/about-us"><i class="fa fa-caret-right"></i> About Us</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/our-team"><i class="fa fa-caret-right"></i> Our Team</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/why-us"><i class="fa fa-caret-right"></i> Why us?</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/press"><i class="fa fa-caret-right"></i> Press</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/contact-us"><i class="fa fa-caret-right"></i> Contact us</a></li>
           </ul>
       </div>
       
       <div class="col-sm-4">
           <h3 class="lg_pb_20">Patients</h3>
           <ul class="footer-link">
               <li><a href="<?php echo  $baseUrl?>"><i class="fa fa-caret-right"></i> Home</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/how-it-works"><i class="fa fa-caret-right"></i> How it works?</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/get-started"><i class="fa fa-caret-right"></i> Get started</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/faq"><i class="fa fa-caret-right"></i> FAQ</a></li>
               <li><a href="<?php echo $baseUrl ?>/page/blog"><i class="fa fa-caret-right"></i> Blog</a></li>
           </ul>
       </div>
       
       <div class="col-sm-4">
           <h3 class="lg_pb_20">Follow us</h3>
           <ul class="footer-link">
            <?php 
            if(!empty($social_links)){
               foreach ($social_links as $social_link) { ?>
               <li><a href="https://<?php echo $social_link->value?>" target="_blank"><i class="fa fa-caret-right"></i> <?php echo $social_link->label; ?></a></li>
               <?php }
           } ?>
       </ul>
   </div>
</div>
</div>
<!-- /.container -->
<div class="copy-part">
 <p><?php echo MetaValues::copyright();?></p>
</div>
    <input type="hidden" name="uribase" id="uribase" value="<?php echo $baseUrl; ?>"/>

</footer>


