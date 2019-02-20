<?php

namespace frontend\modules\user\controllers;

use backend\models\AddScheduleForm;
use common\components\DrsImageUpload;
use common\models\MetaKeys;
use common\models\UserEducations;
use common\models\UserExperience;
use common\models\UserSchedule;
use common\models\UserScheduleDay;
use common\models\UserScheduleGroup;
use common\models\UserScheduleSlots;
use Yii;
use yii\authclient\AuthAction;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\UserProfile;
use common\models\UserRequest;
use common\models\Groups;
use backend\models\AttenderForm;
use backend\models\AttenderEditForm;
use common\components\DrsPanel;
use common\models\UserAppointment;
use common\models\UserAddress;
use common\models\UserAddressImages;
use common\models\MetaValues;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use frontend\models\AppointmentForm;
use yii\db\Query;


/**
 * Class DoctorController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class DoctorController extends \yii\web\Controller
{
    private $loginUser;
    /**
     * @return array
     */
    public function actions()
    {
        return [
        'oauth' => [
        'class' => AuthAction::class,
        'successCallback' => [$this, 'successOAuthCallback']
        ]
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
        'access' => [
        'class' => AccessControl::className(),
        'rules' => [
        [
        'allow' => true,
        'roles' => ['@'],
        'matchCallback' => function () {
            $this->loginUser=Yii::$app->user->identity;
            return $this->loginUser->groupid==Groups::GROUP_DOCTOR;
        }
        ],
        ]
        ]
        ];
    }

    public function actionEditProfile() {
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $genderlist=DrsPanel::getGenderList();
        $degrees = MetaValues::find()->orderBy('id asc')->where(['key'=>2])->all();
        $specialities = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>5])->all();
        $treatment=array();
        if($userProfile['speciality']){
            $treatment=MetaValues::getValues(9,$userProfile['speciality']);
        }

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $userModel->load($post);
            $old_image = $userProfile->avatar;
            $userProfile->load($post);
            $userProfile->avatar= $old_image;

            if(isset($post['UserProfile']['degree'])){
                $udegrees=$post['UserProfile']['degree'];
                $userProfile->speciality=$post['UserProfile']['speciality'];

                $treatment=$post['UserProfile']['treatment'];
                if(!empty($udegrees)){
                    $other_degree=false;
                    if (in_array("Other", $udegrees)){
                        $other_degree=true;
                    }
                    $userProfile->degree=implode(',',$udegrees);
                    if(isset($post['other_degree']) && !empty($post['other_degree']) && $other_degree){
                        $userProfile->other_degree=$post['other_degree'];
                    }else{
                        $userProfile->other_degree=NULL;
                    }
                }
                if(!empty($treatment)){
                    $userProfile->treatment=implode(',',$treatment);
                }
            }

            if($userModel->groupUniqueNumber(['phone'=>$post['User']['phone'],'groupid'=>$userModel->groupid,'id'=>$userModel->id])){
                $userModel->addError('phone', 'This phone number already exists.');
            }
            $upload = UploadedFile::getInstance($userProfile, 'avatar');
            if($userModel->save() && $userProfile->save()){
                if(isset($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                    $imageUpload=DrsImageUpload::updateProfileImageWeb('doctors',$id,$upload);
                }
                Yii::$app->session->setFlash('success', "Profile updated!");
                return $this->redirect(['/doctor/profile']);
            }
        }

        if(isset($userProfile->speciality) && !empty($userProfile->speciality)) {
            $key=MetaValues::findOne(['value'=>$userProfile->speciality]);
            $treatment=MetaValues::find()->andWhere(['status'=>1,'key'=>9])->andWhere(['parent_key'=> isset($key->id)?$key->id:'0'])->all();
        }
        return $this->render('/doctor/edit-profile',['model' => $userModel,'userModel'=>$userModel,'userProfile'=>$userProfile,'genderList'=>$genderlist,'degrees'=>$degrees,'treatment'=>$treatment,'specialities'=>$specialities,]);
    }

    public function actionProfile(){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $groupid = Groups::GROUP_DOCTOR;
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $genderlist=[UserProfile::GENDER_MALE=>'Male',UserProfile::GENDER_FEMALE=>'Female'];
        if (Yii::$app->request->isAjax) {
            $userProfile->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($userProfile);
        }
        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            if(isset($post['UserProfile']['treatment']) &&  !empty($post['UserProfile']['treatment'])) {
                $Usertreatments=$post['UserProfile']['treatment'];
                if(!empty($Usertreatments)){
                    $Userspecialities=$post['UserProfile']['speciality'];
                    $metakey_speciality=MetaKeys::findOne(['key'=>'speciality']);
                    $getSpecilaity= MetaValues::find()->where(['key'=>$metakey_speciality->id,'value'=>$Userspecialities])->one();
                    $post['UserProfile']['treatment']=implode(',',$Usertreatments);

                    foreach ($Usertreatments as $keyt => $valuet) {
                        $treatmentModel = MetaValues::find()->where(['key'=>9,'parent_key'=>$getSpecilaity->id,'value' => $valuet])->one();
                        if(empty($treatmentModel)) {
                            $treatmentModel = new MetaValues();
                            if(!empty($getSpecilaity)){
                                $treatmentModel->parent_key=$getSpecilaity->id;
                            }
                            $treatmentModel->key =9;
                            $treatmentModel->value = $valuet;
                            $treatmentModel->label = $valuet;
                            $treatmentModel->status = 0;
                            $treatmentModel->save();
                        }
                    }
                }
                $modelUpdate= UserProfile::upsert($post,$id,$groupid);
                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', "Speciality/Treatments Updated");
                    return $this->redirect(['/doctor/profile']);
                }
            }

            if(isset($post['UserProfile']['services'])){
                $userServices=$post['UserProfile']['services'];
                if(!empty($userServices)){
                    $post['UserProfile']['services']=implode(',',$userServices);
                }
                foreach ($userServices as $key => $value) {
                    $servicesModel = MetaValues::find()->where(['key'=>11,'value' => $value])->one();
                    if(empty($servicesModel)) {
                        $servicesModel = new MetaValues();
                        $servicesModel->key =11;
                        $servicesModel->value = $value;
                        $servicesModel->label = $value;
                        $servicesModel->status = 0;
                        $servicesModel->save();
                    }
                }
                $modelUpdate= UserProfile::upsert($post,$id,$groupid);
                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', 'doctor Services Updated');
                    return $this->redirect(['/doctor/profile']);
                }else{
                    Yii::$app->session->setFlash('error', 'Sorry doctor Facility Not Added');

                }
            }
        }

        $specialityList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        $speciality=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>5])->all();

        $treatment=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>9])->all();

        $treatmentList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        $profilepercentage=DrsPanel::profiledetails($userModel,$userProfile,$groupid);

        $servicesList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->all();

        $services=DrsPanel::getMetaData('services',$id);

        return $this->render('/doctor/profile',['userModel'=>$userModel,'userProfile'=>$userProfile,'speciality' => $speciality,'specialityList' => $specialityList,'treatments' =>$treatment,'treatmentList' =>$treatmentList,'profilepercentage'=>$profilepercentage['complete_percentage'],'services' => $services,'servicesList' => $servicesList]);
    }

    public function actionAjaxTreatmentList(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $form = ActiveForm::begin(['id' => 'profile-form']);
            $post=Yii::$app->request->post();
            $treatment_list=[];
            if(isset($post['id']) && !empty($post['user_id'])){
                $userProfile=UserProfile::findOne(['user_id'=>$post['user_id']]);
                $key=MetaValues::findOne(['value'=>$post['id']]);
                $treatments=MetaValues::find()->andWhere(['status'=>1,'key'=>9,'parent_key'=>$key->id])->all();
                $all_active_values=array();
                foreach ($treatments as $treatment) {
                    $all_active_values[]=$treatment->value;
                    $treatment_list[$treatment->value] = $treatment->label;
                }

                $treatments=$userProfile->treatment;
                if(!empty($treatments)){
                    $treatments=explode(',',$treatments);
                    foreach($treatments as $treatment){
                        if(!in_array($treatment,$all_active_values)){
                            $checkValue=MetaValues::find()->where(['parent_key'=>$key->id,'value'=>$treatment])->one();
                            if(!empty($checkValue)){
                                $treatment_list[$checkValue->value] = $checkValue->label;
                            }
                        }
                    }
                }

                echo $this->renderAjax('/doctor/ajax-treatment-list',['form'=>$form,'treatment_list'=>$treatment_list,'userProfile'=>$userProfile]); exit();
            }
        }

    }

    /*Hospital Request*/
    public function actionAcceptHospitalRequest($doctor_id = NULL){
        $doctor_id = $this->loginUser->id;
        $usergroupid = Groups::GROUP_HOSPITAL;


        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            if(isset($post['UserRequest']['request_to'])){
                $Userrequstto=$post['UserRequest']['request_to'];
                foreach ($Userrequstto as  $value) {
                    $postData['groupid']=Groups::GROUP_HOSPITAL;
                    $postData['request_from']=$hospital_id;
                    $postData['request_to']=$value;
                    $postData['status']=2;
                    $type= 'Add';
                    $modelUpdate = UserRequest::updateStatus($postData,$type);
                }
                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', 'Requested sent');
                    return $this->redirect(['/doctor/accept-hospital-request']);
                }else{
                    Yii::$app->session->setFlash('error', 'Sorry request couldnot sent');
                }
            }

            if(isset($post['UserProfile']['name'])){
                $lists = UserProfile::find()->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->one();
                return $this->render('/doctor/accept-hospital-request',['listsRecord'=> $lists,'doctor_id' =>$doctor_id ]);
            }

        }
        $lists=UserRequest::find()->andWhere(['request_to'=>$doctor_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        return $this->render('/doctor/accept-hospital-request',['lists'=> $lists,'doctor_id' =>$doctor_id ]);

    }

    public function actionUpdateStatus($doctor_id = NULL){
        /*$lists= DrsPanel::myPatients(['doctor_id'=>$this->loginUser->id]);*/
        $hospital_id = $this->loginUser->id;
        $usergroupid = Groups::GROUP_HOSPITAL;
        if(!empty($doctor_id) && !empty($hospital_id) ){
            $model=UserRequest::find()->andWhere(['request_from'=>$hospital_id,'request_to'=>$hospital_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();
        }else{
            $model = new UserRequest();
        }
        if(Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $post['groupid']=Groups::GROUP_HOSPITAL;
            $type= 'Add';


            $modelUpdate = UserRequest::updateStatus($post,$type);
            if(count($modelUpdate)>0){
                Yii::$app->session->setFlash('success', 'Requested sent');
                return $this->redirect(['/doctor/accept-hospital-request']);
            }else{
                Yii::$app->session->setFlash('error', 'Sorry request couldnot sent');

            }
        }

        exit;

        return Null;
    }

    /* My-Shifts List */

    public function actionMyShifts(){
        $ids=Yii::$app->user->id;
        $address_list=DrsPanel::doctorHospitalList($ids);
        return $this->render('shift/my-shifts',['doctor_id'=>$ids,'address_list'=>$address_list['apiList']]);
    }

    /* Add-Edit Shifts*/
    
    public function actionAddShift($id = NULL){
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
        $newShift->user_id=$this->loginUser->id;
        if($id){
            $userShift= UserSchedule::findOne($id);
            $newShift->setShiftData($userShift);
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
                    // echo '<pre>';
                    // print_r($dayShiftsFromDb);
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
                } die;
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
                    return $this->redirect(['/doctor/my-shifts']);
                }
            } 
        } 
        $scheduleslist= DrsPanel::weekSchedules($this->loginUser->id);
        $hospitals= DrsPanel::doctorHospitalList($this->loginUser->id);
        return $this->render('shift/add-shift',['defaultCurrrentDay'=>strtotime($date),'hospitals'=>$hospitals['apiList'],'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'listaddress'=>$hospitals['listaddress'],'scheduleslist'=>$scheduleslist,
            'modelAddress' => $addAddress, 'userAdddressImages' => $imgModel]);
    }

    public function saveShiftData($ids,$keyClnt,$post,$day_shift,$shiftcount)
    {
       
            $shift['AddScheduleForm']['user_id']=$ids;
            $shift['AddScheduleForm']['start_time']=$post['AddScheduleForm']['start_time'][$keyClnt];
            $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$keyClnt];
            $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$keyClnt];
            $shift['AddScheduleForm']['weekday']=$day_shift[$keyClnt];
            $time1 = strtotime($shift['AddScheduleForm']['start_time']);
            $time2 = strtotime($shift['AddScheduleForm']['end_time']);
            $difference = abs($time2 - $time1) / 60;
            $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];
            $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
            $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$keyClnt]) && ($post['AddScheduleForm']['consultation_fees'][$keyClnt] > 0) )?$post['AddScheduleForm']['consultation_fees'][$keyClnt]:0;
            $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$keyClnt]) && ($post['AddScheduleForm']['emergency_fees'][$keyClnt] > 0))?$post['AddScheduleForm']['emergency_fees'][$keyClnt]:0;
            $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['consultation_fees_discount'][$keyClnt]:0;
            $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]))?$post['AddScheduleForm']['emergency_fees_discount'][$keyClnt]:0;
        
        $this->pr($shift);
        return $shift;
    }

    public function shiftValidate($startTimeClnt,$endTimeClnt,$startTimeDb,$endTimeDb,$canAddEdit,$errorShift,$errorIndex,$dayshiftValuedb)
    {
         if($startTimeClnt == $startTimeDb && $startTimeClnt == $endTimeDb)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;
            $msg = ' already exists';
        }
        if($startTimeClnt >= $startTimeDb && $startTimeClnt <= $endTimeDb)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;
        }

        elseif($endTimeClnt >= $startTimeDb && $endTimeClnt <= $endTimeDb)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;

        }
        elseif($startTimeDb >= $startTimeClnt && $startTimeDb <= $endTimeClnt)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;

        }
        elseif($endTimeDb >= $startTimeClnt && $endTimeDb <= $endTimeClnt)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;

        }
        elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb)
        {
            $canAddEdit = false;
            $errorShift[$errorIndex] =  $dayshiftValuedb;
            $errorIndex++;
        }
    }

    public function actionEditShift($id = NULL){
        $loginID=Yii::$app->user->id;
        $address=UserAddress::findOne($id);
        $addressImages = UserAddressImages::find()->where(['address_id'=>$id])->all();
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
        $shifts=DrsPanel::getShiftListByAddress($loginID,$id);

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            //$this->pr($post);die;
            if(isset($post['UserAddress'])){
                $user=User::findOne(['id'=>$loginID]);
                if(!empty($user)){
                    $addAddress=UserAddress::findOne($id);
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
                                return $this->redirect(['/doctor/my-shifts']);   
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
                                                    return $this->redirect(['/doctor/my-shifts']);
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
                                            return $this->redirect(['/doctor/my-shifts']);
                                        }

                                    }
                                }die; 
                            }
                        }
                    }else
                    {
                         Yii::$app->session->setFlash('error', 'Please Select days');
                        return $this->redirect(['/doctor/my-shifts']);
                    }
                }
            }
        }
        return $this->render('shift/edit-shift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'shifts'=>$shifts, 'modelAddress' => $address, 'userAdddressImages' => $imgModel,'addressImages'=>$addressImages,'disable_field'=>$disable_field]);
}

    public function actionEditShift2($id = NULL){
        $ids=Yii::$app->user->id;
        $address=UserAddress::findOne($id);
        $addressImages = UserAddressImages::find()->where(['address_id'=>$id])->all();
        $imgModel= new UserAddressImages();

        if($address->user_id == $ids){
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
        $newShift->user_id=$ids;
        $shifts=DrsPanel::getShiftListByAddress($ids,$id);


        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();

            if(isset($post['UserAddress'])){
                $address->load(Yii::$app->request->post());
                $address->user_id = $ids;
                $userAddressLastId  = UserAddress::find()->orderBy(['id'=> SORT_DESC])->one();
                if($address->save()){
                    $upload = UploadedFile::getInstance($address, 'image');
                    if(!empty($upload))
                    {
                        $imageUpload=DrsImageUpload::updateAddressImageWeb($address->id,$upload);
                        $imgModel->load(Yii::$app->request->post());
                        $uploads = UploadedFile::getInstances($imgModel, 'image');
                        $listimageUpload=DrsImageUpload::updateAddressImageListWeb($address->id,$uploads);
                    }
                    // Add Shift in Myschedule page
                }
            }
            $shift=array();
            $address_id=$post['UserAddress']['id'];
            $shift=array();
            $shiftcount=$post['AddScheduleForm']['weekday'];

            foreach($shiftcount as $key=>$shift_v) {
                foreach ($shift_v as $shkey => $shif) {
                   if(isset($shifts[$key]) && !empty($shifts[$key])){

                    $oldAddressWeeks=$shifts[$key]['shifts'];

                    foreach($oldAddressWeeks as $sc_id=>$weekday){

                        if(in_array($weekday,$post['AddScheduleForm']['weekday'][$key])){

                           $shift['AddScheduleForm']['address_id']=$address_id;
                           $shift['AddScheduleForm']['user_id']=$ids;

                           $shift['AddScheduleForm']['start_time']=$post['AddScheduleForm']['start_time'][$key];
                           $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$key];
                           $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$key];
                           $shift['AddScheduleForm']['weekday']=$post['AddScheduleForm']['weekday'][$key];
                           $time1 = strtotime($shift['AddScheduleForm']['start_time'][$key]);
                           $time2 = strtotime($shift['AddScheduleForm']['end_time'][$key]);
                           $difference = abs($time2 - $time1) / 60;
                           $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];

                           $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
                           $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$key]) && ($post['AddScheduleForm']['consultation_fees'][$key] > 0) )?$post['AddScheduleForm']['consultation_fees'][$key]:0;
                           $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$key]) && ($post['AddScheduleForm']['emergency_fees'][$key] > 0))?$post['AddScheduleForm']['emergency_fees'][$key]:0;
                           $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$key]))?$post['AddScheduleForm']['consultation_fees_discount'][$key]:0;
                           $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$key]))?$post['AddScheduleForm']['emergency_fees_discount'][$key]:0;
                            // $userScheduleData=UserSchedule::find()->where(['id'=>$sc_id])->all();

                           $addUpdateShift=DrsPanel::upsertShift($shift,$sc_id,$address_id);
                            //update
                       }
                       else {

                        $userScheduleData=UserSchedule::find()->where(['user_id'=>$ids,'weekday' => $weekday,'is_edit' => 1])->all();
                        foreach ($userScheduleData as $key => $value) {
                            $value->delete();
                        }
                            //delete weekday shift
                    }
                }



            }
            else {
                    // echo 'fdsaf';die;
                $shift['AddScheduleForm']['address_id']=$address_id;
                $shift['AddScheduleForm']['user_id']=$ids;

                $shift['AddScheduleForm']['start_time']=$post['AddScheduleForm']['start_time'][$key];
                $shift['AddScheduleForm']['end_time']=$post['AddScheduleForm']['end_time'][$key];
                $shift['AddScheduleForm']['appointment_time_duration']=$post['AddScheduleForm']['appointment_time_duration'][$key];
                $shift['AddScheduleForm']['weekday']=$post['AddScheduleForm']['weekday'][$key];
                $time1 = strtotime($shift['AddScheduleForm']['start_time'][$shkey]);
                $time2 = strtotime($shift['AddScheduleForm']['end_time'][$shkey]);
                $difference = abs($time2 - $time1) / 60;
                $patient_limit=$difference/$shift['AddScheduleForm']['appointment_time_duration'];

                $shift['AddScheduleForm']['patient_limit']=(int)$patient_limit;
                $shift['AddScheduleForm']['consultation_fees']=(isset($post['AddScheduleForm']['consultation_fees'][$key]) && ($post['AddScheduleForm']['consultation_fees'][$key] > 0) )?$post['AddScheduleForm']['consultation_fees'][$key]:0;
                $shift['AddScheduleForm']['emergency_fees']=(!empty($post['AddScheduleForm']['emergency_fees'][$key]) && ($post['AddScheduleForm']['emergency_fees'][$key] > 0))?$post['AddScheduleForm']['emergency_fees'][$key]:0;
                $shift['AddScheduleForm']['consultation_fees_discount']=(isset($post['AddScheduleForm']['consultation_fees_discount'][$key]))?$post['AddScheduleForm']['consultation_fees_discount'][$key]:0;
                $shift['AddScheduleForm']['emergency_fees_discount']=(isset($post['AddScheduleForm']['emergency_fees_discount'][$key]))?$post['AddScheduleForm']['emergency_fees_discount'][$key]:0;
                    // echo '<pre>';
                    // print_r($shift);die;
                $addUpdateShift=DrsPanel::upsertShift($shift,0,$address_id);
            }

        }
    }


    return $this->redirect(['/doctor/my-shifts']);
}

