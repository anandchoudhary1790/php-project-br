<?php $baseUrl= Yii::getAlias('@frontendUrl'); ?>
<section class="online-appointment">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-5 order-lg-2 text-center">
        <div class="appscerrnpart-slider wow bounceInRight">
         <div class="swiper-container">
          <div class="swiper-wrapper">
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/main.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
            <div class="swiper-slide my-screenshots"><img class="img-fluid" src="<?php echo $baseUrl ?>/images/screenshots/1.jpg" alt=""></div>
          </div>
          <!-- Add Arrows -->
          <div class="swiper-button-next swiper-button-black"></div>
          <div class="swiper-button-prev swiper-button-black"></div>
        </div>
      </div>

    </div>
    <div class="col-lg-7 order-lg-1">
      <div class="p-5 mx-auto wow bounceInLeft">
        <h2 class="font-weight-light">Find And </h2>
        <h2 class="display-6 lg_pb_30">Book Appointment</h2>
        <ul class="mobile-text">
          <li><i><img src="<?php echo $baseUrl ?>/images/check-icon.png"></i> 100,000 Verified doctors</li>
          <li><i><img src="<?php echo $baseUrl ?>/images/check-icon.png"></i> 3M+ Patient recommendations</li>
          <li><i><img src="<?php echo $baseUrl ?>/images/check-icon.png"></i> 25M Patients/year</li>
        </ul>
        <a href="<?php echo $baseUrl?>/specialization?type=doctor" class="find-btn hvr-wobble-vertical">Find me the right doctor</a>
      </div>
    </div>
  </div>
</div>
</section>


