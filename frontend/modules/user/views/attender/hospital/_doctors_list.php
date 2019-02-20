<?php
use common\components\DrsPanel;
use common\models\UserProfile;
$base_url = Yii::getAlias('@frontendUrl');

if(!empty($data_array)){ ?>
    <?php
    $categories=$data_array['speciality'];
    $doctorList=$data_array['data'];
    ?>
    <div class="slider multiple-items">
        <?php
        if(!empty($categories)) {
            foreach ($categories as $hslider) { ?>
                <?php if($actionType == 'appointment') {
                    $searchUrl=yii\helpers\Url::to(['/attender/appointments?type='.$type.'&speciality='.$hslider['id']]);
                }elseif($actionType == 'user_history'){
                    $searchUrl=yii\helpers\Url::to(['/attender/user-statistics-data?&speciality='.$hslider['id']]);
                }else{
                    $searchUrl=yii\helpers\Url::to(['/attender/patient-history?&speciality='.$hslider['id']]);
                }?>
                <div onclick="location.href='<?php echo $searchUrl; ?>';">
                    <div class="detailmain-box <?php echo ($selected_speciality == $hslider['id'])?'detailmain_selected' : '' ?>">
                        <div class="detial-imgmain">
                            <?php if($hslider['icon']=='') { ?>
                                <img src="<?php echo $base_url?>/images/doctors1.png" alt="image">
                            <?php } else { ?>
                                <img src="<?php echo $hslider['icon']; ?>" alt="image">
                            <?php  }?>
                        </div>
                    </div>
                    <div class="hos-discription"> <p><?php echo $hslider['value']?></p><span>(<?php echo isset($hslider['count'])?$hslider['count']:'0' ?>) <span></div>
                </div>
            <?php } }?>
    </div>
    <div class="mt-top25p" id="doctors_list_div">
        <div class="row">
            <?php
            if(!empty($doctorList)){
                foreach ($doctorList as $doctor) {
                    ?>
                    <div class="col-sm-6">
                        <div class="pace-part main-tow">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="pace-left">
                                        <?php if(!empty($doctor['avatar'])) { ?>
                                            <img src="<?php echo DrsPanel::getUserAvator($doctor['user_id'])?>" alt="image">
                                        <?php }  else { ?>
                                            <img src="<?php echo $base_url?>/images/doctor-profile-image.jpg" alt="image">
                                        <?php    }?>
                                    </div>
                                    <div class="pace-right">
                                        <h4><?php echo $doctor['name']?>
                                            <span class="ratingpart red-star pull-right"> <i class="fa fa-heart"></i>
                                             4.5 </span>
                                        </h4>
                                        <p> <?php echo $doctor['speciality']?> </p>
                                    </div>
                                    <div class="doc-listboxes">
                                        <div class="pull-left"> <p> Experience </p>  <p><strong> <?php echo $doctor['experience']?> </strong> years </p></div>
                                        <div class="pull-right text-right">
                                            <p> Fees: <strong class="cut-price"> <i class="fa fa-rupee"></i> <?php echo $doctor['fees'] ?> </strong> <strong> <i class="fa fa-rupee"></i><?php echo $doctor['fees'] ?> </strong> </p>
                                        </div>
                                        <div class="bookappoiment-btn">
                                            <?php if($actionType == 'appointment') {
                                                $doctorUrl= $base_url.'/attender/appointments/'.$doctor['slug'];
                                                $label='Book Appointment';
                                            }elseif($actionType == 'user_history') {
                                                $doctorUrl= $base_url.'/attender/user-statistics-data/'.$doctor['slug'];
                                                $label='View History';
                                            }
                                            else{
                                                $doctorUrl= $base_url.'/attender/patient-history/'.$doctor['slug'];                             $label='View History';
                                            }?>
                                            <?php $groupAlias= DrsPanel::getusergroupalias($doctor['user_id'])?>
                                            <a href="<?php echo $doctorUrl; ?>" class="bookinput bookinput_color" ><?php echo $label; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } } ?>
        </div>
    </div>
<?php }
else{

}
?>