return $this->render('shift/edit-shift',['defaultCurrrentDay'=>strtotime($date),'model'=>$newShift,'weeks'=>$week_array,'availibility_days'=>$availibility_days,'shifts'=>$shifts, 'modelAddress' => $address, 'userAdddressImages' => $imgModel,'addressImages'=>$addressImages,'disable_field'=>$disable_field]);
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

/* Today Timing*/
public function actionDayShifts(){
    $user_id=Yii::$app->user->id;
    $doctor=User::findOne($user_id);
    $date=date('Y-m-d');
    $getShists=DrsPanel::getBookingShifts($user_id,$date,$user_id);
    return $this->render('shift/day-shifts',
        ['defaultCurrrentDay'=>strtotime($date),'shifts'=>$getShists,'doctor'=>$doctor]);
}

public function actionUpdateShiftStatus(){
    $response["status"] = 0;
    $response["error"] = true;
    $response['message']= 'You have do not permission.';
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $params['booking_closed']=$post['status'];
        $params['doctor_id']=$this->loginUser->id;
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
        $days_plus=$post['plus'];
        $operator=$post['operator'];
        $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));
        $appointments= DrsPanel::getBookingShifts($this->loginUser->id,$date,$this->loginUser->id);
        echo $this->renderAjax('shift/_address-with-shift',
            ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments]); exit();
    }
}

