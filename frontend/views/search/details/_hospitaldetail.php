<?php 
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;

$base_url= Yii::getAlias('@frontendUrl');
$doctorHospital=$profile;
$js="
    $('.fancybox').fancybox();
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<?php $this->title = Yii::t('frontend','DrsPanel ::'.$doctorHospital->name); ?>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                    <?php //echo $this->render('_search'); ?>
                    <div class="pace-part">
                        <div class="row">

                            <div class="col-sm-12">
                                <div class="pace-left">
                                <?php $image = DrsPanel::getUserAvator($doctorHospital->user_id);
                                ?>
                                 <?php    echo Lightbox::widget([
                                            'files' => [
                                            [
                                            'thumb' => $image,
                                            'original' => $image,
                                            'title' => $doctorHospital->name,
                                            ],
                                            ]
                                            ]); ?>
                                </div>
                                <div class="pace-right">
                                    <h4> <?php echo $doctorHospital->name?>
                                        <span class="ratingpart pull-right">

                                    <i class="fa fa-star"></i>  <?php $rating=DrsPanel::getRatingStatus($doctorHospital->user_id); echo $rating['rating'];?>  </span> </h4>
                                    <p> <?php echo $doctorHospital->speciality ?></p>
                                    <p><?php  echo $doctorHospital->address2 ?></p>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="pace-rightpart-show">
                                    <ul class="pacemain-list">
                                        <li>
                                            <div class="pace-list"> 
                                                 <a class="fancybox" rel="gallery1" href="<?php echo $base_url.'/images/img02.jpg';?>">
                                                <img src="<?php echo $base_url.'/images/img02.jpg'?>" alt="" />
                                                </a>
                                           </div>
                                       </li>
                                         <li>
                                            <div class="pace-list"> 
                                                 <a class="fancybox" rel="gallery1" href="<?php echo $base_url.'/images/img03.jpg';?>">
                                                <img src="<?php echo $base_url.'/images/img03.jpg'?>" alt="" />
                                                </a>
                                           </div>
                                       </li>
                                        <li>
                                            <div class="pace-list"> 
                                                 <a class="fancybox" rel="gallery1" href="<?php echo $base_url.'/images/img04.jpg';?>">
                                                <img src="<?php echo $base_url.'/images/img04.jpg'?>" alt="" />
                                                </a>
                                           </div>
                                       </li>
                                    </ul>

                                    <div class="away-part pull-right hide"> <a href="#"> <i class="fa fa-map-marker" aria-hidden="true"></i> 0.8 km away </a>
                                        <a href="#"> Get Direction <i class="fa fa-location-arrow"></i> </a>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="hospitals-detailspt">
                        <div id="parentHorizontalTab">
                            <div class="doctforstab">
                                <ul class="resp-tabs-list hor_1">
                                    <li>Doctors</li>
                                    <li>Speciality/Treatment</li>
                                    <li>Services </li>
                                    <li>About Us</li>
                                </ul>
                            </div>
                            <div class="resp-tabs-container hor_1">
                                <div id="hospital-doctors">
                                    <?php echo $this->render('_doctor-slider', ['hospital' => $doctorHospital,'selected_speciality'=>$selected_speciality,'loginid' => $loginID])?>
                                </div>
                                <div id="hospital-treatment">
                                    <?php echo $this->render('_hospital-treatment',['doctorSpecialities' => $getspecialities])?>
                                </div>
                                <div id="hospital-services">
                                    <div class="checkservices-list">

                                        <?php
                                        $servicesList=$doctorHospital->services;
                                        echo $this->render('_services',['hospital' => $doctorHospital,'servicesList' => $servicesList])?>
                                    </div>
                                </div>
                                 <div id="hospital-about-us">
                                    <?php echo $this->render('_aboutus' , ['user_id' => $doctorHospital->user_id])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php echo $this->render('/layouts/rightside'); ?>
            </div>
        </div>
</section>

