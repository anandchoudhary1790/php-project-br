<?php
use common\components\DrsPanel;
use branchonline\lightbox\Lightbox;
$baseUrl=Yii::getAlias('@frontendUrl');

$groupAlias= DrsPanel::getusergroupalias($doctor['id']);
$listAddress=DrsPanel::getBookingAddressShifts($doctor['id'],date('Y-m-d'));
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
    <div class="doctoe_listing_one profile_detail_section" data-url="<?php echo $baseUrl.'/doctor/'.$doctor['userProfile']['slug']?>" data-slug="<?php echo $doctor['userProfile']['slug']?>">
        <div class="doctor_detail_left">
            <div class="image_doc">
                <?php $image = DrsPanel::getUserAvator($doctor['id']);?>
                <?php    echo Lightbox::widget([
                    'files' => [
                        [
                            'thumb' => DrsPanel::getUserThumbAvator($doctor['id']),
                            'original' => $image,
                            'title' => DrsPanel::getUserName($doctor['id']),
                        ],
                    ]
                ]); ?>
            </div>
            <div class="doc_specify">
                <h4>
                    <a href="<?php echo $baseUrl.'/doctor/'.$doctor['userProfile']['slug']?>"><?php echo DrsPanel::getUserName($doctor['id']);?></a>
                    <div class="pull-right yellow-star">
                        <i class="fa fa-star"></i>
                        <?php $total_rating = Drspanel::getRatingStatus($doctor['id']);
                        if(!empty($total_rating)){
                            echo isset($total_rating['rating'])?$total_rating['rating']:'';   }  ?>
                    </div>
                </h4>
                <p><?php echo $doctor['userProfile']['degree']; ?> </p>
                <p class="text"><?php
                    $addressList = DrsPanel::getUserAddressMeta($doctor['id']);
                    if(!empty($addressList)) {
                        echo $addressList[0]['address_line'];
                    }
                    ?>
                </p>
            </div>
            <div class="doctor-feeandm">
                <ul>
                    <li> Exp. <?php echo isset($doctor['userProfile']['experience'])?$doctor['userProfile']['experience']:'0' ?> Years  </li>
                    <li> <i class="fa fa-map-marker" aria-hidden="true"></i> <a href="#">5.5KM. Away</a> </li>
                    <li> <i class="fa fa-rupee" aria-hidden="true"></i><?= $fees; ?></li>
                </ul>
            </div>
        </div>
        <div class="button_bottom_c text-center hide">
            <a href="#" class="view_pro_appoint new_bookbtn"> Book Appointment </a>
        </div>
        <div class="button_bottom_c text-center patient_book_appointment">
                <a href="javascript:void(0)" data-slug="<?php echo $doctor['userProfile']['slug']; ?>" id="id_<?php echo $doctor['userProfile']['slug']?>" class="view_pro_appoint new_bookbtn doctor-addresss-list" > Book Appointment </a>
        </div>
    </div>
</div>