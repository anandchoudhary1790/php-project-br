<?php 
use common\components\DrsPanel;
use common\models\UserProfile;
use common\models\Groups;
use common\models\User;
$baseUrl= Yii::getAlias('@frontendUrl'); 


if($groupid == Groups::GROUP_DOCTOR) {
	$title_text_start=Yii::t('db','Doctor');
	$title_group= Yii::t('db','Doctor');
	$title_display_add= DrsPanel::getTitleDisplayAddress($profile->user_id);
	$title_text_end=Yii::t('db','find an appointment online');

	$this->title = Yii::t('db', $title_text_start.' '.$profile->name.' | '.$title_group.' '.$title_display_add.' '.$title_text_end);

	$address_d=DrsPanel::getDisplayAddress($profile->user_id);
	if(!empty($profile->designation)){
		$text=Yii::t('db','Doctor').' '.Yii::t('db','the').' '.Yii::t('db',$profile->designation);
	}
	else{
		$text=Yii::t('db','Doctor');
	}
	$text2=Yii::t('db','at');
	$text3=Yii::t('db','Take an appointment online with');
	\Yii::$app->view->registerMetaTag([
		'name' => 'description',
		'content' => $text3.' '.$profile->name.': '.$text.' '.$text2.' '.$address_d
		]);

	//$this->params['breadcrumbs'][] = $this->title;
	echo $this->render('details/_doctordetail.php',[
		'profile' => $profile,'user'=>$user,'groupid'=>$groupid,'loginid' => $loginid
		]);
}
if($groupid == Groups::GROUP_HOSPITAL) {
	$title_text_start=Yii::t('db','Hospital');
	$title_group= Yii::t('db','Hospital');
	$title_display_add= DrsPanel::getTitleDisplayAddress($profile->user_id);
	$title_text_end=Yii::t('db','find an appointment online');

	$this->title = Yii::t('db', $title_text_start.' '.$profile->name.' | '.$title_group.' '.$title_display_add.' '.$title_text_end);

	$address_d=DrsPanel::getDisplayAddress($profile->user_id);
	if(!empty($profile->designation)){
		$text=Yii::t('db','Hospital').' '.Yii::t('db','the').' '.Yii::t('db',$profile->designation);
	}
	else{
		$text=Yii::t('db','Hospital');
	}
	$text2=Yii::t('db','at');
	$text3=Yii::t('db','Take an appointment online with');
	\Yii::$app->view->registerMetaTag([
		'name' => 'description',
		'content' => $text3.' '.$profile->name.': '.$text.' '.$text2.' '.$address_d
		]);

	//$this->params['breadcrumbs'][] = $this->title;
	echo $this->render('details/_hospitaldetail.php',[
		'profile' => $profile,'user'=>$user,'groupid'=>$groupid,'getspecialities' =>$getspecialities,'selected_speciality'=>$selected_speciality,'loginID' => $loginID
		]);
}
else{

}?>




