<?php
use yii\web\view;
use common\models\Groups;
$userid_login=1;
if(Yii::$app->user->isGuest){
    $userid_login=0;
}
else{
    $login_user_data=Yii::$app->user->identity;
    $login_user_data=Yii::$app->user->identity;
    if($login_user_data->groupid==Groups::GROUP_PATIENT){
        $userid_login=0;
    }
}

if($userid_login == 0){
    $this->registerJsFile(
        '@web/js/search.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );

    if(isset($query_slug) && !empty($query_slug)) {
        $search=$query_slug;
    }
    elseif(isset($result_type) && !empty($result_type)) {
        $search=$result_type;
    }
    else{
        $search='';
    }

    ?>

    <div class="topheader-searchbar">
        <div class="inputbar">
            <input id="search-legal-users" value="<?php echo Yii::t('db',$search); ?>" type="text" placeholder="<?php echo Yii::t('db','Doctors, hospitals, speciality, treatments');?>">
            <div class="search-icon"> <i class="fa fa-search"></i>
            </div>
        </div>
    </div>
	

<?php } ?>