public function actionGetShiftDetails(){
    $user_id=Yii::$app->user->id;
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        if(isset($post['id']) && isset($post['date'])){
            $schedule_id = $post['id'];
             $shift_id = $post['shift_id'];
            $date=date('Y-m-d',$post['date']);
            $weekday=DrsPanel::getDateWeekDay($date);
            $userSchedule=UserScheduleDay::find()->where(['schedule_id'=>$schedule_id,'date'=>$date,'weekday'=>$weekday,'user_id'=>$user_id])->one();
            if(empty($userSchedule)){
                $userSchedule=UserSchedule::findOne($schedule_id);
            }
            if(!empty($userSchedule)){
                $model= new AddScheduleForm();
                $model->setShiftData($userSchedule);
                $model->id=$shift_id;
                $model->user_id=$userSchedule->user_id;
                echo $this->renderAjax('shift/_day_shift_edit_form', ['model' => $model,'date'=>$date,'schedule_id' =>$schedule_id]); exit();
            }
        }
    }
    return NULL;
}

public function actionShiftUpdate(){
    $user_id=Yii::$app->user->id;
    $updateShift= new AddScheduleForm();
    if (Yii::$app->request->isAjax) {
        $updateShift->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($updateShift);
    }
    if(Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $shift_id=$post['AddScheduleForm']['id'];
        $update_shift=DrsPanel::updateShiftTiming($shift_id,$post);
        if(isset($update_shift['error']) && $update_shift['error']==true)
        {
            Yii::$app->session->setFlash('shifterror', $update_shift['message']);
        }
        else 
        {
            Yii::$app->session->setFlash('success', 'Shift updated successfully');
        }
        return $this->redirect(['/doctor/day-shifts']);
    }
}




