<?php
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;

?>
<?php
 $base_url= Yii::getAlias('@frontendUrl'); 
 $groupAlias= DrsPanel::getusergroupalias($doctor['user_id']);
 $listAddress=DrsPanel::getBookingAddressShifts($doctor['user_id'],date('Y-m-d'));
 if(!empty($listAddress)){
     $firstAddress=$listAddress[0];
     $fees=$firstAddress['consultation_fees'];
     $address=$firstAddress['address_show'];
 }
 else{
     $fees='NA';
     $address='';
 }
 ?>
<div class="col-sm-6">
    <div class="doctoe_listing_one profile_detail_section" data-url="<?php echo $base_url.'/'.$groupAlias.'/'.$doctor['slug']?>" data-slug="<?php echo $doctor['slug']?>">
        <div class="doctor_detail_left">
            <div class="image_doc">
                <?php $image = DrsPanel::getUserAvator($doctor['user_id']);?>
                <?php    echo Lightbox::widget([
                                            'files' => [
                                            [
                                            'thumb' => DrsPanel::getUserThumbAvator($doctor['user_id']),
                                            'original' => $image,
                                            'title' => $doctor['name'],
                                            ],
                                            ]
                                            ]); ?>
                <!-- <img src="<?php //echo $image; ?>" alt="image"/> -->
            </div>
            <div class="doc_specify">
                <h4> <a href="<?php echo $base_url.'/'.$groupAlias.'/'.$doctor['slug']?>"><?php echo $doctor['name']; ?></a>
                    <div class="review_top_details pull-right">
                                        <div class="rate-ex1-cnt yellow-star">
                                            <?php
                                            $total_rating = Drspanel::getRatingStatus($doctor['user_id']);
                                            if(!empty($total_rating))
                                            { ?>
                                                <i class="fa fa-star" aria-hidden="true"></i> <?php echo isset($total_rating['rating'])?$total_rating['rating']:'0';?>
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                    </div> 

                </h4>
                <p><?php echo  str_replace(',', ', ', $doctor['degree']); ?> </p>
                <p class="text"><?php
                    echo $address; ?> </p>
            </div>
            <div class="doctor-feeandm">
                <ul>
                <?php if($doctor['groupid']==\common\models\Groups::GROUP_DOCTOR) { ?>
                    <li> Exp. <?php echo !empty($doctor['experience'])?($doctor['experience']):'0' ?> Years  </li>
                      <li> <i class="fa fa-map-marker" aria-hidden="true"></i> <a href="#">5.5KM. Away</a> </li>
                      <li> <i class="fa fa-rupee" aria-hidden="true"></i><?= $fees; ?></li>
                    <?php }  ?>

                </ul>
            </div>
        </div>
        
        <div class="button_bottom_c text-center">
            <?php if (!Yii::$app->user->isGuest) { ?>
                <a href="javascript:void(0)" data-slug="<?php echo $doctor['slug']; ?>"
                   id="id_<?php echo $doctor['slug']?>" class="view_pro_appoint new_bookbtn doctor-addresss-list" >
                    Book Appointment
                </a>
            <?php } else{ ?>
                <a href="javascript:void(0)" class="view_pro_appoint new_bookbtn modal-call" id="login-popup"> Book Appointment</a>
            <?php } ?>
        </div>
    </div>
</div>