 <?php 

 $baseUrl= Yii::getAlias('@frontendUrl'); ?>
 <section class="category-part">
 <?php 
 if(!empty($categories)) {  ?>
  <div class="container PopularCategories">
   <h2 class="wow bounceInLeft">Popular Specialization</h2>
   <div class="slider responsive wow bounceInDown">
    <?php
  

      foreach ($categories as $cat) { ?>
      <div>
        <a href="<?php echo  $baseUrl?>/specialization/<?php echo $cat->slug;?>?type=doctor">
         <div class="category-box">
           <?php if(!empty($cat->image)) { ?>
           <img src="<?php echo $cat->base_path.$cat->file_path.$cat->image;?>" class="cat-img">
           <?php } else { ?>
           <img src="<?php echo $baseUrl?>/images/hospitals_img1.jpg" class="cat-img">
           <?php } ?>
           <p><span><?php echo $cat->label;?></span></p>
         </div>
       </a>
     </div>
     <?php } ?>
 </div>
</div>
<?php } ?>
  <?php if(!empty($treatements)) { ?>
<div class="container PopularDiseases">
 <h2 class="wow bounceInLeft">Popular Treatments</h2>
 <div class="slider responsive wow bounceInUp">
   <?php  foreach ($treatements as $treat) { ?>
    <div>
      <a href="<?php echo  $baseUrl?>/treatment/<?php echo $treat->slug;?>?type=doctor">
       <div class="category-box">
         <?php if(!empty($treat->image)) { ?>
         <img src="<?php echo $treat->base_path.$treat->file_path.$treat->image;?>" class="cat-img">

         <?php } else { ?>
         <img src="<?php echo $baseUrl?>/images/hospitals_img1.jpg" class="cat-img">
         <?php } ?>
         <p><span><?php echo $treat->label?></span></p>
       </div>
     </a>
   </div>
   <?php  
 
} ?>
</div>
</div>
<?php } 
  if(!empty($hospitals)) {  ?>
<div class="container PopularHospitals">
 <h2 class="wow bounceInLeft">Popular Hospitals</h2>
 <div class="slider responsive wow bounceInUp">
  <?php 
    foreach ($hospitals as $hospital) { ?>
    <div>
     <a href="<?php echo  $baseUrl?>/hospital/<?php echo $hospital['slug'];?>">
       <div class="category-box">
         <?php if(!empty($hospital['image'])) { ?>
         <img src="<?php echo $hospital['base_path'].$hospital['file_path'].$hospital['image'] ?>" class="cat-img">
         <?php } else { ?>
         <img src="<?php echo $baseUrl?>/images/hospitals_img1.jpg" class="cat-img">
         <?php } ?>
         <p><span><?php echo $hospital['name']?></span></p>
       </div>
     </a>
   </div>
   <?php 
 } 

?>
</div>
</div>
<?php } ?>
</section>