/****************************Booking/Appointment***************************************/
public function actionAppointments($type =''){
    $id=Yii::$app->user->id;
    $doctor=User::findOne($id);
    $date=date('Y-m-d');
    if($type == 'current_appointment'){
        $current_shifts=0; $bookings=array();
        $getShists=DrsPanel::getBookingShifts($this->loginUser->id,$date,$this->loginUser->id);
        $appointments=DrsPanel::getCurrentAppointments($this->loginUser->id,$date,$current_shifts,$getShists);
        if(!empty($appointments)){
            if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                $current_shifts=$appointments['shifts'][0]['schedule_id'];
                $bookings = $appointments['bookings'];
            }
        }

        return $this->render('/doctor/appointment/current-appointments',
            ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'bookings'=>$bookings,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor]);
    }
    elseif($type == 'current_shift'){
        $current_shifts='';
        $getSlots=DrsPanel::getBookingShifts($id,$date,$id);
        $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
        if(!empty($checkForCurrentShift)){

            $current_shifts=isset($checkForCurrentShift['shift_id'])?$checkForCurrentShift['shift_id']:'';
            $current_affairs=DrsPanel::getCurrentAffair($checkForCurrentShift,$this->loginUser->id,$date,$current_shifts,$getSlots);

            if($current_affairs['status'] && empty($current_affairs['error'])){

                $shifts=$current_affairs['all_shifts'];
                $appointments=$current_affairs['data'];
                $current_shifts=$current_affairs['schedule_id'];
                return $this->render('/doctor/appointment/current-affair',['schedule_id'=>$current_affairs['schedule_id'],'is_completed'=>$current_affairs['is_completed'],'is_started'=>$current_affairs['is_started'],'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>'current_shift','date'=>$date]);
            }
            else {
                return $this->render('/doctor/appointment/current-affair',['schedule_id'=> '','is_completed'=> '','is_started'=>'','Shifts'=> array(),'appointments'=> array(),'current_shifts'=> array(),'doctor'=>$doctor,'type'=>'current_shift','date'=>$date]);

            }
        }
        else {
            Yii::$app->session->setFlash('error', 'Sorry doctor Facility Not Added');
        }
    }
    else{
        $type='book';
        $current_shifts=0; $slots=array();
        $getShists=DrsPanel::getBookingShifts($id,$date,$id);
        $appointments=DrsPanel::getCurrentAppointments($id,$date,$current_shifts,$getShists);
        if(!empty($appointments)){
            if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                $current_shifts=$appointments['shifts'][0]['schedule_id'];
                $slots = DrsPanel::getBookingShiftSlots($id,$date,$current_shifts,'available');
            }
        }
        return $this->render('/doctor/appointment/appointments',
            ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'slots'=>$slots,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor]);
    }
}

public function actionAjaxToken(){
    $result=['status'=>false,'msg'=>'Invalid Request.'];
    if(Yii::$app->request->post() && Yii::$app->request->isAjax){
        $post=Yii::$app->request->post();
        $current_shifts=$post['shift_id'];
        $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
        $slots = DrsPanel::getBookingShiftSlots($this->loginUser->id,$date,$current_shifts,'available');
        echo $this->renderAjax('/common/_slots',['slots'=>$slots,'doctor_id'=>$this->loginUser->id,'userType'=>'doctor']); exit();
    }
}

public function actionAjaxCurrentAppointment(){
    $result=['status'=>false,'msg'=>'Invalid Request.'];
    if(Yii::$app->request->post() && Yii::$app->request->isAjax) {
        $post = Yii::$app->request->post();
        $bookings = array();
        $current_shifts = $post['shift_id'];
        $id=$this->loginUser->id;
        $doctor=User::findOne($id);
        $date = (isset($post['date']) && !empty($post['date'])) ? $post['date'] : date('Y-m-d');

        $getSlots = DrsPanel::getBookingShifts($this->loginUser->id, $date, $this->loginUser->id);
        $checkForCurrentShift = DrsPanel::getDoctorCurrentShift($getSlots);
        if (!empty($checkForCurrentShift)) {
            $current_affairs = DrsPanel::getCurrentAffair($checkForCurrentShift, $this->loginUser->id, $date, $current_shifts, $getSlots);
            if ($current_affairs['status'] && empty($current_affairs['error'])) {
                $shifts = $current_affairs['all_shifts'];
                $appointments = $current_affairs['data'];
                foreach($shifts as $shift){
                    if($shift['schedule_id'] == $current_shifts){
                        $current_shifts = $shift['schedule_id'];
                        $is_started=$shift['is_started'];
                        $is_completed=$shift['is_completed'];
                        break;
                    }
                }
                echo $this->renderAjax('/common/_current_bookings', ['bookings' => $appointments, 'doctor_id' => $this->loginUser->id, 'userType' => 'doctor', 'schedule_id' => $current_shifts, 'is_completed' => $is_completed, 'is_started' => $is_started,'doctor'=>$doctor]);
                exit();


            }
        }

    }
}

public function actionAjaxAppointment(){
    $result=['status'=>false,'msg'=>'Invalid Request.'];
    if(Yii::$app->request->post() && Yii::$app->request->isAjax){
        $post=Yii::$app->request->post();
        $bookings=array();
        $current_shifts=$post['shift_id'];
        $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
        $getShists=DrsPanel::getBookingShifts($this->loginUser->id,$date,$this->loginUser->id);
        $appointments=DrsPanel::getCurrentAppointments($this->loginUser->id,$date,$current_shifts,$getShists);
        if(!empty($appointments)){
            if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                $bookings = $appointments['bookings'];
            }
        }
        echo $this->renderAjax('/common/_bookings',['bookings'=>$bookings,'doctor_id'=>$this->loginUser->id,'userType'=>'doctor']); exit();
    }
}

