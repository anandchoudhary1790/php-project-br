<?php
namespace frontend\controllers;

use common\models\MetaValues;
use common\models\PatientMembers;
use common\models\UserAddress;
use common\models\UserAppointment;
use common\models\UserSchedule;
use common\models\UserFavorites;
use common\models\UserScheduleDay;
use common\models\UserScheduleSlots;
use frontend\models\AppointmentForm;
use Yii;
use common\models\User;
use common\models\UserProfile;
use yii\helpers\Url;
use yii\web\Controller;
use yii\db\Query;
use frontend\modules\user\models\LoginForm;
use yii\data\Pagination;
use common\models\Groups;
use common\components\DrsPanel;
use yii\web\NotFoundHttpException;

/**
 * Search controller
 */
class SearchController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'set-locale'=>[
                'class'=>'common\actions\SetLocaleAction',
                'locales'=>array_keys(Yii::$app->params['availableLocales'])
            ]
        ];
    }

    public function actionIndex(){
        $string=Yii::$app->request->queryParams;
        $baseurl=Yii::$app->getUrlManager()->getBaseUrl();
        $type=''; $recordlimit = 50;$count_result=0;
        $count_result1=0; $getlastPagination = $getlastPagination1=0; $cityslug=0;
        if(isset($string['results_type']) && !empty($string['results_type'])){
            $type=strtolower($string['results_type']);
        }

        if(isset($string['page']) && !empty($string['page'])){
            $pagination = $string['page'];
            $page = $string['page'] - 1;
            $offset= $recordlimit * $page ;
        }
        else{
            $pagination = 1;
            $offset=0;
        }
        $lists=$lists2= array();$user = array(); $v1 = '';$city='';$q='';

        if(isset($string['city']) && !empty($string['city'])){
            $city=Appelavocat::slugifyCity($string['city']);
            if(isset($_COOKIE['location_filter'])){
                $cookie = $_COOKIE['location_filter'];
                //$cookie = stripslashes($cookie);
                $location = json_decode($cookie, true);
                $locationname=Appelavocat::slugifyCity($location['name']);
                if($locationname == $city){
                    $user=Appelavocat::getLocationUserList($location['lat'],$location['lng']);
                }
                else{
                    $fetchlatlong=Appelavocat::get_lat_long($city);
                    if(!empty($fetchlatlong)){
                        $user=Appelavocat::getLocationUserList($fetchlatlong['lat'],$fetchlatlong['lng']);
                        $setlatlong=$this->setLocationCookieFilter($fetchlatlong);

                    }
                }
            }
            else{
                $fetchlatlong=Appelavocat::get_lat_long($city);
                if(!empty($fetchlatlong)){
                    $user=Appelavocat::getLocationUserList($fetchlatlong['lat'],$fetchlatlong['lng']);
                    $setlatlong=$this->setLocationCookieFilter($fetchlatlong);

                }
            }
            $cityslug=1;
        }

        if($type == Groups::GROUP_DOCTOR_LABEL || $type == Groups::GROUP_HOSPITAL_LABEL){
            $typeSlug=$type;
            if(isset($string['q']) && !empty($string['q'])){
                if($city != ''){
                    return $this->redirect($baseurl.'/'.$typeSlug.'/'.$city.'?q='.$string['q']);
                }
                else{
                    return $this->redirect($baseurl.'/'.$typeSlug.'?q='.$string['q']);
                }
            }
            else{
                if($city != ''){
                    return $this->redirect($baseurl.'/'.$typeSlug.'/'.$city);
                }
                else{
                    return $this->redirect($baseurl.'/'.$typeSlug);
                }
            }
        }
        elseif($type == 'category'){
            $q=$string['q'];
            return $this->redirect($baseurl.'/'.$type.'/'.$string['q'].'/'.$city);
        }
        else{
            $lists= new Query();
            $lists = UserProfile::find();
            $lists->joinWith('user');
            $lists->where(['user_profile.groupid'=>array(Groups::GROUP_DOCTOR,Groups::GROUP_HOSPITAL)]);
            if((isset($string['city']) && !empty($string['city']))){
                $lists->andWhere(['user.id' => $user]);
            }

            $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);

            $countQuery = clone $lists;
            $totalpages = new Pagination(['totalCount' => $countQuery->count()]);

            $lists->limit($recordlimit);
            $lists->offset($offset);
            if(!empty($user)){
                $lists->orderBy([new \yii\db\Expression('FIELD (user.id, ' . implode(',',(array_values($user))) . ')')]);
            }
            $command = $lists->createCommand();
            $lists = $command->queryAll();

        }

        if (!\Yii::$app->user->isGuest) {
            $userid=   Yii::$app->user->identity->id;
        }
        else{ $userid=0; }


        if(isset($totalpages)){
            $count_result=$totalpages->totalCount;
        }
        $getlastPagination=DrsPanel::getlastPagination($count_result,$recordlimit);
        return $this->render('search',['lists'=>$lists,'userid'=>$userid,'city'=>$city,'result_type'=>$type,'query_slug'=>$q,'count_result'=>$count_result,'page'=>$pagination,'offset'=>$offset,'recordlimit'=>$recordlimit,'getlastPagination'=>$getlastPagination,'string'=>$string,'lists2'=>$lists2,'getlastPagination1'=>$getlastPagination1,'cityslug'=>$cityslug,'selected_filter' => '']);
    }

    /*Doctor Profile or all doctors list search results*/
    public function actionDoctor($slug = ''){
        $loginID=   isset(Yii::$app->user->identity->id)?Yii::$app->user->identity->id:'';
        $profile=array();
        if(!empty($slug)){
            $profile = UserProfile::findOne(['slug'=>$slug]);
        }
        if(!empty($profile)){
            $groupid=$profile->groupid;
            $user = User::findOne(['id'=>$profile->user_id]);

            return $this->render('details', [
                'profile' => $profile,'user'=>$user,'groupid'=>$groupid,
                'loginid' => $loginID]);
        }
        else {
            $string=Yii::$app->request->queryParams;
            $groupid=Groups::GROUP_DOCTOR;
            $lists = $this->getSearchResults('doctor',$slug,$groupid,$string);

            $out = array('result'=>'success','fullpath'=>0,'filter'=>'Doctor','slug'=>'','path'=>'/search?results_type=doctor&q=');
            $this->setSearchCookie($out);

            if (!\Yii::$app->user->isGuest) {
                $userid=   Yii::$app->user->identity->id;
            }
            else{ $userid=0; }
            $loginform = new LoginForm();
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                $loginP=$this->searchResultLogin($loginform,$post);
                if($loginP['type'] == 'success'){
                    $userid=$loginP['userid']; $groupid=$loginP['groupid'];
                    return $this->refresh();
                }
                elseif($loginP['type'] == 'redirect'){
                    return $this->redirect([$loginP['group'].'/dashboard']);
                }
                else{
                    return $this->redirect(['/login']);
                }
            }

            $totalpages=$lists['totalpages'];
            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            else{
                $count_result=0;
            }

            $model = new UserProfile();
            $specialities = MetaValues::find()->orderBy('id asc')
                ->where(['key'=>5])->all();

            $treatments = MetaValues::find()->orderBy('id asc')
                ->where(['key'=>9])->all();
            return $this->render('search',['lists'=>$lists['lists'],'loginform'=>$loginform,'userid'=>$userid,'city'=>$lists['city'],'result_type'=>$lists['type'],'query_slug'=>$lists['q'],'count_result'=>$count_result,'page'=>$lists['pagination'],'offset'=>$lists['offset'],'recordlimit'=>$lists['recordlimit'],'string'=>$string,'cityslug'=>1,'$getlastPagination' => 0,'model' => $model,'specialities' => $specialities,'treatments' => $treatments,'selected_filter' =>'']);

        }
    }

    /*Hospital Profile or all doctors list search results*/
    public function actionHospital($slug = ''){
        $loginID=   isset(Yii::$app->user->identity->id)?Yii::$app->user->identity->id:'';
        $profile=array();
        if(!empty($slug)){
            $profile = UserProfile::findOne(['slug'=>$slug]);
        }
        if(!empty($profile)){
            $string=Yii::$app->request->queryParams;

            $groupid=$profile->groupid;
            $user = User::findOne(['id'=>$profile->user_id]);
            $hospital_id=$profile->user_id;
            $getspecialities = Drspanel::getMyHospitalSpeciality($hospital_id);

            if(isset($string['speciality']) && !empty($string['speciality'])){
                $selected_speciality=$string['speciality'];
            }
            else{
                $selected_speciality=0;
            }

            return $this->render('details', [
                'profile' => $profile,'user'=>$user,'groupid'=>$groupid ,'getspecialities' =>$getspecialities,'selected_speciality'=>$selected_speciality,'loginID' => $loginID
            ]);
        }
        else {
            $string=Yii::$app->request->queryParams;
            $groupid=Groups::GROUP_HOSPITAL;
            $lists = $this->getSearchResults('hospital',$slug,$groupid,$string);

            $out = array('result'=>'success','fullpath'=>0,'filter'=>'Lawyer','slug'=>'','path'=>'/search?results_type=hospital&q=');
            $this->setSearchCookie($out);

            if (!\Yii::$app->user->isGuest) {
                $userid=   Yii::$app->user->identity->id;
            }
            else{ $userid=0; }
            $loginform = new LoginForm();
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                $loginP=$this->searchResultLogin($loginform,$post);
                if($loginP['type'] == 'success'){
                    $userid=$loginP['userid']; $groupid=$loginP['groupid'];
                    return $this->refresh();
                }
                elseif($loginP['type'] == 'redirect'){
                    return $this->redirect([$loginP['group'].'/dashboard']);
                }
                else{
                    return $this->redirect(['/login']);
                }
            }

            $totalpages=$lists['totalpages'];
            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            else{
                $count_result=0;
            }
            //  $getlastPagination=Appelavocat::getlastPagination($count_result,$lists['recordlimit']);

            //$clists = $this->getClaimResults('lawyer',$slug,Groups::GROUP_LAWYER,$string,$lists,$getlastPagination);

            return $this->render('search',['lists'=>$lists['lists'],'loginform'=>$loginform,'userid'=>$userid,'city'=>$lists['city'],'result_type'=>$lists['type'],'query_slug'=>$lists['q'],'count_result'=>$count_result,'page'=>$lists['pagination'],'offset'=>$lists['offset'],'recordlimit'=>$lists['recordlimit'],'string'=>$string,'cityslug'=>1]);

        }
    }

    public function actionSpecialization($slug = ''){
        if(!empty($slug)){
            $string=Yii::$app->request->queryParams;
            // pr($string);die;
            $string['specialization']=$slug;
            if(isset($string['type']) && $string['type'] == 'hospital'){
                $typestring='hospital';
                $groupid=Groups::GROUP_HOSPITAL;
            }
            else{
                $typestring='doctor';
                $groupid=Groups::GROUP_DOCTOR;
            }
            $lists = $this->getSearchResults($typestring,$slug,$groupid,$string);


            $out = array('result'=>'success','fullpath'=>0,'filter'=>'Specialization','slug'=>'','path'=>'/search?results_type=specialization&q=');
            $this->setSearchCookie($out);

            if (!\Yii::$app->user->isGuest) {
                $userid=   Yii::$app->user->identity->id;
            }
            else{ $userid=0; }
            $loginform = new LoginForm();
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                $loginP=$this->searchResultLogin($loginform,$post);
                if($loginP['type'] == 'success'){
                    $userid=$loginP['userid']; $groupid=$loginP['groupid'];
                    return $this->refresh();
                }
                elseif($loginP['type'] == 'redirect'){
                    return $this->redirect([$loginP['group'].'/dashboard']);
                }
                else{
                    return $this->redirect(['/login']);
                }
            }

            $totalpages=$lists['totalpages'];
            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            else{
                $count_result=0;
            }
            // $getlastPagination=Appelavocat::getlastPagination($count_result,$lists['recordlimit']);

            return $this->render('search',['lists'=>$lists['lists'],'loginform'=>$loginform,'userid'=>$userid,'city'=>$lists['city'],'result_type'=>$lists['type'],'query_slug'=>$lists['q'],'count_result'=>$count_result,'page'=>$lists['pagination'],'offset'=>$lists['offset'],'recordlimit'=>$lists['recordlimit'],'string'=>$string,'cityslug'=>1,'$getlastPagination' => 0]);

        }
        else{
            $string=Yii::$app->request->queryParams;
            $type='';
            if(isset($string['type']) && $string['type'] != ''){
                $type = $string['type'];
                if($type == 'doctor'){
                    $groupid = Groups::GROUP_DOCTOR;
                }
                elseif($type == 'hospital'){
                    $groupid = Groups::GROUP_HOSPITAL;
                }
                else{
                    $type = 'doctor';
                    $groupid = Groups::GROUP_DOCTOR;
                }
            }
            else{
                $type = 'doctor';
                $groupid = Groups::GROUP_DOCTOR;
            }
            $lists= new Query();
            $lists=UserProfile::find();
            $lists->joinWith('user');
            $lists->where(['user_profile.groupid'=>$groupid]);
            $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
            $command = $lists->createCommand();
            $countQuery = clone $lists;
            $countTotal=$countQuery->count();
            if($groupid == Groups::GROUP_HOSPITAL){
                $fetchCount=DrsPanel::fetchHospitalSpecialityCount($command->queryAll());
            }
            else{
                $fetchCount=Drspanel::fetchSpecialityCount($command->queryAll());
            }
            $s_list=DrsPanel::getSpecialityWithCount('speciality',$fetchCount);
            return $this->render('specialization',['lists' => $s_list,'type' => $type]);
        }
    }

    public function actionTreatment($slug = ''){
        if(!empty($slug)){
            $string=Yii::$app->request->queryParams;
            // pr($string);die;
            $string['treatment']=$slug;
            if(isset($string['type']) && $string['type'] == 'hospital'){
                $typestring='hospital';
                $groupid=Groups::GROUP_HOSPITAL;
            }
            else{
                $typestring='doctor';
                $groupid=Groups::GROUP_DOCTOR;
            }
            $lists = $this->getSearchResults($typestring,$slug,$groupid,$string);


            $out = array('result'=>'success','fullpath'=>0,'filter'=>'Specialization','slug'=>'','path'=>'/search?results_type=specialization&q=');
            $this->setSearchCookie($out);

            if (!\Yii::$app->user->isGuest) {
                $userid=   Yii::$app->user->identity->id;
            }
            else{ $userid=0; }
            $loginform = new LoginForm();
            if(Yii::$app->request->post()){
                $post = Yii::$app->request->post();
                $loginP=$this->searchResultLogin($loginform,$post);
                if($loginP['type'] == 'success'){
                    $userid=$loginP['userid']; $groupid=$loginP['groupid'];
                    return $this->refresh();
                }
                elseif($loginP['type'] == 'redirect'){
                    return $this->redirect([$loginP['group'].'/dashboard']);
                }
                else{
                    return $this->redirect(['/login']);
                }
            }

            $totalpages=$lists['totalpages'];
            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            else{
                $count_result=0;
            }
            // $getlastPagination=Appelavocat::getlastPagination($count_result,$lists['recordlimit']);

            return $this->render('search',['lists'=>$lists['lists'],'loginform'=>$loginform,'userid'=>$userid,'city'=>$lists['city'],'result_type'=>$lists['type'],'query_slug'=>$lists['q'],'count_result'=>$count_result,'page'=>$lists['pagination'],'offset'=>$lists['offset'],'recordlimit'=>$lists['recordlimit'],'string'=>$string,'cityslug'=>1,'$getlastPagination' => 0]);

        }
        else{
            $string=Yii::$app->request->queryParams;
            $type='';
            if(isset($string['type']) && $string['type'] != ''){
                $type = $string['type'];
                if($type == 'doctor'){
                    $groupid = Groups::GROUP_DOCTOR;
                }
                elseif($type == 'hospital'){
                    $groupid = Groups::GROUP_HOSPITAL;
                }
                else{
                    $type = 'doctor';
                    $groupid = Groups::GROUP_DOCTOR;
                }
            }
            else{
                $type = 'doctor';
                $groupid = Groups::GROUP_DOCTOR;
            }
            $lists= new Query();
            $lists=UserProfile::find();
            $lists->joinWith('user');
            $lists->where(['user_profile.groupid'=>$groupid]);
            $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
            $command = $lists->createCommand();
            $countQuery = clone $lists;
            $countTotal=$countQuery->count();
            if($groupid == Groups::GROUP_HOSPITAL){
                $fetchCount=DrsPanel::fetchHospitalSpecialityCount($command->queryAll());
            }
            else{
                $fetchCount=Drspanel::fetchSpecialityCount($command->queryAll());
            }
            $s_list=DrsPanel::getSpecialityWithCount('speciality',$fetchCount);
            return $this->render('specialization',['lists' => $s_list,'type' => $type]);
        }
    }



    public function actionFavorite(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $data['user_id']=$post['user_id'];
            $data['profile_id']=$post['profile_id'];
            if($post['status']==0) {
                $data['status']=UserFavorites::STATUS_FAVORITE;
            }
            else {
                $data['status']=UserFavorites::STATUS_UNFAVORITE;
            }
            $status =  DrsPanel::userFavoriteUpsert($data);
            echo  $this->renderAjax('details/_favorite_status',['status'=> $status]);exit;
        }
        return NULL;
    }

    /*Ajax search result on string enter*/
    public function actionGetSearchList(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data=array();
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $q=trim($post['term']);
            $query = new Query;
            if($q != ''){
                $words=explode(' ',$q);
                $words= DrsPanel::search_permute($words);
                $userprofilelist=DrsPanel::getUserSearchListArray($words);
                foreach($userprofilelist as $result){
                    $avator=DrsPanel::getUserThumbAvator($result['id']);
                    $data[]=array('id'=>$result['id'],'category_check'=>$result['category'],'category'=>Yii::t('db',$result['category']), 'query'=>$result['query'],'label'=>$result['label'],'avator'=>$avator);
                }

                /*Category List search*/
                $categories=DrsPanel::getSpecialitySearchListArray($words);
                if(!empty($categories)){
                    foreach($categories as $cat){
                        $data[]=array('id'=>'','category_check'=>'Specialization','category'=>Yii::t('db','Specialization'),'query'=>$cat['query'],'label'=>Yii::t('db',$cat['label']),'filters'=>'Specialization');
                    }
                }

                /*Treatment List search*/
                $treatments=DrsPanel::getTreatmentSearchListArray($words);
                if(!empty($treatments)){
                    foreach($treatments as $treatment){
                        $data[]=array('id'=>'','category_check'=>'Treatments','category'=>Yii::t('db','Treatments'),'query'=>$treatment['query'],'label'=>Yii::t('db',$treatment['label']),'filters'=>'Treatments');
                    }
                }

                $data[]=array('id'=>'','category_check'=>'Search','category'=>Yii::t('db','Search'),'query'=>$q,'label'=>Yii::t('db','Doctor').' '.Yii::t('db','named').' '.$q,'filters'=>'Doctor','avator'=>'');
                $data[]=array('id'=>'','category_check'=>'Search','category'=>Yii::t('db','Search'),'query'=>$q,'label'=>Yii::t('db','Hospital').' '.Yii::t('db','named').' '.$q,'filters'=>'Hospital','avator'=>'');
                $out = array_values($data);
            }
            else {
                $data= DrsPanel::getTypeDefaultListArray();
                foreach($data as $group){
                    $out[]=array('id'=>'','category_check'=>'Groups','category'=>'Groups','query'=>'','label'=>Yii::t('db',$group['name']),'filters'=>$group['name'],'avator'=>'');
                }
            }
            return $out; exit();
        }

    }

    /*Ajax search result details url*/
    public function actionGetDetailurl(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $id=$post['id'];
            $out=array();

            if($id != ''){
                if($post['search_type'] == 'Specialization'){
                    $path='specialization';
                    $out = array('result'=>'success','fullpath'=>1,'path'=>'/'.$path.'/');
                }
                else{
                    $groupalias=DrsPanel::getusergroupalias($id);
                    if($groupalias){
                        $out = array('result'=>'success','fullpath'=>1,'path'=>'/'.$groupalias.'/');
                    }
                    else{
                        if(isset($post['filter'])){
                            if(isset($post['slug'])){
                                $path='/search?results_type='.strtolower($post['filter']).'&q=';
                                $out = array('result'=>'success','fullpath'=>0,'filter'=>$post['filter'],'slug'=>$post['slug'],'path'=>$path);
                            }
                            else{
                                $out = array('result'=>'success','fullpath'=>0,'filter'=>$post['filter'],'path'=>'/search?results_type='.$post['filter']);
                            }
                            $this->setSearchCookie($out);
                            return $out;exit();
                        }
                    }
                }
                return $out;exit();
            }
            elseif(isset($post['filter'])){
                $restype=strtolower($post['filter']);
                if(isset($post['slug'])){
                    $out = array('result'=>'success','fullpath'=>0,'filter'=>$restype,'slug'=>$post['slug'],'path'=>'/search?results_type='.$restype.'&q=');
                }
                else{
                    $out = array('result'=>'success','fullpath'=>0,'filter'=>$restype,'path'=>'/search?results_type='.$restype);
                }
                $this->setSearchCookie($out);
                return $out;exit();
            }
            else{

            }
        }
        return array('result'=>'fail');exit();
    }

    public function getSearchResults($type,$slug,$groupid,$string){
        $recordlimit = 50;
        $user = array(); $lists = array();
        $v1 = ''; $city=''; $q=''; $specialization = ''; $treatment = '';

        if(isset($string['page']) && !empty($string['page'])){
            $pagination = $string['page'];
            $page = $string['page'] - 1;
            $offset= $recordlimit * $page ;
        }
        else{
            $pagination = 1;
            $offset=0;
        }

        if(isset($string['q'])){
            $q=strtolower($string['q']);
        }
        if($q != ''){
            $q_explode=explode(' ',$q);
            $usersearch=array();
            foreach($q_explode as $word){
                $usersearch[] ="user_profile.name LIKE '%".$word."%'";
            }
            $v1=implode(' or ', $usersearch);
        }
        $lists= new Query();
        $lists=UserProfile::find();
        $lists->joinWith('user');
        $lists->where(['user_profile.groupid'=>$groupid]);
        $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
            'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
        if($v1 != ''){
            $lists->andFilterWhere(['or', $v1]);
        }

        if($groupid == Groups::GROUP_HOSPITAL){
            $addSpeciality=Drspanel::addHospitalSpecialityCount($lists->createCommand()->queryAll());
            $lists->joinWith('hospitalSpecialityTreatment');

            if(isset($string['specialization'])){
                $specialization=strtolower($string['specialization']);
            }

            if($specialization != ''){
                $valuecat=[];
                $metavalues=MetaValues::find()->where(['slug'=>$specialization])->one();
                if($metavalues){
                    $valuecat[]=$metavalues->value;
                }
                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `hospital_speciality_treatment`.`speciality`)', [':key2'=>$sev]);
                }
            }

            if(isset($string['treatment'])){
                $treatment=strtolower($string['treatment']);
            }

            if($treatment != ''){
                $valuecat=[];
                $metavalues=MetaValues::find()->where(['slug'=>$treatment])->one();
                if($metavalues){
                    $valuecat[]=$metavalues->value;
                }
                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `hospital_speciality_treatment`.`treatment`)', [':key2'=>$sev]);
                }
            }
        }
        else{

            if(isset($string['specialization'])){
                $specialization=strtolower($string['specialization']);
            }

            if($specialization != ''){
                $valuecat=[];
                $metavalues=MetaValues::find()->where(['slug'=>$specialization])->one();
                if(!empty($metavalues)){
                    $valuecat[]=$metavalues->value;
                }
                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                }
            }

            if(isset($string['treatment'])){
                $treatment=strtolower($string['treatment']);
            }

            if($treatment != ''){
                $valuecat=[];
                $metavalues=MetaValues::find()->where(['slug'=>$treatment])->one();
                if($metavalues){
                    $valuecat[]=$metavalues->value;
                }
                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `user_profile`.`treatment`)', [':key2'=>$sev]);
                }
            }
        }

        $countQuery = clone $lists;
        $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
        $lists->limit($recordlimit);
        $lists->offset($offset);
        if(!empty($user)){
            $lists->orderBy([new \yii\db\Expression('FIELD (user.id, ' . implode(',',(array_values($user))) . ')')]);
        }
        $command = $lists->createCommand();
        $lists = $command->queryAll();

        return array('lists'=>$lists,'city'=>$city,'type'=>$type,'q'=>$q,'pagination'=>$pagination,'offset'=>$offset,'recordlimit'=>$recordlimit,'totalpages'=>$totalpages);
    }

    public function searchResultLogin($loginform,$post){
        $loginform->load($post);
        if($loginform->login()){
            $userid=   Yii::$app->user->identity->id;
            $groupid=Yii::$app->user->identity->userProfile->groupid;
            if($groupid == Groups::GROUP_LAWYER){ return array('type'=>'redirect','group'=>'lawyer');}
            elseif($groupid == Groups::GROUP_NOTARY){ return array('type'=>'redirect','group'=>'notary');}
            elseif($groupid == Groups::GROUP_FIRM){ return array('type'=>'redirect','group'=>'firm');}
            elseif($groupid == Groups::GROUP_NOTARY_FIRM){ return array('type'=>'redirect','group'=>'firm');}
            else{ return array('type'=>'success','userid'=>$userid,'groupid'=>$groupid);}
        }
        else{
            return array('type'=>'redirect_login');
        }
    }

    public function actionGetLocationList(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data=array();
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $searchTerm=trim($post['term']);
            $key=Yii::$app->params['googleApiKey'];
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $searchTerm;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $array = json_decode($response, true);
            $array = $array['results'];
            echo "<pre>"; print_r($array);exit;
        }
    }

    public function actionGetSearchurl(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            $field_search=$post['field_search'];
            $location_search=$post['location_search'];
            $out=array();

            $baseurl =$_SERVER['HTTP_HOST'];

            $cookielat='';$cookielng='';
            if($location_search != ''){
                if(isset($_COOKIE['location_filter'])){
                    $cookie = $_COOKIE['location_filter'];
                  //  $cookie = stripslashes($cookie);
                    $location = json_decode($cookie, true);
                    $cookiename=$location['name'];
                    $cookieadd=$location['address'];
                    if($cookiename == $location_search || $cookieadd == $location_search){
                        $cookielat=$location['lat'];
                        $cookielng=$location['lng'];
                    }
                    else{
                        setcookie ("location_filter", "", time() - 3600,'/', $baseurl , false);
                    }
                }
            }
            if($field_search != ''){
                if(isset($_COOKIE['search_filter'])) {
                    $cookie = $_COOKIE['search_filter'];
                    //$cookie = stripslashes($cookie);
                    $search = json_decode($cookie, true);

                    if($search['filter'] == 'Category'){
                        $path=$search['path'].$search['slug'];
                        if($cookielat != '' && $cookielng != ''){
                            $cookiename=Appelavocat::slugifyCity($cookiename);
                            $path=$path.'/'.$cookiename;
                        }
                        else{
                            setcookie ("location_filter", "", time() - 3600,'/', $baseurl , false);
                        }
                    }
                    elseif($search['filter'] == 'SubCategory'){
                        $path=$search['path'].$search['slug'];
                        if($cookielat != '' && $cookielng != ''){
                            $cookiename=Appelavocat::slugifyCity($cookiename);
                            $path=$path.'/'.$cookiename;
                        }
                        else{
                            setcookie ("location_filter", "", time() - 3600,'/', $baseurl , false);
                        }
                    }
                    else{
                        $path=$search['path'].$search['slug'];
                        if($cookielat != '' && $cookielng != ''){
                            $cookiename=Appelavocat::slugifyCity($cookiename);
                            $path=$path.'&city='.$cookiename;
                        }
                    }
                    $out = array('result'=>'success','fullpath'=>1 ,'path'=>$path);
                    return $out;exit();
                }
            }
            else{
                setcookie ("search_filter", "", time() - 3600,'/', $baseurl , false);
                $path='/search';
                if($cookielat != '' && $cookielng != ''){
                    $cookiename=Appelavocat::slugifyCity($cookiename);
                    $path=$path.'?city='.$cookiename;
                }
                $out = array('result'=>'success','fullpath'=>1 ,'path'=>$path);
                return $out;exit();
            }
        }
        return array('result'=>'fail');exit();
    }

    public function actionDoctorAddressList(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $date=(isset($post['date']))?date('Y-m-d',strtotime($post['date'])):date('Y-m-d');
            $slug=$post['slug'];
            $doctorProfile=UserProfile::find()->where(['slug'=> $slug])->one();
            if(!empty($doctorProfile)){
                $doctor=User::findOne($doctorProfile->user_id);
                $d_id=$doctor->id;
                $current_login=Yii::$app->user->id;
                $appointments= DrsPanel::getBookingAddressShifts($d_id,$date,$current_login);

                return $this->renderAjax('_doctor_address_list',['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile]);
            }
        }
        return null;
    }

    public function actionAppointmentTime($slug){
        if (!\Yii::$app->user->isGuest) {
            if(Yii::$app->request->post()){
                $post=Yii::$app->request->post();
                $current_login = Yii::$app->user->identity->id;
                $doctorProfile=UserProfile::find()->where(['slug'=> $slug])->one();
                if(!empty($doctorProfile)){
                    $doctor=User::findOne($doctorProfile->user_id);
                    $d_id=$doctor->id;
                    $schedule_id=$post['schedule_id'];
                    $date=$post['nextdate'];

                    $scheduleData = UserSchedule::findOne($schedule_id);
                    if(!empty($scheduleData)){
                        $scheduleDay=UserScheduleDay::find()->where(['user_id'=>$d_id,'date'=>$date,'schedule_id'=>$schedule_id])->one();
                        if(!empty($scheduleDay)){
                            if($scheduleDay->booking_closed == 0){
                                $getSlots = DrsPanel::getBookingShiftSlots($d_id,$date,$schedule_id,array('available','booked'));
                            }
                            else{
                                $getSlots = array();
                            }
                        }
                    }
                    return $this->render('_doctor_appointment_time',['date'=>$date,'doctor'=>$doctor,'scheduleDay'=>$scheduleDay,'schedule'=>$scheduleData,'slots'=>$getSlots,'doctorProfile'=>$doctorProfile]);
                }
                else{
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }
            else{
                $this->redirect(array('search/doctor','slug'=>$slug));
            }
        }
        else{
            Yii::$app->session->setFlash('error', "You are not logged in.");
            return $this->goHome();
        }

    }

    public function actionGetDateTokens(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $current_login = Yii::$app->user->identity->id;
            $d_id=$post['doctor_id'];
            $schedule_id=$post['schedule_id'];
            $date=$post['nextdate'];

            $doctor=User::findOne($d_id);
            $doctorProfile=UserProfile::find()->where(['user_id'=> $d_id])->one();

            $scheduleData = UserSchedule::findOne($schedule_id);
            if(!empty($scheduleData)){
                $scheduleDay=UserScheduleDay::find()->where(['user_id'=>$d_id,'date'=>$date,'schedule_id'=>$schedule_id])->one();
                if(!empty($scheduleDay)){
                    if($scheduleDay->booking_closed == 0){
                        $getSlots = DrsPanel::getBookingShiftSlots($d_id,$date,$schedule_id,array('available','booked'));
                    }
                    else{
                        $getSlots = array();
                    }
                }
            }
            echo $this->renderAjax('_booking_detail_list',['date'=>$date,'doctor'=>$doctor,'scheduleDay'=>$scheduleDay,'schedule'=>$scheduleData,'slots'=>$getSlots,'doctorProfile'=>$doctorProfile]); exit();

        }
    }

    public function actionGetShiftBookingDays(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $doctorProfile=UserProfile::find()->where(['user_id'=> $post['doctor_id']])->one();
            $date = $post['next_date'];

            $shiftData =  Drspanel::getAddressShiftsDays($post);


            return $this->renderAjax('_doctor_appointment_time',
                        ['getshiftaddressdays'=>$shiftData,'doctorProfile' => $doctorProfile]);
            exit;
            
        }
    }

    public function actionBookingConfirm(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $slot_id=explode('-',$post['slot_id']);
            $date=$post['date'];
            $doctorProfile=UserProfile::find()->where(['slug'=> $post['slug']])->one();
            if(!empty($doctorProfile)){
                $doctor=User::findOne($doctorProfile->user_id);
                $slot=UserScheduleSlots::find()->andWhere(['user_id'=>$doctor->id,'id'=>$slot_id[1]])->one();
                if($slot){
                    $schedule=UserSchedule::findOne($slot->schedule_id);
                    $model= new AppointmentForm();
                    $model->doctor_id=$doctor->id;
                    $model->slot_id=$slot->id;
                    $model->schedule_id=$slot->schedule_id;
                    return $this->renderAjax('booking-confirm',
                        ['doctor'=>$doctor,
                            'slot'=>$slot,
                            'schedule'=>$schedule,
                            'address'=>UserAddress::findOne($schedule->address_id),
                            'model'=> $model,
                            'user_type'=>'patient',
                        ]);

                }
            }
        }
        return NULL;
    }

    public function actionBookingConfirmStep2(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $slot_id=$post['slot_id'];
            $id=$post['doctor_id'];
            $doctorProfile=UserProfile::find()->where(['user_id'=>$id])->one();
            if(!empty($doctorProfile)){
                $doctor=User::findOne($doctorProfile->user_id);
                $slot=UserScheduleSlots::find()->andWhere(['user_id'=>$doctor->id,'id'=>$slot_id])->one();
                if($slot){
                    $schedule=UserSchedule::findOne($slot->schedule_id);
                    $model= new AppointmentForm();
                    $model->doctor_id=$doctor->id;
                    $model->slot_id=$slot->id;
                    $model->schedule_id=$slot->schedule_id;
                    $model->user_name=$post['name'];
                    $model->user_phone=$post['phone'];
                    $model->user_gender=$post['gender'];
                    return $this->renderAjax('_booking_confirm_step2.php',
                        ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,   'address'=>UserAddress::findOne($schedule->address_id), 'model'=> $model, 'userType'=>'patient'
                        ]);

                }
            }
        }
        return NULL;
    }

    public function actionAppointmentBooked(){
        $user_id=Yii::$app->user->id;
        $userDetail=UserProfile::find()->where(['user_id'=>$user_id])->one();
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'Does not match require parameters';
        if(Yii::$app->request->post() && Yii::$app->request->isPost) {
            $post=Yii::$app->request->post();
            $doctor_id=$post['AppointmentForm']['doctor_id'];
            $doctor=User::findOne($doctor_id);
            if(!empty($doctor)){
                $doctorProfile=UserProfile::find()->where(['user_id'=> $doctor->id])->one();
                $slot_id=$post['AppointmentForm']['slot_id'];
                $schedule_id=$post['AppointmentForm']['schedule_id'];

                $slot=UserScheduleSlots::find()->where(['id'=>$slot_id,'schedule_id'=>$schedule_id])->one();
                if(!empty($slot)){
                    $schedule=UserSchedule::findOne($schedule_id);
                    $address=UserAddress::findOne($schedule->address_id);
                    if($slot->status == 'available'){
                        $data['UserAppointment']['booking_type']=UserAppointment::BOOKING_TYPE_ONLINE;
                        $data['UserAppointment']['booking_id']=DrsPanel::generateBookingID();
                        $data['UserAppointment']['type']=$slot->type;
                        $data['UserAppointment']['token']=$slot->token;
                        $data['UserAppointment']['user_id']=$user_id;
                        $data['UserAppointment']['user_name']=$post['AppointmentForm']['user_name'];
                        $data['UserAppointment']['user_age']='2002-03-13';
                        $data['UserAppointment']['user_phone']=$post['AppointmentForm']['user_phone'];
                        $data['UserAppointment']['user_address']=isset($post['address'])?$post['address']:'';
                        $data['UserAppointment']['user_gender']=$post['AppointmentForm']['user_gender'];


                        $data['UserAppointment']['doctor_id']=$doctor->id;
                        $data['UserAppointment']['doctor_name']=$doctor['userProfile']['name'];
                        $data['UserAppointment']['doctor_phone']=$address->phone;
                        $data['UserAppointment']['doctor_address']=DrsPanel::getAddressLine($address);
                        $data['UserAppointment']['doctor_address_id']=$schedule->address_id;

                        if(isset($slot->fees_discount) && $slot->fees_discount < $slot->fees && $slot->fees_discount > 0) {
                            $data['UserAppointment']['doctor_fees']=$slot->fees_discount;
                        }
                        else{
                            $data['UserAppointment']['doctor_fees']=$slot->fees;
                        }
                        $data['UserAppointment']['date']=$slot->date;
                        $data['UserAppointment']['weekday']=$slot->weekday;
                        $data['UserAppointment']['start_time']=$slot->start_time;
                        $data['UserAppointment']['end_time']=$slot->end_time;
                        $data['UserAppointment']['shift_name']=$slot->shift_name;
                        $data['UserAppointment']['schedule_id']=$schedule_id;
                        $data['UserAppointment']['slot_id']=$slot_id;
                        $data['UserAppointment']['book_for']=UserAppointment::BOOK_FOR_SELF;
                        $data['UserAppointment']['payment_type']='cash';
                        $data['UserAppointment']['service_charge']=0;
                        $data['UserAppointment']['status']='pending';

                        $addAppointment=DrsPanel::addAppointment($data,'patient');
                        if($addAppointment['type'] == 'model_error'){
                            $response=DrsPanel::validationErrorMessage($addAppointment['data']);
                        }
                        else{
                            //add appointment member to patient record list
                            $member=PatientMembers::find()->where(['user_id'=>$user_id,
                                'name'=>$post['AppointmentForm']['user_name'],'phone'=>$post['AppointmentForm']['user_phone']])->one();
                            if(empty($member)){
                                $memberdata=array();
                                $memberdata['user_id']=$user_id;
                                $memberdata['name']=$post['AppointmentForm']['user_name'];
                                $memberdata['phone']=$post['AppointmentForm']['user_phone'];
                                $memberdata['gender']=$post['AppointmentForm']['user_gender'];
                                $memberInsert=DrsPanel::memberUpsert($memberdata,array());
                            }
                            $response["status"] = 1;
                            $response["error"] = false;
                            $response['message']= 'Success';
                            $response['appointment_id']=$addAppointment['data'];
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message']= 'Slot not available for booking';
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Can not add booking for this slot';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Doctor Details not found';
            }

        }
        echo json_encode($response); exit();
    }

    public function actionGetAppointmentDetail(){
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $appointment_id=$post['appointment_id'];
            $appointment=UserAppointment::find()->where(['id'=>$appointment_id])->one();
            $booking=DrsPanel::patientgetappointmentarray($appointment);
            echo $this->renderAjax('_booking_detail',['booking'=>$booking,'doctor_id'=>$appointment->doctor_id,'userType'=>'patient']); exit();
        }
    }

    public function setTypeCookie($type){
        $baseurl =$_SERVER['HTTP_HOST'];
        $codearray=$type;
        $json = json_encode($codearray, true);
        setcookie('booking_type', $json, time()+60*60, '/',$baseurl , false);
        return $type;
    }

    public function setSearchCookie($codearray){
        $baseurl =$_SERVER['HTTP_HOST'];
        $json = json_encode($codearray, true);
        setcookie('search_filter', $json, time()+60*60, '/',$baseurl , false);
        return $codearray;
    }

    public function actionSetLocationCookie(){
        $post = Yii::$app->request->post();
        $out=array();
        $out['lat']=$post['lat'];
        $out['lng']=$post['lng'];
        $out['address']=$post['address'];
        $out['name']=$post['name'];
        $baseurl =$_SERVER['HTTP_HOST'];
        $json = json_encode($out, true);
        setcookie('location_filter', $json, time()+60*60, '/',$baseurl , false);
        echo \GuzzleHttp\json_encode($out);exit();
    }

    public function setLocationCookieFilter($post){
        $out=array();
        $out['lat']=$post['lat'];
        $out['lng']=$post['lng'];
        $out['address']=$post['address'];
        $out['name']=$post['address'];
        $baseurl =$_SERVER['HTTP_HOST'];
        $json = json_encode($out, true);
        setcookie('location_filter', $json, time()+60*60, '/',$baseurl , false);
       return true;
    }

    public function actionSetLocationCookieNavigation(){
        $post = Yii::$app->request->post();
        $lat=$post['lat'];
        $lng=$post['lng'];
        $location = DrsPanel::getCurrentLocation($lat,$lng);
        echo \GuzzleHttp\json_encode($location);exit();
    }




    public function actionSearchFilter(){
        $selected_filter = array();
        if(Yii::$app->request->post() && Yii::$app->request->isPost) {
            $post=Yii::$app->request->post();
            
            $specialities =isset($post['UserProfile']['speciality'])?$post['UserProfile']['speciality']:'';

            $treatments =isset($post['UserProfile']['treatment'])?$post['UserProfile']['treatment']:'';

            $gender_list =isset($post['UserProfile']['gender'])?$post['UserProfile']['gender']:'';

            $rating  =isset($post['UserProfile']['rating'])?$post['UserProfile']['rating']:'';
            
            $lists= new Query();
            $lists = UserProfile::find();
            $lists->joinWith('user');
            $lists->where(['user_profile.groupid'=>Groups::GROUP_DOCTOR]); 
            if(!empty($specialities)) {
                $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$specialities]); 
            }
            if(!empty($treatments)) {
                $t= 3;
                foreach ($treatments as $key => $treatment) {
                    if($t==3)
                    {
                     $lists->andWhere('find_in_set(:key'.$t.', `user_profile`.`treatment`)', [':key'.$t=>$treatment]);
                    }
                    else {
                    $lists->orWhere('find_in_set(:key'.$t.', `user_profile`.`treatment`)', [':key'.$t=>$treatment]);
                    }
                    $t++;
                }
                
            }
            if(!empty($gender_list)) {
                $lists->andWhere('find_in_set(:key4, `user_profile`.`gender`)', [':key4'=>$gender_list]); 
            }
            if($rating != '' ){
                $rating=explode('-',$rating);
                $lists->andFilterWhere(['between','rating',$rating[0],$rating[1]]);
            }
            $command = $lists->createCommand();
            $lists = $command->queryAll();
            return $this->render('search',['lists' => $lists,'selected_filter' => $post]);
         }
    }

    public function actionShareProfile(){
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $doctor_id=$post['doctor_id'];
            $type=$post['type'];

            $doctorProfile=UserProfile::find()->where(['user_id'=>$doctor_id])->one();

            $url= Url::to(['search/doctor','slug'=>$doctorProfile->slug], true);

            if ($type == 'google') {
                $baseurl = 'https://plus.google.com/share';
            } elseif ($type == 'twitter') {
                $baseurl = 'https://twitter.com/share';
            } elseif ($type == 'facebook') {
                $baseurl = 'http://www.facebook.com/sharer.php';
            } elseif ($type == 'linkedin') {
                $baseurl = 'http://www.linkedin.com/shareArticle';
            } elseif ($type == 'pinterest') {
                $baseurl = 'http://pinterest.com/pin/create/button';
            } else {
                $result = array('status' => 'error');
                echo json_encode($result);
                exit;
            }
            $result = array('status' => 'success', 'url' => $url, 'baseurl' => $baseurl);
            echo json_encode($result);
            exit;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action){
        if (!\Yii::$app->user->isGuest) {
            $groupid=Yii::$app->user->identity->userProfile->groupid;

            if($groupid == Groups::GROUP_DOCTOR){ return $this->redirect(['doctor/dashboard']); }
            elseif($groupid == Groups::GROUP_HOSPITAL){ return $this->redirect(['hospital/dashboard']);}
            elseif($groupid == Groups::GROUP_ATTENDER){ return $this->redirect(['attender/dashboard']);}
            else{

            }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);

    }
}
