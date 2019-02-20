<?php
use yii\web\view;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\helpers\Html;

use common\models\User;

$baseurl =\Yii::$app->getUrlManager()->getBaseUrl();
$address =$model;

if($address->type == 'Hospital'){
    $class="bg-red";
}
else{
    $class="bg-green";
}
?>
<div class="col-sm-12" id="singlediv">
    <div id="address_main">

        <div class="info-box">
            <a href="javascript:void(0)">
              <span class="info-box-icon <?php echo $class?>">
                <i class="ion ion-ios-people-outline">
                    <p class="info-text"><?php echo $address->type; ?></p>
                </i>
              </span>
            </a>
            <div class="info-box-content">

                <span class="info-box-values">
                    <p class="address_edit_btn"><a href="javascript:void(0)" onclick="return updateAddress(<?php echo $address->id; ?>);"><i class="fa fa-pencil"></i></a></p>
                    <p><?php echo $address->name; ?></p>
                    <p><?php echo $address->address; ?></p>
                    <p><?php echo $address->city.', '.$address->state.', '.$address->country; ?></p>

                </span>
            </div><!-- /.info-box-content -->
        </div>


    </div>
</div>