public function actionBookingConfirm(){
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $slot_id=explode('-',$post['slot_id']);

        $id=Yii::$app->user->id;
        $doctorProfile=UserProfile::find()->where(['user_id'=>$id])->one();
        if(!empty($doctorProfile)){
            $doctor=User::findOne($doctorProfile->user_id);
            $slot=UserScheduleSlots::find()->andWhere(['user_id'=>$doctor->id,'id'=>$slot_id[1]])->one();
            if($slot){
                $schedule=UserSchedule::findOne($slot->schedule_id);
                $model= new AppointmentForm();
                $model->doctor_id=$doctor->id;
                $model->slot_id=$slot->id;
                $model->schedule_id=$slot->schedule_id;
                return $this->renderAjax('/common/_booking_confirm.php',
                    ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,   'address'=>UserAddress::findOne($schedule->address_id), 'model'=> $model, 'userType'=>'doctor'
                    ]);

            }
        }
    }
}

public function actionBookingConfirmStep2(){
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $slot_id=$post['slot_id'];
        $id=Yii::$app->user->id;
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
                return $this->renderAjax('/common/_booking_confirm_step2.php',
                    ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,   'address'=>UserAddress::findOne($schedule->address_id), 'model'=> $model, 'userType'=>'doctor'
                    ]);

            }
        }
    }
    return NULL;
}

public function actionAppointmentBooked(){
    $user_id=Yii::$app->user->id;
    $response["status"] = 0;
    $response["error"] = true;
    $response['message']= 'Does not match require parameters';

    if(Yii::$app->request->post() && Yii::$app->request->isPost) {
        $post=Yii::$app->request->post();
        $postData=$post['AppointmentForm'];
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

                $userphone=User::find()->where(['phone'=>$postData['user_phone'],'groupid'=>Groups::GROUP_PATIENT])->one();
                if($userphone){
                    $user_id=$userphone->id;
                }else{
                    $user_id=0;
                }

                if($slot->status == 'available'){
                    $data['UserAppointment']['booking_type']=UserAppointment::BOOKING_TYPE_OFFLINE;
                    $data['UserAppointment']['booking_id']=DrsPanel::generateBookingID();
                    $data['UserAppointment']['type']=$slot->type;
                    $data['UserAppointment']['token']=$slot->token;

                    $data['UserAppointment']['user_id']=$user_id;
                    $data['UserAppointment']['user_name']=$postData['user_name'];
                    $data['UserAppointment']['user_age']=(isset($postData['age']))?$postData['age']:'0';
                    $data['UserAppointment']['user_phone']=$postData['user_phone'];
                    $data['UserAppointment']['user_address']=isset($postData['address'])?$postData['address']:'';
                    $data['UserAppointment']['user_gender']=(isset($postData['user_gender']))?$postData['user_gender']:'3';

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
                    $data['UserAppointment']['status']=UserAppointment::STATUS_AVAILABLE;
                    $data['UserAppointment']['payment_status']=UserAppointment::PAYMENT_COMPLETED;

                    $addAppointment=DrsPanel::addAppointment($data,'doctor');

                    if($addAppointment['type'] == 'model_error'){
                        $response=DrsPanel::validationErrorMessage($addAppointment['data']);
                    }
                    else{
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response['message']= 'Success';
                        $response['appointment_id']=$addAppointment['data'];

                            //Yii::$app->session->setFlash('success', "Appointment booked successfully.");
                            //return $this->renderAjax('/common/_booking_confirm_step2');

                            //return $this->redirect(['/doctor/appointments']);
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

public function actionCurrentAppointmentShiftUpdate(){
    $response = $data =  $required = array();
    $params = Yii::$app->request->post();

    $id=Yii::$app->user->id;
    $doctor=User::findOne($id);
    $date = date('Y-m-d');
    $shift = $params['schedule_id'];
    $status = $params['status'];
    if ($status == 'start') {
        $schedule_check=UserScheduleGroup::find()->where(['user_id'=>$doctor->id,'date'=>$date, 'status' => array('pending','current')])->orderBy('shift asc')->one();
        if (!empty($schedule_check)) {
            if($schedule_check->schedule_id == $shift){
                $schedule_check->status = 'current';
                if ($schedule_check->save()) {
                    $checkFirstAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>UserAppointment::STATUS_ACTIVE])->orderBy('token asc')->one();
                    if(empty($checkFirstAppointment)){
                        $checkFirstAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>UserAppointment::STATUS_AVAILABLE])->orderBy('token asc')->one();
                        if(!empty($checkFirstAppointment)){
                            $checkFirstAppointment->status=UserAppointment::STATUS_ACTIVE;
                            $checkFirstAppointment->save();
                        }
                    }
                    $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                    $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                    if(!empty($checkForCurrentShift)){
                        $response=DrsPanel::getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$shift,$getSlots);
                    }
                    else{
                        $response["status"] = 0;
                        $response["data"]=$checkForCurrentShift;
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response["data"]=$schedule_check->getErrors();
                    $response['message'] = 'Shift error';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = ucfirst($schedule_check->shift_label).' is pending';
            }
        } else {
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Shift not found';
        }
    }
    elseif ($status == 'completed') {
        $schedule_check = UserScheduleGroup::find()->where(['user_id' => $params['doctor_id'], 'date' => $date, 'status' => 'current'])->one();
        if (!empty($schedule_check)) {
            $schedule_check->status = 'completed';
            if ($schedule_check->save()) {
                $date=date('Y-m-d');
                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                if(!empty($checkForCurrentShift)){
                    $response=DrsPanel::getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$shift,$getSlots);
                }

            }
        } else {
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Shift not found';
        }
    }
    else {
        $response["status"] = 0;
        $response["error"] = true;
        $response['message'] = 'Please try again.';
    }
    echo json_encode($response); exit;
}

public function actionAppointmentStatusUpdate(){
    $res=['status'=>false];
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $params=Yii::$app->request->post();
        $id=Yii::$app->user->id;
        $doctor=User::findOne($id);
        $date = date('Y-m-d');
        $shift = $params['schedule_id'];
        $status = $params['status'];

        if ($status == 'next' || $status == 'skip') {
            $checkFirstAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>UserAppointment::STATUS_ACTIVE])->orderBy('token asc')->one();
            $status_update=($status=='next')?UserAppointment::STATUS_COMPLETED:UserAppointment::STATUS_SKIP;
            $checkFirstAppointment->status=$status_update;
            if($checkFirstAppointment->save()){
                $res=['status'=>true];
                $secondAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>UserAppointment::STATUS_AVAILABLE])->orderBy('token asc')->one();
                if(!empty($secondAppointment)){
                    $secondAppointment->status=UserAppointment::STATUS_ACTIVE;
                    $secondAppointment->save();
                }
                else{
                    $secondAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>UserAppointment::STATUS_SKIP])->orderBy('token asc')->one();
                    if(!empty($secondAppointment)){
                        $secondAppointment->status=UserAppointment::STATUS_ACTIVE;
                        $secondAppointment->save();
                    }
                }
            }
        }
        else{

        }
    }
    return json_encode($res);
}

