<?php 
use common\components\DrsPanel;
use common\models\User;
use common\models\UserProfile;
use common\models\Groups;

$baseUrl=Yii::getAlias('@frontendUrl');
?>
<?php if(count($doctors)>0){ foreach($doctors as $key=>$doctor){
    ?>


    <div class="col-sm-6">
        <div class="doctoe_listing_one">
            <div class="doctor_detail_left">
                <div class="image_doc">  <?php if($image_url=DrsPanel::getUserAvator($doctor['doctor_id'])) {?>
                    <img src="<?php echo $image_url; ?>" alt="">
                    <?php } ?></div>
                    <div class="doc_specify">
                        <h4><?php if($doctor_slug =DrsPanel::getPtientDoctorSlug($doctor['doctor_id'])) { ?>
                            <a href="<?php echo $baseUrl.'/doctor/'.$doctor_slug->slug?>"><?php echo $doctor['doctor_name'];?></a>
                            <?php } ?>
                            <div class="pull-right red-star hide"> <i class="fa fa-heart"></i> 4.5 </div>
                            <div class="review_top_details pull-right">
                                        <div class="rate-ex1-cnt">
                                            <?php
                                            $total_rating = Drspanel::getRatingStatus($doctor['doctor_id']);
                                            if(!empty($total_rating)){
                                                for($i=1; $i<=5; $i++){
                                                if($i <= $total_rating['rating']){
                                                    echo '<div class= "rate-btn-'.$i.' rate-btn1 rate-btn-active"> </div>';
                                                }
                                                else{
                                                    echo '<div class= "rate-btn-'.$i.' rate-btn1 rate_disable"> </div>';
                                                }
                                            }
                                        }
                                            ?>
                                        </div>
                                    </div> 
                        </h4>
                            <?php if($doctor_data =DrsPanel::getPtientDoctor($doctor['doctor_id'])) { ?>
                            <p><?php echo  $doctor_data->speciality ?></p>
                            <p class="hide"><?php  echo  $doctor_data->degree ?></p>
                            <?php } ?>
                            <p class="text"><?php echo $doctor['doctor_address'];?> </p>
                        </div>
                        <div class="doctor-feeandm">
                            <ul>
                                <li> Exp. <?php echo  !empty($doctor_data->experience)?$doctor_data->experience:'0' ?> Years </li>
                                <li class="hide"> <i class="fa fa-map-marker" aria-hidden="true"></i> <a href="#">5.5KM. Away</a> </li>
                                <li class="pull-right"> <i class="fa fa-money" aria-hidden="true"></i> <?php echo $doctor['doctor_fees']?> INR </li>
                            </ul>
                        </div>
                    </div>
                    <div class="button_bottom_c text-center hide"> <a href="#" class="view_pro_appoint new_bookbtn"> Book Appointment </a> </div>
                    <div class="button_bottom_c text-right patient_book_appointment">
                        <?php if($doctor_slug =DrsPanel::getPtientDoctorSlug($doctor['doctor_id'])) { ?>
                            <a href="<?php echo $baseUrl?>/doctor/<?php echo $doctor_slug->slug; ?>" class="view_pro_appoint new_bookbtn"> Book Appointment  </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } }?>