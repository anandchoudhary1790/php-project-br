<?php

namespace backend\controllers;

use backend\models\AddAppointmentForm;
use backend\models\DailyPatientLimitForm;
use backend\models\search\UserAppointmentSearch;
use common\components\DrsPanel;
use common\components\DrsImageUpload;
use common\models\Groups;
use common\models\MetaValues;
use common\models\UserAddress;
use common\models\UserAppointment;
use common\models\UserFeesPercent;
use common\models\UserProfile;
use common\models\UserRating;
use common\models\UserSchedule;
use common\models\UserEducations;
use common\models\UserExperience;
use common\models\UserRequest;
use common\models\UserAddressImages;
use Yii;
use common\models\User;
use backend\models\DoctorForm;
use backend\models\AttenderForm;
use backend\models\search\DoctorSearch;
use backend\models\search\UserScheduleSearch;
use backend\models\search\AttenderSearch;
use backend\models\search\UserEducationsSearch;
use backend\models\search\HospitalSearch;
use backend\models\search\UserUserExperienceSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\AddScheduleForm;
use yii\bootstrap\ActiveForm;
use yii\web\UploadedFile;


/**
 * DoctorController implements the CRUD actions for User model.
 */
class DoctorController extends Controller{

    public function behaviors(){
        return [
        'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
        'delete' => ['post'],
        ],
        ],
        ];
    }

    public function beforeAction($action)
    {
        $logined=Yii::$app->user->identity;
        if($logined->role=='SubAdmin'){
            $action=Yii::$app->controller->action->id; 
            $id=Yii::$app->request->get('id'); 
            if(in_array($action,DrsPanel::adminAccessUrl($logined,'hospital')) && $id){
                $isAccess=User::find()->andWhere(['admin_user_id'=>$logined->id])->andWhere(['id'=>$id])->one();
                if(empty($isAccess)){
                    $this->goHome();
                }
            }
        }
        return true;
    }
    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex(){
        $searchModel = new DoctorSearch();
        $logined=Yii::$app->user->identity;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$logined);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DoctorForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->groupid=Groups::GROUP_DOCTOR;
            $model->admin_user_id=Yii::$app->user->id;
            if($res = $model->signup($model)){
                return $this->redirect(['detail', 'id' => $res->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
            ]);
    }

    /**
     * Deatils an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionDetail($id){
        $model =$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $degrees = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>2])->all();
        $specialities = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>5])->all();
         $services=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>11])->all();
        if($userProfile['speciality'])
            $treatment=MetaValues::getValues(9,$userProfile['speciality']);
        else $treatment=[];
        $addressList=DrsPanel::doctorHospitalList($id);
        $listaddress=$addressList['listaddress'];
        $addressProvider=$addressList['addressProvider'];
        $userShift= UserSchedule::find()->where(['user_id'=>$id])->all();
        $week_array=DrsPanel::getWeekArray();
        $availibility_days=array();
        foreach($week_array as $week){
            $availibility_days[]=$week;
        }
        if(empty($userShift)){ $shiftType='new';}
        else{ $shiftType='old';}

        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            if(isset($post['AddScheduleForm'])){
                $addUpdateShift=DrsPanel::addupdateShift($id,$post);
                return $this->redirect(['detail', 'id' => $id]);

            }
            elseif(isset($post['LiveStatus'])){
                $model->admin_status=$post['LiveStatus']['status'];
                if($model->save()){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile status updated!')
                        ]);
                    return $this->redirect(['detail', 'id' => $id]);
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Status not updated!')
                        ]);
                    return $this->redirect(['detail', 'id' => $id]);
                }
            }
            elseif(isset($post['AdminRating'])){
                $type=$post['AdminRating']['type'];
                $userRating=UserRating::find()->where(['user_id'=>$id])->one();
                if(empty($userRating)){
                    $userRating=new UserRating();
                    $userRating->user_id=$id;
                }
                $userRating->show_rating=$type;
                if($type == 'Admin'){
                    $userRating->admin_rating=$post['AdminRating']['rating'];
                }
                if($userRating->save()){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile rating updated!')
                        ]);
                    return $this->redirect(['detail', 'id' => $id]);
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Rating not updated!')
                        ]);
                    return $this->redirect(['detail', 'id' => $id]);
                }
            }
            elseif(isset($post['Fees'])){
                foreach ($post['Fees'] as $key=>$feetype){
                    $getFees=UserFeesPercent::find()->where(['user_id'=>$id,'type'=>$key])->one();
                    if(empty($getFees)){
                        $getFees=new UserFeesPercent();
                        $getFees->user_id=$id;
                        $getFees->type=$key;
                    }
                    $getFees->admin=$feetype['admin'];
                    $getFees->user_provider=$feetype['user_provider'];
                    if($key == 'cancel' || $key == 'reschedule'){
                        $getFees->user_patient=$feetype['user_patient'];
                    }
                    $getFees->save();
                }
                return $this->redirect(['detail', 'id' => $id]);

            }
            elseif(isset($post['UserAddress'])){
                $addAddress=new UserAddress();
                $addAddress->load($post);
                $addAddress->save();
                return $this->redirect(['detail', 'id' => $id]);

            }
            else{
                $model->load($post);
                $userProfile->load($post);
                if(isset($post['UserProfile']['degree'])){
                    $degrees=$post['UserProfile']['degree'];
                    $userProfile->speciality=$post['UserProfile']['speciality'];
                    $treatment=$post['UserProfile']['treatment'];
                    if(!empty($degrees)){
                     $other_degree=false;
                     if (in_array("Other", $degrees)){
                        $other_degree=true;
                           // unset($degrees[array_search("Other", $degrees)]);
                    }
                    $userProfile->degree=implode(',',$degrees);
                    if(isset($post['other_degree']) && !empty($post['other_degree']) && $other_degree){
                        $userProfile->other_degree=$post['other_degree'];
                         /*   if($userProfile->degree){
                                $userProfile->degree=$userProfile->degree.','.$post['other_degree'];
                            }else{
                                $userProfile->degree=$post['other_degree'];
                            } */
                        }else{
                            $userProfile->other_degree=NULL;
                        }
                    }
                    if(!empty($treatment)){
                        $userProfile->treatment=implode(',',$treatment);
                    }
                }

                if(isset($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                    $upload = UploadedFile::getInstance($userProfile, 'avatar');
                    $uploadDir = Yii::getAlias('@storage/web/source/doctors/');  
                    $image_name=time().rand().'.'.$upload->extension;
                    $userProfile->avatar=$image_name;
                    $userProfile->avatar_path='/storage/web/source/doctors/';
                    $userProfile->avatar_base_url =Yii::getAlias('@frontendUrl');
                }

                if($model->save() && $userProfile->save()){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile updated!')
                        ]);
                    if(!empty($upload)){
                        $upload->saveAs($uploadDir .$image_name );  
                    }
                    return $this->redirect(['detail', 'id' => $id]);
                }
            }
        }
        // pr($degrees);die;

        return $this->render('detail', [
            'model' => $model,
            'userProfile'=>$userProfile,'degrees'=>$degrees,'treatment'=>$treatment,'specialities'=>$specialities,
            'addressProvider' => $addressProvider,'shiftType'=>$shiftType,'listaddress'=>$listaddress,'week_array'=>$week_array,'availibility_days'=>$availibility_days,'services' => $services
            ]);
    }
    public function actionMyShifts($id)
    {

        $address_list=DrsPanel::doctorHospitalList($id);
        return $this->render('my-shifts',['doctor_id'=>$id,'address_list'=>$address_list['apiList']]);
    }

    public function actionHospitals($id)
    {
        $userShift= UserSchedule::find()->where(['user_id'=>$id])->all();
                if(empty($userShift)){ $shiftType='new';}
        else{ $shiftType='old';}
        $addressList=DrsPanel::doctorHospitalList($id);
        $listaddress=$addressList['listaddress'];
        $addressProvider=$addressList['addressProvider'];
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            if(isset($post['UserAddress'])){
                    $addAddress=new UserAddress();
                    $addAddress->load($post);
                    $addAddress->save();
                    return $this->redirect(['hospitals', 'id' => $id]);

                }
       }


       return $this->render('hospitals',['addressProvider'=>$addressProvider,'userid' => $id]);
    }


    public function actionAjaxTreatmentList(){
        if(Yii::$app->request->isPost){
            $form = ActiveForm::begin(['id' => 'profile-form']);
            $post=Yii::$app->request->post();

            if(isset($post['id']) && !empty($post['user_id'])){
                $userProfile=UserProfile::findOne(['user_id'=>$post['user_id']]);
                $metavalue=MetaValues::find()->where(['value'=>$post['id']])->one();
                if(!empty($metavalue)){
                    $checkid=$metavalue->id;
                    $treatment=MetaValues::getValues(9,$checkid);
                }
                else{
                    $checkid=0;
                    $treatment=array();
                }
                
                $treatment_list=[];
                foreach ($treatment as $obj) {
                    $treatment_list[$obj->value] = $obj->label;
                }
                return $this->renderAjax('ajax-treatment-list',['form'=>$form,'treatment_list'=>$treatment_list,'userProfile'=>$userProfile]);
            }
        }
    }

    public function actionUpdateAddressModal(){
        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();
            if(isset($post['UserAddress'])){
                $address=UserAddress::findOne($post['UserAddress']['id']);
                $address->load($post);
                $address->save();
                return $this->redirect(['detail', 'id' => $post['UserAddress']['user_id']]);
            }
            else{
                $id=$post['id'];
                $address=UserAddress::findOne($id);
                echo $this->renderAjax('_editAddress',['model'=> $address]); exit;
            }
        }
        echo 'error'; exit;
    }
    public function actionAttenderList($id){
        $searchModel = new AttenderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);

        return $this->render('/user-attender/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'id'           => $id
            ]);
    }
    public function actionAttenderCreate($id)
    {
        if(User::findOne($id)){
            $model = new AttenderForm();
            if (Yii::$app->request->post()) {
                $post=Yii::$app->request->post();
                // pr($post);die;
                if(count($post['AttenderForm']['shift_id'])>0)
                   if(!empty($post['AttenderForm']['shift_id'])){   
                    $post['AttenderForm']['shift_id']=implode(',', $post['AttenderForm']['shift_id']);
                }
                $model->load($post);
                $model->groupid=Groups::GROUP_ATTENDER;
                $model->parent_id=$id;
                if($res = $model->signup()){
                    return $this->redirect(['attender-list', 'id' => $id]);
                }
            }
            $addressList=DrsPanel::doctorHospitalList($id);
            return $this->render('/user-attender/create', [
                'model' => $model,
                'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
                'id'    => $id,
                'hospitals'=>$addressList['listaddress'],
                'shifts'=>Drspanel::shiftList(['user_id'=>$id],'list'),
                ]);
        }else{
            return $this->redirect(['index']);
        }
    }

    public function actionAttenderDetail($id)
    {
        $model =$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $addressList=DrsPanel::attenderHospitalList($id);
        $shiftList=Drspanel::shiftList(['user_id'=>$model->parent_id],'list');
        $selectedShifts=Drspanel::shiftList(['user_id'=>$model->parent_id,'attender_id'=>$id],'list');
        $shiftModels = new AttenderForm();
        $shiftModels->shift_id=array_keys($selectedShifts);
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $model->load(Yii::$app->request->post());
            $userProfile->load(Yii::$app->request->post());
            if($model->save() && $userProfile->save()){

                if(!empty($post['AttenderForm']['shift_id'])){
                    DrsPanel::attenderShiftUpdate($model,$post['AttenderForm']['shift_id']);
                }else{
                    DrsPanel::attenderShiftUpdate($model);
                }
                Yii::$app->session->setFlash('alert', [
                    'options'=>['class'=>'alert-success'],
                    'body'=>Yii::t('backend', 'Profile updated!')
                    ]);
                return $this->redirect(['/doctor/attender-list', 'id' => $model->parent_id]);
            }
        }
        return $this->render('/user-attender/detail', [
            'model' => $model,
            'shiftModels'=>$shiftModels,
            'userProfile'=>$userProfile,
            'hospitals'=>$addressList,
            'shifts'=>$shiftList,
            ]);
    }

    public function actionDailyPatientLimit($id){
        $date=date('Y-m-d');
        $user_id=$id;
        $week=DrsPanel::getDateWeekDay($date);
        $shift_details=$this->setDateShiftData($user_id,$date);

        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $postForm=$post['DailyPatientLimitForm'];
            $addUpdateShift=DrsPanel::updateDateShift($id,$postForm['date'],$postForm);
            return $this->redirect(['update', 'id' => $id]);
        }

        return $this->render('daily-patient-limit', [
            'model' => $this->findModel($id),'userShift'=>$shift_details['userShift'],'listaddress'=>$shift_details['listaddress'],'date'=>$date,'shifts_available'=>$shift_details['shifts_available']
            ]);
    }

    public function actionAddShiftOld($id,$day=null){

        $model =$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $addressList=DrsPanel::doctorHospitalList($id);
        $userShift= UserSchedule::find()->where(['user_id'=>$id])->all();
        $week_array=DrsPanel::getWeekArray();
        $availibility_days=array();
        foreach($week_array as $week){
            $availibility_days[]=$week;
        }
        $newShift= new AddScheduleForm();
        $newShift->user_id=$userProfile->user_id;
        $newShift->weekday=$day;
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $newShift->load($post);
            $post['AddScheduleForm']['user_id']=$id;
            $weekdays=$post['AddScheduleForm']['weekday'];

            $post['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount']))?$post['AddScheduleForm']['consultation_fees_discount']:0;
            $post['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount']))?$post['AddScheduleForm']['emergency_fees_discount']:0;

            if(!$newShift->validShiftTime($newShift)){ 

                $addUpdateShift=DrsPanel::upsertShift($post);
                if(empty($addUpdateShift->getErrors())){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Shift Time Added successfully')
                        ]);
                    return $this->redirect(['add-shift','id'=>$id]);
                }
                
            }
        } 
        return $this->render('shift/_addShift',['model'=>$newShift,
            'userProfile'=>$userProfile,'listaddress'=>$addressList['listaddress'],'weeks'=>$week_array,'availibility_days'=>$availibility_days]);
    }

     public function actionAddShift7Feb($id = NULL){
        $ids=Yii::$app->user->id;
        $date=date('Y-m-d');
        $current_shifts=0;
        $week_array=DrsPanel::getWeekShortArray();
        $availibility_days=array();
        $addAddress=new UserAddress();
        $imgModel = new  UserAddressImages();
        foreach($week_array as $week){
            $availibility_days[]=$week;
        }
        $newShift= new AddScheduleForm();
        $newShift->user_id=$id;
        if($id){
            $userShift= UserSchedule::findOne($id);
            if(!empty($userShift))
            {
               $newShift->setShiftDataAdmin($userShift);
            }
        }
        if(Yii::$app->request->post())
        {
            $post=Yii::$app->request->post();

            $newShift->setPostData($post);

            if(isset($post['UserAddress']))
            {
                $shift=array();
                $shiftcount=$post['AddScheduleForm']['start_time'];
                $canAddEdit = true;
                $msg = ' invalid';
                $errorIndex = 0;
                $newInsertIndex = 0;
                $errorShift = array();
                $insertShift = array();
                $newshiftInsert =0;
                $insertShift = array();
                $addAddress->load(Yii::$app->request->post());
                $addAddress->user_id = $ids;
                $upload = UploadedFile::getInstance($addAddress, 'image');
                $userAddressLastId  = UserAddress::find()->orderBy(['id'=> SORT_DESC])->one();
                $countshift =  count($shiftcount); 
                foreach ($post['AddScheduleForm']['weekday'] as $key => $day_shift) 
                {
                    $dayShiftsFromDb=UserSchedule::find()->where(['user_id' =>$ids])->andwhere(['weekday' => $day_shift[0]])->all();
                    if(!empty($dayShiftsFromDb))
                    {
                        foreach($shiftcount as $keyClnt=>$shift_v)
                        {
                            foreach ($dayShiftsFromDb as $keydb => $dayshiftValuedb) 
                            {
                                $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);
                                $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);
                                $nstart_time = $dbstart_time.' '.$post['AddScheduleForm']['start_time'][$keyClnt];
                                $nend_time = $dbend_time.' '.$post['AddScheduleForm']['end_time'][$keyClnt];
                                $startTimeClnt = strtotime($nstart_time);
                                $endTimeClnt = strtotime($nend_time);
                                $startTimeDb =$dayshiftValuedb->start_time;
                                $endTimeDb = $dayshiftValuedb->end_time;

                                if($startTimeClnt >= $startTimeDb && $startTimeClnt <= $endTimeDb)
                                {
                                    $canAddEdit = false;
                                    $errorIndex++;
                                    $msg = ' already exists';
                                }
                                elseif($endTimeClnt >= $startTimeDb && $endTimeClnt <= $endTimeDb)
                                {
                                    $canAddEdit = false;
                                    $errorIndex++;
                                }
                                elseif($startTimeDb >= $startTimeClnt && $startTimeDb <= $endTimeClnt)
                                {
                                    $canAddEdit = false;
                                    $errorIndex++;
                                }
                                elseif($endTimeDb >= $startTimeClnt && $endTimeDb <= $endTimeClnt)
                                {
                                    $canAddEdit = false;
                                    $errorIndex++;
                                }
                                elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb)
                                {
                                    $canAddEdit = false;
                                    $errorIndex++;
                                }
                                if($canAddEdit==false) {
                                    Yii::$app->session->setFlash('shifterror', 'Shift '.date('h:i a',$startTimeClnt). ' - ' .date('h:i a',$endTimeClnt).' on '.$day_shift[0].$msg);

                                    return $this->render('shift/add-shift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel,'postData' => $post]);
                                } 
                            } 
                            $insertShift[$newInsertIndex] = $this->saveShiftData($ids,$keyClnt,$post,$day_shift,$countshift= NULL);
                            $newInsertIndex++;
                        }

                    } else {
                        foreach ($day_shift as $keydata => $value) {
                            $insertShift[$newInsertIndex] = $this->saveShiftData($ids,$keydata,$post,$day_shift,$countshift);
                            $newInsertIndex++;
                        }
                    }
                }
                if($canAddEdit == true)
                {
                    if($addAddress->save())
                    {
                        $imageUpload='';
                        if (isset($_FILES['image'])){
                            $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                        }
                        if (isset($_FILES['images'])){
                            $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES);
                        }
                        if(!empty($insertShift))
                        {
                            foreach ($insertShift as $key => $value) 
                            {
                                $saveScheduleData = new UserSchedule();
                                $saveScheduleData->load(['UserSchedule'=>$value['AddScheduleForm']]);
                                $saveScheduleData->address_id= $addAddress->id;
                                $saveScheduleData->start_time= strtotime($value['AddScheduleForm']['start_time']);
                                $saveScheduleData->end_time= strtotime($value['AddScheduleForm']['end_time']);
                                if($saveScheduleData->save()){
                                }
                                else 
                                {
                                    echo '<pre>';
                                    print_r($saveScheduleData->getErrors());
                                }
                            }
                        } 
                    }
                    Yii::$app->session->setFlash('success', 'Shift Added SuccessFully');
                    return $this->redirect(['/doctor/my-shifts?id='.$id]);
                }
            } 
        } 
        $scheduleslist= DrsPanel::weekSchedules($id);
        $hospitals= DrsPanel::doctorHospitalList($id);
        return $this->render('shift/_addShift',['defaultCurrrentDay'=>strtotime($date),'hospitals'=>$hospitals['apiList'],'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'listaddress'=>$hospitals['listaddress'],'scheduleslist'=>$scheduleslist,
            'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel]);
    }

    public function actionAddShift($id = NULL){
        $ids=$id;
        $date=date('Y-m-d');
        $current_shifts=0;
        $week_array=DrsPanel::getWeekShortArray();
        $availibility_days=array();
        $addAddress=new UserAddress();
        $imgModel = new  UserAddressImages();
        foreach($week_array as $week){
            $availibility_days[]=$week;
        }
        $newShift= new AddScheduleForm();
        $newShift->user_id=$id;
        if($id){
            $userShift= UserSchedule::findOne($id);
            if(!empty($userShift))
            {
               $newShift->setShiftDataAdmin($userShift);
            }
        }
        if(Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $newShift->setPostData($post);
            if(isset($post['UserAddress'])) {
                $shift=array();
                $shiftcount=$post['AddScheduleForm']['start_time'];
                $canAddEdit = true;$msg = ' invalid';
                $errorIndex = 0;$newInsertIndex = 0;
                $errorShift = array();$insertShift = array();
                $newshiftInsert =0;$insertShift = array();
                $addAddress->load(Yii::$app->request->post());
                $addAddress->user_id = $ids;
                $upload = UploadedFile::getInstance($addAddress, 'image');
                $userAddressLastId  = UserAddress::find()->orderBy(['id'=> SORT_DESC])->one();
                $countshift =  count($shiftcount);
                $newshiftcheck=array(); $errormsgloop=array();
                $nsc=0; $error_msg=0;
                foreach ($post['AddScheduleForm']['weekday'] as $keyClnt => $day_shift) {
                    foreach ($day_shift as $keydata => $value) {
                        $dayShiftsFromDb=UserSchedule::find()->where(['user_id' =>$ids])->andwhere(['weekday' => $value])->all();

                        if(!empty($dayShiftsFromDb)) {
                            foreach ($dayShiftsFromDb as $keydb => $dayshiftValuedb) {
                                $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);
                                $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);
                                $nstart_time = $dbstart_time.' '.$post['AddScheduleForm']['start_time'][$keyClnt];
                                $nend_time = $dbend_time.' '.$post['AddScheduleForm']['end_time'][$keyClnt];
                                $startTimeClnt = strtotime($nstart_time);
                                $endTimeClnt = strtotime($nend_time);
                                $startTimeDb =$dayshiftValuedb->start_time;
                                $endTimeDb = $dayshiftValuedb->end_time;

                                if($startTimeClnt > $endTimeClnt){
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= '(end time should be greater than start time)';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' (end time should be greater than start time)';
                                }

                                //check values with local runtime form value
                                foreach($newshiftcheck as $keyshift=>$newshift){
                                    $starttime_check=$newshift['start_time'];
                                    $endtime_check=$newshift['end_time'];
                                    $weekday_check=$newshift['weekday'];
                                    $keyClnt_check=$newshift['keyclnt'];
                                    if($weekday_check == $value && $keyClnt != $keyClnt_check){
                                        if($startTimeClnt == $starttime_check && $endTimeClnt == $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is already exists';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' already exists';
                                        }
                                        elseif($startTimeClnt >= $starttime_check && $startTimeClnt < $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg1';
                                        }
                                        elseif($endTimeClnt >= $starttime_check && $endTimeClnt <= $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg2';
                                        }
                                        elseif($starttime_check >= $startTimeClnt && $starttime_check <= $endTimeClnt) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg3';
                                        }
                                        elseif($endtime_check > $startTimeClnt && $endtime_check <= $endTimeClnt) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg4';
                                        }
                                        elseif($startTimeClnt >= $starttime_check && $startTimeClnt < $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg5';
                                        }

                                    }
                                }


                                //check values with database value
                                if ($startTimeClnt == $endTimeClnt) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= '(start time & end time should not be same)';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' (start time & end time should not be same)';
                                }
                                elseif ($startTimeClnt == $startTimeDb && $endTimeClnt == $endTimeDb) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is already exists';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' already exists';

                                } elseif ($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is invalid time';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' msg1';
                                } elseif ($endTimeClnt >= $startTimeDb && $endTimeClnt <= $endTimeDb) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is invalid time';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' msg2';
                                } elseif ($startTimeDb >= $startTimeClnt && $startTimeDb <= $endTimeClnt) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is invalid time';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' msg3';
                                } elseif ($endTimeDb > $startTimeClnt && $endTimeDb <= $endTimeClnt) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is invalid time';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' msg4';
                                } elseif ($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb) {
                                    $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                    $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                    $errormsgloop[$error_msg]['shift']= $keyClnt;
                                    $errormsgloop[$error_msg]['weekday']= $value;
                                    $errormsgloop[$error_msg]['message']= 'is invalid time';
                                    $canAddEdit = false;
                                    $errorIndex++;$error_msg++;
                                    $msg = ' msg5';
                                } else {
                                    if($canAddEdit==true) {
                                        $nsc_add = $nsc++;
                                        $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                        $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                        $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                        $newshiftcheck[$nsc_add]['weekday'] = $value;
                                    }
                                }
                            }
                            if($canAddEdit==true) {
                                $insertShift[$newInsertIndex] = $this->saveShiftData($ids,$keyClnt,$post,$value,$countshift= NULL);
                                $newInsertIndex++;
                            }

                        }
                        else{
                            $dbstart_time=date('Y-m-d');
                            $nstart_time = $dbstart_time.' '.$post['AddScheduleForm']['start_time'][$keyClnt];
                            $nend_time = $dbstart_time.' '.$post['AddScheduleForm']['end_time'][$keyClnt];
                            $startTimeClnt = strtotime($nstart_time);
                            $endTimeClnt = strtotime($nend_time);
                            if($startTimeClnt > $endTimeClnt){
                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                $errormsgloop[$error_msg]['weekday']= $value;
                                $errormsgloop[$error_msg]['message']= '(end time should be greater than start time)';
                                $canAddEdit = false;
                                $errorIndex++;$error_msg++;
                                $msg = ' (end time should be greater than start time)';
                            }

                            //check values with local runtime form value
                            foreach($newshiftcheck as $keyshift=>$newshift){
                                    $starttime_check=$newshift['start_time'];
                                    $endtime_check=$newshift['end_time'];
                                    $weekday_check=$newshift['weekday'];
                                    $keyClnt_check=$newshift['keyclnt'];
                                    if($weekday_check == $value && $keyClnt != $keyClnt_check){
                                        if($startTimeClnt == $starttime_check && $endTimeClnt == $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is already exists';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' already exists';
                                        }
                                        elseif($startTimeClnt > $starttime_check && $startTimeClnt < $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg1';
                                        }
                                        elseif($endTimeClnt > $starttime_check && $endTimeClnt < $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg2';
                                        }
                                        /*elseif($starttime_check >= $startTimeClnt && $starttime_check <= $endTimeClnt) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'msg3 is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg3';
                                        }
                                        elseif($endtime_check > $startTimeClnt && $endtime_check <= $endTimeClnt) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'msg 4is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg4';
                                        }
                                        elseif($startTimeClnt >= $starttime_check && $startTimeClnt < $endtime_check) {
                                            $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                            $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                            $errormsgloop[$error_msg]['shift']= $keyClnt;
                                            $errormsgloop[$error_msg]['weekday']= $value;
                                            $errormsgloop[$error_msg]['message']= 'is invalid time';
                                            $canAddEdit = false;
                                            $errorIndex++;$error_msg++;
                                            $msg = ' msg5';
                                        }*/
                                    }
                            }
                            if($canAddEdit==true) {
                                $nsc_add = $nsc++;
                                $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                $newshiftcheck[$nsc_add]['weekday'] = $value;
                                $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                $insertShift[$newInsertIndex] = $this->saveShiftData($ids,$keyClnt,$post,$value,$countshift);
                                $newInsertIndex++;
                            }
                        }
                    }
                }

                if($canAddEdit==false) {
                    if(!empty($errormsgloop)){

                        $html=array();
                        $weekdaysl=array();
                        foreach($errormsgloop as $msgloop){
                            $keyshifts=$msgloop['shift'];
                            $weekdaysl[$keyshifts][]=$msgloop['weekday'];
                            $html[$keyshifts]='Shift time '.date('h:i a',$msgloop['start_time']). ' - ' .date('h:i a',$msgloop['end_time']).' on '.implode(',',$weekdaysl[$keyshifts]).' '.$msgloop['message'];
                        }
                        Yii::$app->session->setFlash('shifterror', implode(" , ", $html));
                    }
                    else{
                        Yii::$app->session->setFlash('shifterror', 'Shift time invalid');
                    }
                    return $this->render('shift/add-shift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel,'postData' => $post]);
                }
                elseif($canAddEdit == true) {

                    if($addAddress->save()) {
                        $imageUpload='';
                        if (isset($_FILES['image'])){
                            $imageUpload=DrsImageUpload::updateAddressImageWeb($addAddress->id,$_FILES);
                        }
                        if (isset($_FILES)){
                            $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'UserAddressImages','web');
                        }
                        if(!empty($insertShift)) {
                            foreach ($insertShift as $key => $value) {
                                $saveScheduleData = new UserSchedule();
                                $saveScheduleData->load(['UserSchedule'=>$value['AddScheduleForm']]);
                                $saveScheduleData->address_id= $addAddress->id;
                                $saveScheduleData->start_time= strtotime($value['AddScheduleForm']['start_time']);
                                $saveScheduleData->end_time= strtotime($value['AddScheduleForm']['end_time']);
                                if($saveScheduleData->save()){
                                }
                                else 
                                {
                                    echo '<pre>';
                                    print_r($saveScheduleData->getErrors());
                                }
                            }
                        }
                        Yii::$app->session->setFlash('success', 'Shift Added SuccessFully');
                        return $this->redirect(['/doctor/my-shifts?id='.$id]);

                    }
                    else{
                            echo "<pre>"; print_r($addAddress->getErrors());die;
                    }

                }
                else{
                    Yii::$app->session->setFlash('error', 'Not added');
                }
            } 
        } 
        $scheduleslist= DrsPanel::weekSchedules($id);
        $hospitals= DrsPanel::doctorHospitalList($id);
        // return $this->render('shift/add-shift',['defaultCurrrentDay'=>strtotime($date),'hospitals'=>$hospitals['apiList'],'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'listaddress'=>$hospitals['listaddress'],'scheduleslist'=>$scheduleslist,
        //     'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel]);

        return $this->render('shift/_addShift',['defaultCurrrentDay'=>strtotime($date),'hospitals'=>$hospitals['apiList'],'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'listaddress'=>$hospitals['listaddress'],'scheduleslist'=>$scheduleslist,
            'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel]);
    }

    public function saveShiftData($ids,$keyClnt,$post,$day_shift,$shiftcount)
    {
        $shift['AddScheduleForm']['user_id']=$ids;
        $shift['AddScheduleForm']['start_time']=$post['AddScheduleForm']['start_time'][$keyClnt];
        $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$keyClnt];
        $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$keyClnt];
        $shift['AddScheduleForm']['weekday']=$day_shift;
        $time1 = strtotime($shift['AddScheduleForm']['start_time']);
        $time2 = strtotime($shift['AddScheduleForm']['end_time']);
        $difference = abs($time2 - $time1) / 60;
        $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];
        $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
        $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$keyClnt]) && ($post['AddScheduleForm']['consultation_fees'][$keyClnt] > 0) )?$post['AddScheduleForm']['consultation_fees'][$keyClnt]:0;
        $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$keyClnt]) && ($post['AddScheduleForm']['emergency_fees'][$keyClnt] > 0))?$post['AddScheduleForm']['emergency_fees'][$keyClnt]:0;
        $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]:0;
        $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]:0;
        return $shift;
    }

    public function actionAddMoreShift(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $shift_count=$post['shiftcount'];
            $week_array=DrsPanel::getWeekShortArray();
            $newShift= new AddScheduleForm();
            $form = new ActiveForm();
            $result = $this->renderAjax('shift/add-more-shift',['model' => $newShift,'form' => $form,'shift_count'=>$shift_count,'weeks'=>$week_array]);
            return $result;
        }
    }
    public function actionCityList(){
        $result='<option value="">Select City</option>';
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $rst=Drspanel::getCitiesList($post['state_id'],'name');
            foreach ($rst as $key => $item) {
                $result=$result.'<option value="'.$item->name.'">'.$item->name.'</option>';
            }
        }
        return $result;
    }




    public function actionUpdateShiftTime($id,$day){
        $user_id=$id;
        $week_array=DrsPanel::getWeekArray();
        $searchModel = new UserScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$day);
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $post['AddScheduleForm']['weekday']=[$day];
            $post['AddScheduleForm']['user_id']=$id;
            $shift_id=isset($post['AddScheduleForm']['id'])?$post['AddScheduleForm']['id']:'';
            if($shift_id > 0){
                $getSchedule=UserSchedule::find()->where(['id'=>$shift_id,'is_edit'=>1])->one();
                if(empty($getSchedule)){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'You can not edit this shift')
                        ]);
                    return $this->redirect(['update-shift-time', 'id' => $id,'day'=>$day]);
                }
            }

            $post['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount']))?$post['AddScheduleForm']['consultation_fees_discount']:0;
            $post['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount']))?$post['AddScheduleForm']['emergency_fees_discount']:0;

            $addUpdateShift=DrsPanel::upsertShift($post,$shift_id);
            Yii::$app->session->setFlash('alert', [
                'options'=>['class'=>'alert-success'],
                'body'=>Yii::t('backend', 'Shift Time updated successfully')
                ]);
            return $this->redirect(['update-shift-time', 'id' => $id,'day'=>$day]);
        }

        return $this->render('shift/update-shift-time', ['model'=>$this->findModel($id),'searchModel'=>$searchModel,'dataProvider'=>$dataProvider,

            ]);
    }


    public function actionSingleShiftTime(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post= Yii::$app->request->post();
            if($shift=UserSchedule::findOne($post['id'])){
                $serach_attender=['parent_id'=>$post['user_id'],'address_id'=>$shift->address_id];
                $addressList=DrsPanel::doctorHospitalList($post['user_id']);
                $attenderList=DrsPanel::attenderList($serach_attender,'list');
                $singleShift=UserSchedule::setSingleShiftData($post);
                $week_array=DrsPanel::getWeekArray();
                $newShift= new AddScheduleForm();
                $newShift->setShiftData($singleShift);
                return $this->renderAjax('shift/_editShift', ['model' => $this->findModel($post['user_id']),'userShift'=>$newShift,'listaddress'=>$addressList['listaddress'],'attenderList'=>$attenderList,'week'=>$singleShift->weekday,'week_array'=>$week_array
                    ]);
            }
        }
        return 'error';
    }

        public function actionEditShift($id,$user_id){
        $loginID=$user_id;
        $address_id = $id;
        $address=UserAddress::findOne($address_id);
        $addressImages = UserAddressImages::find()->where(['address_id'=>$address_id])->all();
        $imgModel= new UserAddressImages();

        if($address->user_id == $loginID){
            $disable_field=0;
        }
        else{
            $disable_field=1;
        }

        $date=date('Y-m-d');
        $week_array=DrsPanel::getWeekShortArray();
        $availibility_days=array();

        foreach($week_array as $week){
            $availibility_days[]=$week;
        }
        $newShift= new AddScheduleForm();
        $newShift->user_id=$loginID;
        $shifts=DrsPanel::getShiftListByAddress($loginID,$address_id);

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            //$this->pr($post);die;
            if(isset($post['UserAddress'])){
                $user=User::findOne(['id'=>$loginID]);
                if(!empty($user)){
                    $addAddress=UserAddress::findOne($address_id);
                    if(empty($addAddress)){
                        Yii::$app->session->setFlash('success', 'Address Not Valid');
                        return $this->redirect(['/doctor/my-shifts']);
                    }
                    $data['UserAddress']['user_id']=$loginID;
                    $data['UserAddress']['name']=$post['UserAddress']['name'];
                    $data['UserAddress']['city']=$post['UserAddress']['city'];
                    $data['UserAddress']['state']=$post['UserAddress']['state'];
                    $data['UserAddress']['address']=$post['UserAddress']['address'];
                    $data['UserAddress']['area']=$post['UserAddress']['area'];
                    $data['UserAddress']['phone']=$post['UserAddress']['phone'];
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);
                    if((isset($post['AddScheduleForm']['weekday']) && !empty($post['AddScheduleForm']['weekday'])))
                    {

                        $dayShifts=$post['AddScheduleForm']['weekday'];
                        $canAddEdit = true;
                        $msg = ' invalid';
                        $errorIndex = 0;
                        $newInsertIndex = 0;
                        $errorShift = array();
                        $insertShift = array();
                        $shiftcount=$post['AddScheduleForm']['start_time'];
                        foreach($dayShifts as $key=> $day_shift)
                        {
                            if(!empty($day_shift))
                            {
                            $dayShiftsFromDb=UserSchedule::find()->where(['user_id' =>$loginID])->andwhere(['weekday' => $day_shift[0]])->all();
                            
                            if(!empty($dayShiftsFromDb))
                            {
                                foreach($shiftcount as $keyClnt=>$shift_v)
                                {
                                    foreach ($dayShiftsFromDb as $keydb => $dayshiftValuedb)
                                    {
                                        // print_r($post['AddScheduleForm']['id']);die;
                                        $newshiftInsertId = isset($id)?$id:'';
                                        if(isset($newshiftInsertId))
                                        {
                                            if($newshiftInsertId == $dayshiftValuedb->address_id)
                                            {
                                                continue;
                                            }
                                        }
                                        $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);
                                        $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);
                                        $nstart_time = $dbstart_time.' '.$post['AddScheduleForm']['start_time'][$keyClnt];
                                        $nend_time = $dbend_time.' '.$post['AddScheduleForm']['end_time'][$keyClnt];
                                        $startTimeClnt = strtotime($nstart_time);
                                        $endTimeClnt = strtotime($nend_time);
                                        $startTimeDb =$dayshiftValuedb->start_time;
                                        $endTimeDb = $dayshiftValuedb->end_time;
                                   
                                        if($startTimeClnt >= $startTimeDb && $startTimeClnt <= $endTimeDb)
                                        {
                                            $canAddEdit = false;
                                            $errorIndex++;
                                            $msg = ' already exists';
                                        }

                                        elseif($endTimeClnt >= $startTimeDb && $endTimeClnt <= $endTimeDb)
                                        {
                                            $canAddEdit = false;
                                            $errorIndex++;

                                        }
                                        elseif($startTimeDb >= $startTimeClnt && $startTimeDb <= $endTimeClnt)
                                        {
                                            $canAddEdit = false;
                                            $errorIndex++;

                                        }
                                        elseif($endTimeDb >= $startTimeClnt && $endTimeDb <= $endTimeClnt)
                                        {
                                            $canAddEdit = false;
                                            $errorIndex++;

                                        }
                                        elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb)
                                        {
                                            $canAddEdit = false;
                                            $errorIndex++;
                                        }
                                        
                                        if($canAddEdit==false) {
                                            Yii::$app->session->setFlash('shifterror', 'Shift '.date('h:i a',$startTimeClnt). ' - ' .date('h:i a',$endTimeClnt).' on '.$day_shift[0].$msg);
                                            return $this->render('shift/edit-shift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel,'postData' => $post,'disable_field' => $disable_field,'shifts'=>$shifts]);
                                        }  
                                    } 
                                    $shift['AddScheduleForm']['user_id']=$loginID;
                                    $shift['AddScheduleForm']['id']=$post['AddScheduleForm']['id'];
                                    $shift['AddScheduleForm']['start_time']=$shift_v;
                                    $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$keyClnt];
                                    $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$keyClnt];
                                    $shift['AddScheduleForm']['weekday']=$day_shift;
                                    $time1 = strtotime($shift['AddScheduleForm']['start_time']);
                                    $time2 = strtotime($shift['AddScheduleForm']['end_time']);
                                    $difference = abs($time2 - $time1) / 60;
                                    $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];
                                    $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
                                    $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$keyClnt]) && ($post['AddScheduleForm']['consultation_fees'][$keyClnt] > 0) )?$post['AddScheduleForm']['consultation_fees'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$keyClnt]) && ($post['AddScheduleForm']['emergency_fees'][$keyClnt] > 0))?$post['AddScheduleForm']['emergency_fees'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]:0;

                                } 
                            }
                            else {
                                foreach($shiftcount as $keyClnt=>$shift_v)
                                {
                                    $shift['AddScheduleForm']['user_id']=$loginID;
                                    $shift['AddScheduleForm']['id']=$post['AddScheduleForm']['id'];
                                    $shift['AddScheduleForm']['start_time']=$shift_v;
                                    $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$keyClnt];
                                    $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$keyClnt];
                                    $shift['AddScheduleForm']['weekday']=$day_shift;
                                    $time1 = strtotime($shift['AddScheduleForm']['start_time']);
                                    $time2 = strtotime($shift['AddScheduleForm']['end_time']);
                                    $difference = abs($time2 - $time1) / 60;
                                    $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];
                                    $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
                                    $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$keyClnt]) && ($post['AddScheduleForm']['consultation_fees'][$keyClnt] > 0) )?$post['AddScheduleForm']['consultation_fees'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$keyClnt]) && ($post['AddScheduleForm']['emergency_fees'][$keyClnt] > 0))?$post['AddScheduleForm']['emergency_fees'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]:0;
                                    $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]:0;
                                }
                            }
                            }
                            else 
                            {
                                Yii::$app->session->setFlash('error', 'Please Select days');
                                return $this->redirect(['/doctor/my-shifts','id' => $loginID]);   
                            }
                        } 
                        if($canAddEdit == true)
                        {
                            if($addAddress->save())
                            {
                                $imageUpload='';
                                if (isset($_FILES['image'])){
                                    $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                                }
                                if (isset($_FILES['images'])){
                                    $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES);
                                }
                                $shiftDay = $shift['AddScheduleForm']['weekday'];
                                $output = array_diff($shiftDay,array_keys($post['shift_ids']));
                                
                                foreach ($shiftDay as $key => $weekdayvalue)
                                { 
                                   
                                    foreach ($shift as $key => $value) 
                                    {

                                        $saveScheduleData1 = UserSchedule::find()->where(['id' =>$value['id']])->all();


                                        if (in_array($weekdayvalue, $saveScheduleData1))
                                        {
                                            $appointment_index = 0;
                                            $appointmentCount= 0;
                                            $notEditedShift = array();
                                            if(isset($value['id']))
                                            {
                                                $saveScheduleData = UserSchedule::findOne($value['id']);

                                                if(!empty($saveScheduleData))
                                                {
                                                    $appointmentCount=UserAppointment::find()->where(['doctor_id'=>$value['user_id'],'schedule_id'=>$value['id'],'status'=>'available'])->count();
                                                    if($appointmentCount > 0 )
                                                    {
                                                        $value['appointment_count'] =  $appointmentCount;
                                                        $notEditedShift[$appointment_index] = $value;
                                                        $appointment_index++;
                                                    }
                                                    $value['weekday'] = $weekdayvalue;
                                                    $saveScheduleData->load(['UserSchedule'=>$value]);
                                                    $saveScheduleData->address_id= $addAddress->id;
                                                    $saveScheduleData->start_time= strtotime($value['start_time']);
                                                    $saveScheduleData->end_time= strtotime($value['end_time']);
                                                    $saveScheduleData->weekday= $weekdayvalue;
                                                    if($saveScheduleData->save()){
                                                    }
                                                    else 
                                                    {
                                                        $this->pr($saveScheduleData->getErrors());die;
                                                    }
                                                    Yii::$app->session->setFlash('success', 'Shift edited successfully');
                                                    return $this->redirect(['/doctor/my-shifts','id' => $loginID]);
                                                }
                                            }
                                        }
                                        else
                                        {
                                            $saveScheduleData = new UserSchedule();
                                            $value['weekday'] = $weekdayvalue;
                                            $saveScheduleData->load(['UserSchedule'=>$value]);
                                            $saveScheduleData->address_id= $addAddress->id;
                                            $saveScheduleData->start_time= strtotime($value['start_time']);
                                            $saveScheduleData->end_time= strtotime($value['end_time']);
                                            $saveScheduleData->weekday= $weekdayvalue;
                                            if($saveScheduleData->save()){
                                            }
                                            else 
                                            {
                                                $this->pr($saveScheduleData->getErrors());die;
                                            }
                                            Yii::$app->session->setFlash('success', 'Shift edited successfully');
                                            return $this->redirect(['/doctor/my-shifts' , 'id' => $loginID]);
                                        }

                                    }
                                }die; 
                            }
                        }
                    }else
                    {
                         Yii::$app->session->setFlash('error', 'Please Select days');
                        return $this->redirect(['/doctor/my-shifts', '$loginID']);
                    }
                }
            }
        }
        return $this->render('shift/_editShift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'shifts'=>$shifts, 'modelAddress' => $address, 'userAdddressImages' => $imgModel,'addressImages'=>$addressImages,'disable_field'=>$disable_field]);
}

    public function actionAjaxNewShift(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $id= $post['id'];
            $model =$this->findModel($id);
            $userProfile=UserProfile::findOne(['user_id'=>$id]);
            $query = UserAddress::find()->where(['user_id'=>$id]);
            $addressProvider = new ActiveDataProvider([
                'query' => $query,
                ]);
            $addressList=DrsPanel::doctorHospitalList($id);

            $userShift= UserSchedule::find()->where(['user_id'=>$id])->all();
            $week_array=DrsPanel::getWeekArray();
            $availibility_days=array();
            foreach($week_array as $week){
                $availibility_days[]=$week;
            }
            $newShift= new AddScheduleForm();
            $newShift->user_id=$userProfile->user_id;
            $newShift->weekday=$availibility_days;
            $form = ActiveForm::begin();
            echo $this->renderAjax('_newShift',[
                'model' => $newShift,'listaddress'=>$addressList['listaddress'],'form'=>$form,
                ]); exit;

        }
        echo 'error';
        exit;
    }

    /* Today Timing*/
    public function actionDayShifts($id){
        $user_id=$id;
        $doctor=User::findOne($user_id);
        $date=date('Y-m-d');
        $getShists=DrsPanel::getBookingShifts($user_id,$date,$user_id);
        return $this->render('day-shifts',
            ['defaultCurrrentDay'=>strtotime($date),'shifts'=>$getShists,'doctor'=>$doctor,'userid' => $id]);
    }

    /* Today Timing Shift Status Acitve */
     public function actionUpdateShiftStatus(){
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'You have do not permission.';
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $params['booking_closed']=$post['status'];
            $params['doctor_id']=$post['userid'];
            $params['date']=date('Y-m-d',$post['date']);
            $params['schedule_id']=$post['id'];
            $response= DrsPanel::updateShiftStatus($params);
            if(empty($response)){
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'You have do not permission.';
            }
        }
        return json_encode($response);
    }

    public function actionAjaxAddressList(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $loginID = $post['user_id'];
            $days_plus=$post['plus'];
            $operator=$post['operator'];
            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));
            $appointments= DrsPanel::getBookingShifts($loginID,$date,$loginID);
            echo $this->renderAjax('_address-with-shift',
                ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'userid' => $loginID]); exit();
        }
    }

    public function actionAjaxDailyShift(){
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $user_id = $post['id'];
            $date = $post['date'];
            $week=DrsPanel::getDateWeekDay($date);
            $shift_details=$this->setDateShiftData($user_id,$date);
            echo $this->renderAjax('_dailylimit',[
                'model' => $this->findModel($user_id),'userShift'=>$shift_details['userShift'],'listaddress'=>$shift_details['listaddress'],'date'=>$date,'shifts_available'=>$shift_details['shifts_available']
                ]); exit;

        }
        echo 'error';
        exit;
    }





    public function actionAddAppointments($id){
        $date=date('Y-m-d');
        $user_id=$id;
        $week=DrsPanel::getDateWeekDay($date);
        $keys_avail=array();
        $addAppointment= new AddAppointmentForm();


        $totalshifts=DrsPanel::getDateShifts($user_id,$date);
        $shifts=array();
        if(!empty($totalshifts) && isset($totalshifts['shifts'])){
            $shifts=$totalshifts['shifts'];
        }
        $shifts_available=0;
        if(isset($shifts[UserSchedule::SHIFT_MORNING]) && !empty($shifts[UserSchedule::SHIFT_MORNING])){
            $shifts_available=1;
            $morningSchedule=$shifts[UserSchedule::SHIFT_MORNING];
            $keys_avail[UserSchedule::SHIFT_MORNING]=ucfirst(UserSchedule::SHIFT_MORNING).' ('.$morningSchedule['time'].')';
        }
        if(isset($shifts[UserSchedule::SHIFT_AFTERNOON]) && !empty($shifts[UserSchedule::SHIFT_AFTERNOON])){
            $shifts_available=1;
            $afternoonSchedule=$shifts[UserSchedule::SHIFT_AFTERNOON];
            $keys_avail[UserSchedule::SHIFT_AFTERNOON]=ucfirst(UserSchedule::SHIFT_AFTERNOON).' ('.$afternoonSchedule['time'].')';
        }
        if(isset($shifts[UserSchedule::SHIFT_EVENING]) && !empty($shifts[UserSchedule::SHIFT_EVENING])){
            $shifts_available=1;
            $eveningSchedule=$shifts[UserSchedule::SHIFT_EVENING];
            $keys_avail[UserSchedule::SHIFT_EVENING]=ucfirst(UserSchedule::SHIFT_EVENING).' ('.$eveningSchedule['time'].')';
        }

        if(Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $doctorProfile=UserProfile::findOne(['user_id'=>$user_id]);
            $params=$post['AddAppointmentForm'];
            if(!empty($doctorProfile)){
                $type=UserAppointment::TYPE_OFFLINE;
                $book_for=UserAppointment::BOOK_FOR_SELF;
                $appointment_date=$params['date'];
                $appointment_shift=$params['slot'];
                $appointment_type=UserAppointment::APPOINTMENT_TYPE_CONSULTATION;

                $dateDay=DrsPanel::getDateShifts($user_id,$appointment_date);
                if(!empty($dateDay)){
                    if(isset($dateDay['shifts']) && isset($dateDay['shifts'][$appointment_shift]) && !empty($dateDay['shifts'][$appointment_shift])){
                        $appointment_time=$dateDay['shifts'][$appointment_shift]['time'];
                        $doctor_fees=$dateDay['shifts'][$appointment_shift]['consultation_fees'];
                        $doctor_address=$dateDay['shifts'][$appointment_shift]['address'];
                        $user_name=$params['name'];
                        $user_age=$params['age'];
                        $user_phone=$params['phone'];
                        $user_gender=$params['gender'];
                        if(isset($params['address'])){
                            $user_address=$params['address'];
                        }
                        else{
                            $user_address='';
                        }
                        $payment_type=$params['payment_type'];

                        $data['UserAppointment']['type']=$type;
                        $data['UserAppointment']['appointment_type']=$appointment_type;
                        $data['UserAppointment']['user_id']=0;
                        $data['UserAppointment']['doctor_id']=$user_id;
                        $data['UserAppointment']['appointment_date']=$appointment_date;
                        $data['UserAppointment']['appointment_shift']=$appointment_shift;
                        $data['UserAppointment']['appointment_time']=$appointment_time;
                        $data['UserAppointment']['book_for']=$book_for;
                        $data['UserAppointment']['user_name']=$user_name;
                        $data['UserAppointment']['user_age']=$user_age;
                        $data['UserAppointment']['user_phone']=$user_phone;
                        $data['UserAppointment']['user_gender']=$user_gender;
                        $data['UserAppointment']['user_address']=$user_address;
                        $data['UserAppointment']['payment_type']=$payment_type;
                        $data['UserAppointment']['doctor_name']=$doctorProfile->name;
                        $data['UserAppointment']['doctor_fees']=$doctor_fees;
                        $data['UserAppointment']['doctor_address']=$doctor_address;
                        $data['UserAppointment']['status']='pending';
                        $addAppointment=DrsPanel::addAppointment($data,'doctor');
                        if($addAppointment['type'] == 'model_error'){
                            Yii::$app->session->setFlash('alert', [
                                'options'=>['class'=>'alert-danger'],
                                'body'=>Yii::t('backend', 'Please try again!')
                                ]);
                        }
                        else{
                            Yii::$app->session->setFlash('alert', [
                                'options'=>['class'=>'alert-success'],
                                'body'=>Yii::t('backend', 'Appointment added successfully')
                                ]);
                            return $this->redirect(['detail', 'id' => $user_id]);
                        }
                    }
                    else{
                        Yii::$app->session->setFlash('alert', [
                            'options'=>['class'=>'alert-danger'],
                            'body'=>Yii::t('backend', 'Can not add booking for this shift')
                            ]);
                    }
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Can not add booking for this shift & date')
                        ]);
                }
            }
            else{
                Yii::$app->session->setFlash('alert', [
                    'options'=>['class'=>'alert-danger'],
                    'body'=>Yii::t('backend', 'Doctor detail not found')
                    ]);
            }
        }

        return $this->render('add_appointments', [
            'model' => $this->findModel($id),'userShift'=>$shifts,'date'=>$date,'shifts_available'=>$shifts_available,'addAppointment'=>$addAppointment,'keys_avail'=>$keys_avail
            ]);
    }

    public function actionAjaxAppointments(){
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $user_id = $post['id'];
            $date = $post['date'];
            $week=DrsPanel::getDateWeekDay($date);
            $keys_avail=array();

            $addAppointment= new AddAppointmentForm();


            $totalshifts=DrsPanel::getDateShifts($user_id,$date);
            $shifts=array();
            if(!empty($totalshifts) && isset($totalshifts['shifts'])){
                $shifts=$totalshifts['shifts'];
            }
            $shifts_available=0;
            if(isset($shifts[UserSchedule::SHIFT_MORNING]) && !empty($shifts[UserSchedule::SHIFT_MORNING])){
                $shifts_available=1;
                $morningSchedule=$shifts[UserSchedule::SHIFT_MORNING];
                $keys_avail[UserSchedule::SHIFT_MORNING]=ucfirst(UserSchedule::SHIFT_MORNING).' ('.$morningSchedule['time'].')';
            }
            if(isset($shifts[UserSchedule::SHIFT_AFTERNOON]) && !empty($shifts[UserSchedule::SHIFT_AFTERNOON])){
                $shifts_available=1;
                $afternoonSchedule=$shifts[UserSchedule::SHIFT_AFTERNOON];
                $keys_avail[UserSchedule::SHIFT_AFTERNOON]=ucfirst(UserSchedule::SHIFT_AFTERNOON).' ('.$afternoonSchedule['time'].')';
            }
            if(isset($shifts[UserSchedule::SHIFT_EVENING]) && !empty($shifts[UserSchedule::SHIFT_EVENING])){
                $shifts_available=1;
                $eveningSchedule=$shifts[UserSchedule::SHIFT_EVENING];
                $keys_avail[UserSchedule::SHIFT_EVENING]=ucfirst(UserSchedule::SHIFT_EVENING).' ('.$eveningSchedule['time'].')';
            }

            echo $this->renderAjax('_todayAppointment',[
                'model' => $this->findModel($user_id),'userShift'=>$shifts,'date'=>$date,'shifts_available'=>$shifts_available,'addAppointment'=>$addAppointment,'keys_avail'=>$keys_avail
                ]); exit;

        }
        echo 'error';
        exit;
    }

    public function actionAppointments($id){
        $searchModel = new UserAppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$id);
        return $this->render('appointments', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $this->findModel($id)
            ]);
    }

    public function actionView($id) {
        $model = UserAppointment::findOne($id);
        return $this->render('view', [
            'model' => $model
            ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id){
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionEducationList($user_id){

        /*$searchModel = new UserEducationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$user_id);

        return $this->renderAjax('education-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]); */
            $edu_list=UserEducations::find()->where(['user_id'=>$user_id])->all();
            return $this->renderAjax('education-list',['edu_list'=>$edu_list]);
        }

        public function actionEducationForm($user_id,$edu_id=NULL){

         $model= UserEducations::findOne($edu_id);
         $msg='Added';
         if(empty($model))
             $model = new UserEducations();
         if(Yii::$app->request->isPost){
             $post=Yii::$app->request->post();
             $post['UserEducations']['user_id']=$user_id;
             if($edu_id){
              $post['UserEducations']['id']=$edu_id;
              $msg='Updated';
          }
          $modelUpdate= UserEducations::upsert($post);
          if(count($modelUpdate)>0){
              Yii::$app->session->setFlash('alert', [
                'options'=>['class'=>'alert-success'],
                'body'=>Yii::t('backend', 'Doctor Education '.$msg.'.')
                ]);
              return $this->redirect(['detail', 'id' => $user_id]);
          }else{
              return false;
          }
      }
      return $this->renderAjax('education-form', ['model' => $model,]);
  }

  public function actionEducationUpsert(){

     if(Yii::$app->request->isAjax && Yii::$app->request->isPost){
      $post=Yii::$app->request->post();
      $model= UserEducations::upsert($post);
      return (count($model)>0)?true:false;
  }
  return false;
}

public function actionExperienceList($user_id){

        /*$searchModel = new UserEducationsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$user_id);

        return $this->renderAjax('education-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]); */
            $edu_list=UserExperience::find()->where(['user_id'=>$user_id])->all();
            return $this->renderAjax('experience-list',['edu_list'=>$edu_list]);
        }

        public function actionExperienceForm($user_id,$exp_id=NULL){

         $model= UserExperience::findOne($exp_id);
         $msg='Added';
         if(empty($model))
             $model = new UserExperience();
         if(Yii::$app->request->isPost){
             $post=Yii::$app->request->post();
             $post['UserExperience']['user_id']=$user_id;
             if($exp_id){
              $post['UserExperience']['id']=$exp_id;
              $msg='Updated';
          }
          $modelUpdate= UserExperience::upsert($post);
          if(count($modelUpdate)>0){
              Yii::$app->session->setFlash('alert', [
                'options'=>['class'=>'alert-success'],
                'body'=>Yii::t('backend', 'Doctor Experience '.$msg.'.')
                ]);
              return $this->redirect(['detail', 'id' => $user_id]);
          }else{
              return false;
          }
      }
      return $this->renderAjax('experience-form', ['model' => $model,]);
  }

  public function actionRequestedHospital($id){
    $confirmDrSearch=['request_to'=>$id,'groupid'=>Groups::GROUP_HOSPITAL,'status'=>UserRequest::Request_Confirmed];
    $confirmDr=UserRequest::requestedUser($confirmDrSearch,'request_to');
    $model =$this->findModel($id);
    $searchModel = new DoctorSearch();
    $dataProvider = $searchModel->linkedDoctors(Yii::$app->request->queryParams,$confirmDr,Groups::GROUP_HOSPITAL);
    if(Yii::$app->request->post()){
        $postData=Yii::$app->request->post();
        if(!empty($postData['RequestForm']['id'])){
            foreach ($postData['RequestForm']['id'] as $key => $value) {

             $model=$this->findModel($value);
             if(count($model)>0){
                $post['request_from']=$value;
                $post['request_to']=$id;
                $post['status']=UserRequest::Request_Confirmed;

                $result=UserRequest::updateStatus($post,'edit');
                if($result){
                    $confirm_hospital=UserAddress::find()->where(['is_register'=>1])->andWhere(['user_id'=>$post['request_from']])->one();
                    if($confirm_hospital){
                        $confirm_hospital->is_register=2;
                        $confirm_hospital->save();
                    }

                }

            }

        }
        if($result){
            return $this->redirect(['doctor/requested-hospital','id' => $id]);
        }
    }

}
$hospitalsList = DrsPanel::requestedHospitalsList($id);
return $this->render('requested-hospital', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'model'=>$model,
    'doctor_id' => $id,
    'hospitalsList'=>$hospitalsList,
    ]);

}

public function actionRequestSend(){

    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $model=$this->findModel($post['request_from']);
        if(count($model)>0){
            $post['groupid']=$model->groupid;
            $result=UserRequest::updateStatus($post,'edit');
            if($result){
                $confirm_hospital=UserAddress::find()->where(['is_register'=>1])->andWhere(['user_id'=>$post['request_from']])->one();
                if($confirm_hospital){
                    $confirm_hospital->is_register=2;
                    $confirm_hospital->save();
                }
                return true;
            }
        }
    }
    return false;
}


    public function actionGetEditLivemodal(){
        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();

            if(isset($post['LiveStatus'])){
                $user_id=$post['userid'];
                $userProfile=UserProfile::find()->where(['user_id'=>$user_id])->one();
                $model=User::findOne($user_id);
                $model->admin_status=$post['LiveStatus']['status'];
                if($model->save()){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile status updated!')
                    ]);
                    return $this->redirect(['index']);
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Status not updated!')
                    ]);
                    return $this->redirect(['index']);
                }
            }
            else{
                $user_id=$post['id'];
                $userProfile=UserProfile::find()->where(['user_id'=>$user_id])->one();
                $user=User::findOne($user_id);
                echo $this->renderAjax('_edit_live_status', [
                    'userProfile' => $userProfile,'user'=>$user
                ]); exit();
            }
        }

    }

     public function actionPrefixTitle(){
        $result='<option value="">Select Title</option>';
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $group=Groups::allgroups();
            $rst=DrsPanel::prefixingList(strtolower($group[$post['type']]),'list');
            if(count($rst)>0){
                foreach ($rst as $key => $item) {
                    $result=$result.'<option value="'.$item.'">'.$item.'</option>';
                }
            }
        }
        return $result;
    }




}