public function actionGetNextSlots(){
    if(Yii::$app->request->post()){
        $post = Yii::$app->request->post();
        $user_id=$post['user_id'];
        $days_plus=$post['plus'];
        $operator=$post['operator'];
        $type=$post['type'];
        $first = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['key']));
        $dates_range=DrsPanel::getSliderDates($first);

        $result['status']=true;
        $result['result']=$this->renderAjax('/common/_appointment_date_slider',['doctor_id'=>$user_id,'dates_range' => $dates_range,'date'=>$first,'type'=>$type,'userType'=>'doctor']);
        $result['date']=$first;
        echo json_encode($result);
        exit;

    }
    echo 'error'; exit;
}

public function actionGetDateShifts(){
    if(Yii::$app->request->post()){
        $post = Yii::$app->request->post();
        $id=Yii::$app->user->id;
        $doctor=User::findOne($id);
        $days_plus=$post['plus'];
        $operator=$post['operator'];
        $type=$post['type'];
        $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));

        $current_shifts=0; $slots=array(); $bookings=array();
        $getShists=DrsPanel::getBookingShifts($this->loginUser->id,$date,$this->loginUser->id);
        $appointments=DrsPanel::getCurrentAppointments($this->loginUser->id,$date,$current_shifts,$getShists);
        if(!empty($appointments)){
            if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                $current_shifts=$appointments['shifts'][0]['schedule_id'];
                if($type == 'book'){
                    $slots = DrsPanel::getBookingShiftSlots($this->loginUser->id,$date,$current_shifts,'available');
                }
                else{
                    $bookings = $appointments['bookings'];
                }
            }
        }
        echo $this->renderAjax('/common/_appointment_shift_slots',['appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type,'slots'=>$slots,'bookings'=>$bookings,'userType'=>'doctor']);exit;
    }
    echo 'error'; exit;
}

public function actionGetAppointmentDetail(){
    if(Yii::$app->request->post() && Yii::$app->request->isAjax){
        $post=Yii::$app->request->post();
        $appointment_id=$post['appointment_id'];
        $booking_type=isset($post['booking_type'])?$post['booking_type']:'';
        $appointment=UserAppointment::find()->where(['id'=>$appointment_id])->one();
        $booking=DrsPanel::patientgetappointmentarray($appointment);
        echo $this->renderAjax('/common/_booking_detail',['booking'=>$booking,'doctor_id'=>$this->loginUser->id,'userType'=>'doctor','booking_type' => $booking_type]); exit();
    }
}

public function actionAppointmentPaymentConfirm(){
    if(Yii::$app->request->post() && Yii::$app->request->isAjax){
        $post=Yii::$app->request->post();
        $appointment_id=$post['appointment_id'];
        $booking_type=isset($post['booking_type'])?$post['booking_type']:'';

        $appointment=UserAppointment::find()->where(['id'=>$appointment_id])->one();
        if($booking_type=='pending') {
            $appointment->status ='available';
        }
        else {
            $appointment->status ='pending';
        }
        $appointment->save();
        echo 'success'; exit();
    }
}

/****************************History***************************************/
public function actionPatientHistory(){
    $date=date('Y-m-d');
    $user_id=Yii::$app->user->id;
    $doctor=User::findOne($user_id);
    $current_selected=0;
    $checkForCurrentShift=0;
    $appointments=$shiftAll=$typeCount=$history=[];
    $getSlots=DrsPanel::getBookingShifts($user_id,$date,$user_id);
    if(!empty($getSlots)){
        $checkForCurrentShift=$getSlots[0]['schedule_id'];
        $current_selected = $checkForCurrentShift;
        $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$current_selected,$getSlots,'');
        $shiftAll=DrsPanel::getDoctorAllShift($user_id,$date,$checkForCurrentShift,$getSlots,$current_selected);
        $appointments=$getAppointments['bookings'];
        $history=$getAppointments['total_history'];
        $typeCount=$getAppointments['type'];
    }
    return $this->render('/doctor/history-statistics/patient-history',['history_count'=>$history,'typeCount'=>$typeCount,'appointments'=>$appointments,'shifts'=>$shiftAll,'defaultCurrrentDay'=>strtotime($date),'doctor'=>$doctor,'current_selected'=>$current_selected]);
}

public function actionAjaxHistoryContent(){
    $user_id=Yii::$app->user->id;
    $doctor=User::findOne($user_id);
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $doctor_id=$post['user_id'];
        $days_plus=$post['plus'];
        $operator=$post['operator'];
        $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));
        $current_selected=0;
        $checkForCurrentShift=0;
        $appointments=$shiftAll=$typeCount=$history=[];
        $getSlots=DrsPanel::getBookingShifts($user_id,$date,$user_id);
        if(!empty($getSlots)){
            $checkForCurrentShift=$getSlots[0]['schedule_id'];
            if($current_selected == 0){
                $current_selected = $checkForCurrentShift;
            }
            $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$current_selected,$getSlots);
            $shiftAll=DrsPanel::getDoctorAllShift($user_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
            $appointments=$getAppointments['bookings'];
            $history=$getAppointments['total_history'];
            $typeCount=$getAppointments['type'];
        }
        echo $this->renderAjax('/doctor/history-statistics/_history-content',['history_count'=>$history,'typeCount'=>$typeCount,'appointments'=>$appointments,'shifts'=>$shiftAll,'doctor'=>$doctor,'current_selected'=>$current_selected]); exit;
    }
    echo 'error'; exit;
}

public function actionAjaxHistoryAppointment() {
    $result=['status'=>false,'msg'=>'Invalid Request.'];
    if(Yii::$app->request->post() && Yii::$app->request->isAjax){
        $post=Yii::$app->request->post();
        $bookings=array();
        $current_shifts=$post['shift_id'];
        $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
        $getShists=DrsPanel::getBookingShifts($this->loginUser->id,$date,$this->loginUser->id);
        $appointments=DrsPanel::getCurrentAppointments($this->loginUser->id,$date,$current_shifts,$getShists);
        if(!empty($appointments)){
            if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                $bookings = $appointments['bookings'];
            }
        }
        echo $this->renderAjax('/doctor/history-statistics/_history-patient',['appointments'=>$bookings,'doctor_id'=>$this->loginUser->id,'userType'=>'doctor']); exit();
    }
}

public function actionUserStatisticsData(){
    $date=date('Y-m-d');
    $user_id=Yii::$app->user->id;
    $doctor=User::findOne($user_id);
    $current_selected=0;
    $checkForCurrentShift=0;
    $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
    $appointments=$shiftAll=$typeCount=[];
    $getSlots=DrsPanel::getBookingShifts($user_id,$date,$user_id);
    if(!empty($getSlots)){
        $checkForCurrentShift=$getSlots[0]['schedule_id'];
        $current_selected = $checkForCurrentShift;
        $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$current_selected,$getSlots,$typeselected);
        $shiftAll=DrsPanel::getDoctorAllShift($user_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
        $appointments=$getAppointments['bookings'];
        $typeCount=$getAppointments['type'];
    }
    return $this->render('/doctor/history-statistics/user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'defaultCurrrentDay'=>strtotime($date),'doctor'=>$doctor,'current_selected'=>$current_selected]);
}

public function actionAjaxUserStatisticsData(){
    $user_id=Yii::$app->user->id;
    $doctor=User::findOne($user_id);
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $days_plus=$post['plus'];
        $operator=$post['operator'];
        $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));

        $current_selected=0;
        $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
        $checkForCurrentShift=0;
        $appointments=$shiftAll=$typeCount=[];
        $getSlots=DrsPanel::getBookingShifts($this->loginUser->id,$date,$user_id);
        if(!empty($getSlots)){
            $checkForCurrentShift=$getSlots[0]['schedule_id'];
            $current_selected = $checkForCurrentShift;
            $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$current_selected,$getSlots,$typeselected);
            $shiftAll=DrsPanel::getDoctorAllShift($user_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
            $appointments=$getAppointments['bookings'];
            $typeCount=$getAppointments['type'];
        }
        return $this->renderAjax('/doctor/history-statistics/_user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'date'=>strtotime($date),'doctor'=>$doctor,'current_shifts'=>$current_selected]);
    }
}

public function actionAjaxStatisticsData(){
    $user_id=$this->loginUser->id;
    $result['status']=false;
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $doctor=User::findOne($user_id);
        $date=($post['date'])?date('Y-m-d',strtotime($post['date'])):date('Y-m-d');
        if(isset($post['type'])){
            $typeselected=($post['type']=='online')?UserAppointment::BOOKING_TYPE_ONLINE:UserAppointment::BOOKING_TYPE_OFFLINE;
        }
        else{
            $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
        }
        $checkForCurrentShift=(isset($post['shift_id']))?$post['shift_id']:0;
        $appointments=$shiftAll=$typeCount=[];
        $getSlots=DrsPanel::getBookingShifts($this->loginUser->id,$date,$user_id);
        if(!empty($getSlots)){
            $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$checkForCurrentShift,$getSlots,$typeselected);
            $appointments=$getAppointments['bookings'];
            $typeCount=$getAppointments['type'];
        }
        $result['status']=true;
        $result['appointments']=$this->renderAjax('/common/_appointment-token',['appointments'=>$appointments,'typeselected'=>$typeselected,'typeCount'=>$typeCount,'doctor'=>$doctor,'userType'=>'doctor']);
        $result['typeCount']=$typeCount;
        $result['typeselected']=$typeselected;
    }
    return json_encode($result);
}

public function actionAjaxPatientList(){
    $user_id=$this->loginUser->id;
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $date=($post['date'])?date('Y-m-d',strtotime($post['date'])):date('Y-m-d');
        $current_selected=($post['schedule_id'])?$post['schedule_id']:0;
        $checkForCurrentShift=0;
        $appointments=$shiftAll=$typeCount=[];
        $getSlots=DrsPanel::getBookingShifts($this->loginUser->id,$date,$user_id);
        if(!empty($getSlots)){
            $getAppointments=DrsPanel::appointmentHistory($user_id,$date,$current_selected,$getSlots);
            $appointments=$getAppointments['bookings'];
            $typeCount=$getAppointments['type'];
        }
        return $this->renderAjax('/doctor/history-statistics/_history-patient',['appointments'=>$appointments]);
    }
    return NULL;
}

/* Attender*/
public function actionAttendersList(){
    $model = new AttenderForm();
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    $id = $this->loginUser->id;

    $shifts  = DrsPanel::getBookingAddressShifts($id,date('Y-m-d',$id));
    $shift_array = array();
    foreach ($shifts as $key => $shift) {
        if($shift['hospital_id']==0) {
            $shift_array[$key] = $shift['address_line'].' '.$shift['shift_name'].' '.$shift['shifts_list'];
        }
    }
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();


        $model->load($post);
        $model->groupid=Groups::GROUP_ATTENDER;
        $model->parent_id=$this->loginUser->id;
        $model->created_by='Doctor';
        $upload = UploadedFile::getInstance($model, 'avatar');
        if(!empty($upload)) {
            $uploadDir = Yii::getAlias('@storage/web/source/attenders/');
            $image_name=time().rand().'.'.$upload->extension;
            $model->avatar=$image_name;
            $model->avatar_path='/storage/web/source/attenders/';
            $model->avatar_base_url =Yii::getAlias('@frontendUrl');
        }
        if($res = $model->signup()){
            if(!empty($upload)){
                $upload->saveAs($uploadDir .$image_name );
            }
            if(!empty($post['AttenderForm']['shift_id']) && count($post['AttenderForm']['shift_id'])>0) {
                $sel_shift=$post['AttenderForm']['shift_id'];
                $shift_val=array();
                foreach($sel_shift as $s){
                    $list=$shifts[$s]['shifts_id'];
                    foreach($list as $list){
                        $shift_val[]=$list;
                    }
                }
                $addupdateAttender=DrsPanel::addUpdateAttenderToShifts($shift_val,$res['id']);
            }
            Yii::$app->session->setFlash('success', "Attender Added!");
            return $this->redirect(['/doctor/attenders']);
        }
    }
    $addressList=DrsPanel::doctorHospitalList($this->loginUser->id);
    $list=DrsPanel::attenderList(['parent_id'=>$this->loginUser->id],'apilist');
    return $this->render('/doctor/attender/list',['list'=>$list,'user'=>$this->loginUser,
        'model' => $model,
        'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
        'hospitals'=>$addressList['listaddress'],
        'shifts'=>$shift_array]);
}

public function actionAttenderDetails(){
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $user =$this->findModel($post['id']);
        $id=Yii::$app->user->id;

        $selectedShifts=Drspanel::shiftList(['user_id'=>$id,'attender_id'=>$post['id']],'list');

        $shifts  = DrsPanel::getBookingAddressShifts($id,date('Y-m-d'),$id);


        $shift_array = $selectedShiftsIds= array();
        foreach ($shifts as $key => $shift) {
            if($shift['hospital_id']==0) {
                $shift_array[$key] = $shift['address_line'].' '.$shift['shift_name'].' '.$shift['shifts_list'];
                $shift_id_list=$shift['shifts_id'];

                foreach($selectedShifts as $select=>$valuesel){
                    if(in_array($select,$shift_id_list)){
                        $selectedShiftsIds[$key]=$key;
                    }
                }

            }
        }


        $addressList=DrsPanel::attenderHospitalList($post['id']);
        $model = new AttenderEditForm();
        $model->id=$post['id'];
        $model->name=$user['userProfile']['name'];
        $model->phone=trim($user->phone);
        $model->email=$user->email;
        $model->shift_id=$selectedShiftsIds;
        return $this->renderAjax('/doctor/attender/edit', [
            'model' => $model,
            'hospitals'=>$addressList,
            'shifts'=>$shift_array,
            ]);

    }
    return NULL;
}

public function actionAttenderUpdate(){
    $model = new AttenderEditForm();
    $id=Yii::$app->user->id;
    $shifts  = DrsPanel::getBookingAddressShifts($id,date('Y-m-d'),$id);

    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();

        $attender_id=$post['AttenderEditForm']['id'];
        $model->load($post);
        $upload = UploadedFile::getInstance($model, 'avatar');
        if(!empty($upload)) {
            $uploadDir = Yii::getAlias('@storage/web/source/attenders/');
            $image_name=time().rand().'.'.$upload->extension;
            $model->avatar=$image_name;
            $model->avatar_path='/storage/web/source/attenders/';
            $model->avatar_base_url =Yii::getAlias('@frontendUrl');
        }

        if($res = $model->update()){
            if(!empty($upload)){
                $upload->saveAs($uploadDir.$image_name );
            }
            if(!empty($post['AttenderEditForm']['shift_id']) && count($post['AttenderEditForm']['shift_id'])>0) {
                $sel_shift=$post['AttenderEditForm']['shift_id'];
                $shift_val=array();
                foreach($sel_shift as $s){
                    $list=$shifts[$s]['shifts_id'];
                    foreach($list as $list){
                        $shift_val[]=$list;
                    }
                }



                $addupdateAttender=DrsPanel::addUpdateAttenderToShifts($shift_val,$attender_id);
            }
            else{
                $addupdateAttender=DrsPanel::addUpdateAttenderToShifts(array(),$attender_id);
            }
            Yii::$app->session->setFlash('success', "Attender Updated!");

            return $this->redirect(['/doctor/attenders']);
        }
    }
    return NULL;
}

public function actionAttenderDelete(){
    if (Yii::$app->request->post() && Yii::$app->request->isAjax) {
        $post=Yii::$app->request->post();

        if($user=User::find()->andWhere(['id'=>$post['id']])->andWhere(['groupid'=>Groups::GROUP_ATTENDER])->andWhere(['parent_id'=>$this->loginUser->id])->one() ){
            if(DrsPanel::attenderDelete($user->id)){
                return '<p>Attender SuccessFully Deleted.</p>';
            }
        }
    }
    return NULL;
}

/*Experience*/
public function actionExperiences($exp_id=NULL){
    $user_id=Yii::$app->user->id;
    if($exp_id){
        $model= UserExperience::findOne($exp_id);
    }else{
        $model = new UserExperience();
    }
    $msg='Added';
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if(Yii::$app->request->isPost){
        $post=Yii::$app->request->post();
        $post['UserExperience']['user_id']=$user_id;
        if($exp_id){
            $post['UserExperience']['id']=$exp_id;
            $msg='Updated';
        }
        $modelUpdate= UserExperience::upsert($post);
        if(count($modelUpdate)>0){
            Yii::$app->session->setFlash('success', "Experience Added!");
            return $this->redirect(['/doctor/experiences']);
        }
    }

    $lists=UserExperience::find()->where(['user_id'=>$user_id])->all();
    return $this->render('/doctor/experience/experiences',['model' => $model,'lists'=>$lists]);
}

public function actionExperienceDetails($exp_id=NULL){
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $user = UserExperience::findOne($post['id']);


        $model = new UserExperience();
        $model->id=$user['id'];
        $model->user_id=$user['user_id'];
        $model->start=date('Y',$user['start']);
        $model->end=date('Y',$user['end']);
        $model->hospital_name=trim($user->hospital_name);
        return $this->renderAjax('/doctor/experience/_experiences_edit', [
            'model' => $model,
            ]);

    }
    return NULL;
}

public function actionExperienceUpdate(){
    $model = new UserExperience();
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $model->load($post);
        if($model->upsert($post)){
            Yii::$app->session->setFlash('success', "Experience Updated!");

            return $this->redirect(['/doctor/experiences']);
        }
    }
    return NULL;
}

public function actionExperienceDelete(){
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $experience = UserExperience::findOne($post['id']);
        $experience->delete();
    }
    Yii::$app->session->setFlash('success', "Experience Deleted!");
    return $this->redirect(['/doctor/experiences']);
}

/*Educations*/
public function actionEducations($exp_id=NULL){
    $user_id=Yii::$app->user->id;
    $model= UserEducations::findOne($exp_id);
    $msg='Added';
    if(empty($model))
        $model = new UserEducations();
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if(Yii::$app->request->isPost){
        $post=Yii::$app->request->post();
        $post['UserEducations']['user_id']=$user_id;
        if($exp_id){
            $post['UserEducations']['id']=$exp_id;
            $msg='Updated';
        }
        $modelUpdate= UserEducations::upsert($post);
        if(count($modelUpdate)>0){
            Yii::$app->session->setFlash('success', "Doctor Education Added!");
            return $this->redirect(['/doctor/educations']);
        }
    }
    $edu_list=UserEducations::find()->where(['user_id'=>$user_id])->all();
    return $this->render('/doctor/education/educations',['model' => $model,'edu_list'=>$edu_list]);
}

public function actionEducationDetails($exp_id=NULL){
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $user = UserEducations::findOne($post['id']);
        $model = new UserEducations();
        $model->id=$user['id'];
        $model->user_id=$user['user_id'];
        $model->start=$user['start'];
        $model->end=$user['end'];
        $model->education=trim($user->education);
        $model->collage_name=trim($user->collage_name);
        return $this->renderAjax('/doctor/education/_educations_edit', [
            'model' => $model,
            ]);
    }
    return NULL;
}

public function actionEducationUpdate(){
    $model = new UserEducations();
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $model->load($post);
        if($model->upsert($post)){
            return $this->redirect(['/doctor/educations']);
        }
    }
    return NULL;
}

public function actionEducationDelete(){
    if (Yii::$app->request->post()) {
        $post=Yii::$app->request->post();
        $education = UserEducations::findOne($post['id']);
        $education->delete();
    }
    return $this->redirect(['/doctor/educations']);
}

public function actionMyPatients(){
    $lists= DrsPanel::myPatients(['doctor_id'=>$this->loginUser->id]);
    return $this->render('/doctor/my-patients',['lists'=>$lists]);
}

public function actionCustomerCare(){
    $customer = MetaValues::find()->orderBy('id asc')->where(['key'=>8])->all();
    return $this->render('/doctor/customer-care', ['customer' => $customer]);
}

public function actionServices($service_id = NULL){
    $user_id=Yii::$app->user->id;
    $groupid = Groups::GROUP_DOCTOR;
    if($user_id){
        $model= UserProfile::findOne($user_id);
    }else{
        $model = new UserProfile();
    }
    $msg='Added';
    if (Yii::$app->request->isAjax) {
        $model->load($_POST);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ActiveForm::validate($model);
    }
    if(Yii::$app->request->isPost){
        $post=Yii::$app->request->post();
        if(isset($post['UserProfile']['services'])){
            $Userservices=$post['UserProfile']['services'];
            if(!empty($Userservices)){
                $post['UserProfile']['services']=implode(',',$Userservices);
            }

            $modelUpdate= UserProfile::upsert($post,$user_id,$groupid);
            if(count($modelUpdate)>0){
                Yii::$app->session->setFlash('success', 'doctor Services Updated');
                return $this->redirect(['/doctor/services']);
            }else{
                Yii::$app->session->setFlash('error', 'Sorry doctor Facility Not Added');

            }
        }

    }

    $servicesList=UserProfile::find()->andWhere(['user_id'=>$user_id])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->all();
    $services=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>11])->all();

    return $this->render('/doctor/services', ['model' => $model,'services' => $services,'servicesList' => $servicesList]);
}

public function actionAjaxCityList(){
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

    public function actionAjaxCancelAppointment(){
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $appointment_id=$post['appointment_id'];
            $appointment = UserAppointment::find()->where(['id' => $appointment_id])->one();
            $appointment->status=UserAppointment::STATUS_CANCELLED;
            if($appointment->save()){
                $slot_id=$appointment->slot_id;
                $schedule_id=$appointment->schedule_id;
                $slot=UserScheduleSlots::find()->where(['id'=>$slot_id,'schedule_id'=>$schedule_id])->one();
                $slot->status='available';
                $slot->save();

                Yii::$app->session->setFlash('success', "Appointment Cancelled!");
            }
            else{
                Yii::$app->session->setFlash('success', $appointment->getErrors());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
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

    /**
     * @inheritdoc
     */
    public function beforeAction($action){

        if (!Yii::$app->user->isGuest) {
            $groupid=Yii::$app->user->identity->userProfile->groupid;
            if($groupid != Groups::GROUP_DOCTOR){
                $this->redirect(array('/'));
            }
            else {
                return parent::beforeAction($action);
            }
        }

        $this->redirect(array('/'));
    }

    public function pr($data)
    {
        echo '<pre>';
        print_r($data);
    }
}