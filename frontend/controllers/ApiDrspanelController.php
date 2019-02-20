<?php
namespace frontend\controllers;
use backend\models\DoctorForm;
use backend\models\HospitalForm;
use backend\models\PatientForm;
use Codeception\Step\Meta;
use common\components\DrsImageUpload;
use common\components\DrsPanel;
use common\components\Logs;
use common\components\Payment;
use common\models\Article;
use common\models\HospitalAttender;
use common\models\MetaKeys;
use common\models\MetaValues;
use common\models\Tempuser;
use common\models\Transaction;
use common\models\User;
use common\models\UserAboutus;
use common\models\UserAddress;
use common\models\UserAddressImages;
use common\models\UserAppointment;
use common\models\UserAppointmentTemp;
use common\models\UserFavorites;
use common\models\UserExperience;
use common\models\UserEducations;
use common\models\UserRating;
use common\models\UserRatingLogs;
use common\models\UserReminder;
use common\models\UserSchedule;
use common\models\UserScheduleDay;
use common\models\UserScheduleGroup;
use common\models\UserScheduleSlots;
use common\models\PatientMembers;
use common\models\PatientMemberFiles;
use common\models\UserSettings;
use common\models\UserRequest;
use League\Uri\Modifiers\AppendLabel;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\helpers\Url;
use common\components\ApiFields;
use common\models\UserProfile;
use common\models\Groups;
use common\models\Page;
use backend\models\AddScheduleForm;
use backend\models\AttenderForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class ApiDrspanelController extends ActiveController
{
    public $modelClass = '';

    public function beforeAction($action){
        return parent::beforeAction($action);
        $this->enableCsrfValidation = false;
    }

    /*
    * @Param Null
    * @Function used for get gender
    * @return json
    */
    public function actionGender(){
        $gender[0]=array('id'=>UserProfile::GENDER_MALE,'label'=>'Male');
        $gender[1]=array('id'=>UserProfile::GENDER_FEMALE,'label'=>'Female');
        $gender[2]=array('id'=>UserProfile::GENDER_OTHER,'label'=>'Other');

        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $gender;
        $response['message']=Yii::t('db','Success');

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function used for get groups
    * @return json
    */
    public function actionGroups(){
        $groups = Groups::find()->where(['show' => Groups::GROUP_ACTIVE])->select(['id','name'])->asArray()->all();
        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $groups;
        $response['message']=Yii::t('db','Success');

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function used for get country code
    * @return json
    */
    public function actionGetCountryCode(){
        $countrycode=DrsPanel::getCountryCode();
        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $countrycode;
        $response['message']=Yii::t('common','Success');

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function used for get country code
    * @return json
    */
    public function actionGetStates(){
        $countrycode=DrsPanel::getStateList();
        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $countrycode;
        $response['message']=Yii::t('common','Success');

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function used for get city list by coountry
    * @return json
    */
    public function actionGetCity(){
        $response = $groups_v  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['state_id']) && $params['state_id'] != ''){
            $stateid=$params['state_id'];
            $countrycode=DrsPanel::getCitiesList($stateid);
            $response["status"] = 1;
            $response["error"] = false;
            $response['data'] = $countrycode;
            $response['message']=Yii::t('common','Success');
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'State id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function used for get meta data
     * @return json
     */
    public function actionGetMetaData(){
        $response = $groups_v  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['type']) && $params['type'] != ''){

            if($params['type'] == 'speciality' && isset($params['count']) && $params['count'] == 1 && $params['search_type'] == 'doctor'){
                if(isset($params['term'])){
                    $term = $params['term'];
                }
                else{
                    $term = '';
                }
                $lists= new Query();
                $lists=UserProfile::find();
                $lists->joinWith('user');
                $lists->where(['user_profile.groupid'=>Groups::GROUP_DOCTOR]);
                $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                    'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
                $command = $lists->createCommand();
                $countQuery = clone $lists;
                $countTotal=$countQuery->count();
                $fetchCount=Drspanel::fetchSpecialityCount($command->queryAll());
                $s_list=DrsPanel::getSpecialityWithCount('speciality',$fetchCount,$term);

                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $s_list;
                $response['message']='Success';
            }
            elseif($params['type'] == 'speciality' && isset($params['count']) && $params['count'] == 1 && $params['search_type'] == 'hospital'){

                if(isset($params['term'])){
                    $term = $params['term'];
                }
                else{
                    $term = '';
                }

                $lists= new Query();
                $lists=UserProfile::find();
                $lists->joinWith('user');
                $lists->where(['user_profile.groupid'=>Groups::GROUP_HOSPITAL]);
                $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                    'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
                $command = $lists->createCommand();
                $countQuery = clone $lists;
                $countTotal=$countQuery->count();
                $fetchCount=Drspanel::fetchHospitalSpecialityCount($command->queryAll());
                $s_list=DrsPanel::getSpecialityWithCount('speciality',$fetchCount,$term);

                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $s_list;
                $response['message']='Success';
            }
            else{
                $key=MetaKeys::findOne(['key'=>$params['type']]);
                if(!empty($key)){
                    $metavalues=MetaValues::find()->where(['key'=>$key->id,'status'=>1])->all();

                    $groups_v['type']=$key->key;
                    $groups_v['list']=array();
                    $m=0;$all_active_values=array();
                    foreach($metavalues as $values){
                        $all_active_values[]=$values->value;
                        $groups_v['list'][$m]['id']=$values->id;
                        $groups_v['list'][$m]['label']=$values->label;
                        $groups_v['list'][$m]['value']=$values->value;
                        if(isset($values->count)){
                            $groups_v['list'][$m]['count']=$values->count;
                        }
                        $groups_v['list'][$m]['icon']=($values->icon)?$values->base_path.$values->file_path.$values->icon:'';
                        $m++;
                    }

                    if($params['type'] == 'services' && !empty($params['user_id'])){
                        $user_id=$params['user_id'];
                        $profile=UserProfile::findOne($user_id);
                        $services=$profile->services;
                        if(!empty($services)){
                            $services=explode(',',$services);
                            foreach($services as $service){
                                if(!in_array($service,$all_active_values)){
                                    $checkValue=MetaValues::find()->where(['key'=>$key->id,'value'=>$service])->one();
                                    if(!empty($checkValue)){
                                        $groups_v['list'][$m]['id']=$checkValue->id;
                                        $groups_v['list'][$m]['label']=$checkValue->label;
                                        $groups_v['list'][$m]['value']=$checkValue->value;
                                        if(isset($checkValue->count)){
                                            $groups_v['list'][$m]['count']=$checkValue->count;
                                        }
                                        $groups_v['list'][$m]['icon']=($checkValue->icon)?$checkValue->base_path.$checkValue->file_path.$checkValue->icon:'';
                                        $m++;
                                    }

                                }
                            }
                        }
                    }

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['data'] = $groups_v;
                    $response['message']='Success';
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Type not found';
                }
            }
        }
        else{
            $keys=MetaKeys::find()->all();
            if(!empty($keys)){
                $l=0;
                foreach($keys as $key){
                    $metavalues=MetaValues::find()->where(['key'=>$key->id])->all();
                    $m=0;
                    $groups_v[$l]['type']=$key->key;
                    $groups_v[$l]['list']=array();
                    foreach($metavalues as $values){
                        $groups_v[$l]['list'][$m]['id']=$values->id;
                        $groups_v[$l]['list'][$m]['label']=$values->label;
                        $groups_v[$l]['list'][$m]['value']=$values->value;
                        $groups_v[$l]['list'][$m]['value']=$values->count;
                        $groups_v[$l]['list'][$m]['icon']=($values->icon)?$values->base_path.$values->file_path.$values->icon:'';
                        $m++;
                    }
                    $l++;
                }
                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $groups_v;
                $response['message']='Success';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Data not found';
            }
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for getting list of title types by user group
     */
    public function actionGetTitleType(){
        $response = $groups_v  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['groupid']) && $params['groupid'] != ''){
            $list=$this->titletype($params['groupid']);
            $response["status"] = 1;
            $response["error"] = false;
            $response['data'] = $list;
            $response['message']=Yii::t('db','Success');
        }

        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Group id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for getting list of treatments by speciality
     */
    public function actionGetTreatments(){
        $response = $arraytreat  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['speciality']) && $params['speciality'] != ''){
            $speciality=$params['speciality'];
            if(isset($params['user_id'])){
                $arraytreat=$this->treatment($speciality,$params['user_id']);
            }
            else{
                $arraytreat=$this->treatment($speciality);
            }
            $response["status"] = 1;
            $response["error"] = false;
            $response['data'] = $arraytreat;
            $response['message']=Yii::t('common','Success');
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Speciality required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for sending otp
     */
    public function actionSendOtp(){
        $fields=ApiFields::sendOtpFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            if(!isset($params['countrycode'])){
                $params['countrycode']='91';
            }
            $checkUser=DrsPanel::sendOtpStep($params['mobile'],$params['countrycode'],$params['groupid']);
            if(!empty($checkUser)){
                if($checkUser['type'] == 'error'){
                    $response["status"] = 0;
                    $response["error"] = true;
                    if(!empty($checkUser['data'])){
                        $response["data"]=$checkUser['data'];
                    }
                    $response['message'] = $checkUser['message'];
                }
                else{
                    $checkUser['groupid']=$params['groupid'];
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response["data"]=$checkUser;
                    $response['message'] = 'Otp sended on number entered by you, please verfiy it';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Please try again!';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for verify otp
     */
    public function actionVerifyOtp(){
        $fields=ApiFields::verifyotpFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }

        if(empty($required)) {
            if(!isset($params['countrycode'])){
                $params['countrycode']='91';
            }
            $mobile=$params['mobile']; $otp=$params['otp'];
            $userType=$params['groupid'];
            $countrycode=$params['countrycode'];
            $token=($params['token'])?$params['token']:'';
            $checkUser=DrsPanel::verifyOtpStep($mobile,$countrycode,$otp,$userType,$token);
            if($checkUser['type'] == 'success'){
                $response["status"] = 1;
                $response["error"] = false;
                if($checkUser['userType'] == 'old'){
                    $response["data"]=$checkUser['data'];
                    $response["data"]['login_status']=$checkUser['userType'];
                }
                else{
                    $response["data"]['mobile']=$checkUser['mobile'];
                    $response["data"]['countrycode']=$checkUser['countrycode'];
                    $response["data"]['login_status']=$checkUser['userType'];
                }
                $response['message'] = 'Success';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                if(!empty($checkUser['data'])){
                    $response["data"]=$checkUser['data'];
                }
                $response['message'] = $checkUser['message'];
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for signup
     */
    public function actionSignup(){
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        $response = $data =  $required = array();

        if(isset($params['groupid'])){
            $userType=$params['groupid'];
            if($userType == Groups::GROUP_PATIENT || $userType == Groups::GROUP_DOCTOR){
                $fields=ApiFields::signupFields();
            }
            else{
                $fields=ApiFields::signupFieldsHospital();
            }
            foreach($fields as  $field){
                if (array_key_exists($field,$params)){}
                    else{ $required[]=$field;}
            }
            if(empty($required)){
                $params['countrycode']=(isset($params['countrycode']))?$params['countrycode']:'91';
                $checkEmail = User::getEmailExist($params['email']);
                if ($checkEmail > 0) {
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response["type"]='Email_Already_Registered';
                    $response['message'] = Yii::t('common', 'This email address has already been taken.');
                } else {
                    $checkVerification=DrsPanel::checkOtpVerified($params['mobile'],$params['countrycode'],$params['groupid']);
                    if($checkVerification['type'] == 'success'){
                        if($params['groupid'] == Groups::GROUP_DOCTOR){
                            $model = new DoctorForm();
                            $modelLabel='DoctorForm';
                        }
                        elseif($params['groupid'] == Groups::GROUP_PATIENT){
                            $model = new PatientForm();
                            $modelLabel='PatientForm';
                        }
                        else{
                            $model = new HospitalForm();
                            $modelLabel='HospitalForm';
                        }
                        $model->groupid = $params['groupid'];
                        $model->name = $params['name'];
                        $model->email = $params['email'];
                        $model->countrycode = $params['countrycode'];
                        $model->phone = $params['mobile'];
                        $model->token=$params['token'];
                        $model->device_id=$params['device_id'];
                        $model->device_type=$params['device_type'];
                        if($userType == Groups::GROUP_PATIENT || $userType == Groups::GROUP_DOCTOR){
                            $model->dob = $params['dob'];
                            $model->gender=$params['gender'];
                        }
                        if($model->validate()){
                            $resp = $model->signup();
                            if ($resp == "ERROR") {
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = Yii::t('common', 'Mandatory fields are required.');
                            } elseif ($resp == "EMAIL_NOT_VALID") {
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = Yii::t('common', 'Email id is not valid.');
                            } elseif ($resp == "USER_NOT_SAVE") {
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = Yii::t('common', 'User couldn\'t be  saved');
                            } else {
                                $user_id = $resp->getId(); 
                                $user=User::findOne(['id'=>$user_id]);
                                if(!empty($user)){
                                    if (isset($_FILES['image'])){ 
                                        $imageUpload=DrsImageUpload::updateProfileImageApp($user->id,$_FILES);
                                    }
                                    $user->mobile_verified=1;
                                    $user->save();
                                    $profile = UserProfile::findOne(['user_id' => $user->id]);
                                    $data_array=DrsPanel::profiledetails($user,$profile,$user->groupid);
                                    $response["status"] = 1;
                                    $response["error"] = false;
                                    $response['data'] = $data_array;
                                    if($params['groupid'] == Groups::GROUP_DOCTOR){
                                        $response['message'] = Yii::t('common', 'Your account has been successfully created. Please update your profile & request for live');
                                    }
                                    else{
                                        $response['message'] = Yii::t('common', 'Your account has been successfully created.');
                                    }
                                }
                                else{
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["message"] = 'Please try again';
                                }
                            }
                        }
                        else{
                            $response=DrsPanel::validationErrorMessage($model->getErrors());
                        }

                    }
                    elseif($checkVerification['type'] == 'verification_error'){
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response["type"]='Mobile_Invalid';
                        $response['message'] = Yii::t('common', 'Mobile number not verified');
                    }
                    elseif($checkVerification['type'] == 'already_registered'){
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response["type"]='Mobile_Already_Registered';
                        $response['message'] = Yii::t('common', 'Mobile number already registered');
                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response["type"]='Invalid';
                        $response['message'] = Yii::t('common', 'Please try again');
                    }
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $fields_req= implode(',',$required);
                $response['message'] = $fields_req.' required';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'User Type group Required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for getuser profile status
     */
    public function actionGetProfileStatus(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                $profile = UserProfile::findOne(['user_id' => $user->id]);
                $response["status"] = 1;
                $response["error"] = false;
                $response['profile_status']=$user->admin_status;
                $response['message']='Profile Status';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for getuser profile details
     */
    public function actionGetProfile(){
        $response = $data  = $arraytreat = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                $profile = UserProfile::findOne(['user_id' => $user->id]);
                $data_array=DrsPanel::profiledetails($user,$profile,$user->groupid);
                $response["status"] = 1;
                $response["error"] = false;
                $response['profile'] = $data_array;
                $response['service_charge'] = DrsPanel::getMetaData('service_charge');
                $response['blood_group']=DrsPanel::getMetaData('blood_group');
                $response['degree']=DrsPanel::getMetaData('degree');
                $response['speciality']=DrsPanel::getMetaData('speciality');
                if($user->groupid==Groups::GROUP_PATIENT){
                    $lists=DrsPanel::getPatientAppointments($params['user_id'],'upcoming');
                    $lists->all();
                    $command = $lists->createCommand();
                    $lists = $command->queryAll();
                    $list_a=DrsPanel::getPatientAppointmentsList($lists);
                    $upcoming = array_values($list_a);
                    $response['upcoming_appointments']=(!empty($upcoming))?$upcoming:'';
                }
                if($user->groupid==Groups::GROUP_HOSPITAL){
                    $response['services']=DrsPanel::getMetaData('services',$params['user_id']);
                    $response['address_type']=DrsPanel::getMetaData('address_type');
                }
                if(!empty($profile->speciality)){
                    $arraytreat = $this->treatment($profile->speciality,$params['user_id']);
                    $response['treatment']= $arraytreat;
                }
                else{
                    $response['treatment']=[];
                }
                $response['title_type']=$this->titletype($user->groupid);

                $response['message']='Profile Data';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for edit doctor profile details
     */
    public function actionEditDoctorProfile(){
        $fields=ApiFields::editDoctorFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){

            if (!in_array(null, $params)) {
                if(!empty($params['user_id'])){
                    $user_id=$params['user_id'];
                    $user = User::findOne(['id' => $user_id]);
                    $profile = UserProfile::findOne(['user_id' => $user_id]);
                    if(!empty($profile)){
                        $groupid =$profile->groupid;
                        $params['countrycode']=(isset($params['countrycode']))?$params['countrycode']:'91';
                        $checkMobileUpdate=DrsPanel::checkmobileUpdate($user_id,$params['countrycode'],$params['mobile'],$groupid);
                        if($checkMobileUpdate['type'] == 'error'){
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message'] = $checkMobileUpdate['message'];
                        }
                        else{
                            $checkEmail=DrsPanel::checkemailUpdate($user_id,$params['email']);
                            if($checkEmail['type'] == 'error'){
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = $checkEmail['message'];
                            }
                            else{
                                $user->email=$params['email'];
                                $user->countrycode=$params['countrycode'];
                                $user->phone=$params['mobile'];

                                if (isset($_FILES['image'])){
                                    $imageUpload=DrsImageUpload::updateProfileImageApp($user->id,$_FILES);
                                }
                                if($user->save()){
                                    $profile->name=$params['name'];
                                    $profile->email=$params['email'];
                                    $profile->dob=$params['dob'];
                                    $profile->gender=$params['gender'];
                                    if(isset($params['blood_group']))
                                    {
                                        $profile->blood_group=$params['blood_group'];
                                    }

                                    $profile->experience=$params['experience'];
                                    $profile->description=$params['description'];

                                    $degrees=json_decode($params['degree']);
                                    $specialities=json_decode($params['speciality']);

                                    if(is_array($degrees) &&!empty($degrees)){
                                        $profile->degree=implode(',',$degrees);
                                    }
                                    if(is_array($specialities) && !empty($specialities)){
                                        $profile->speciality=implode(',',$specialities);
                                    }

                                    if(isset($params['treatment'])){
                                        $treatment=json_decode($params['treatment']);
                                        if(is_array($treatment) && !empty($treatment)){
                                            $profile->treatment=implode(',',$treatment);
                                        }
                                    }

                                    if(isset($params['services'])){
                                        $services=json_decode($params['services']);
                                        if(is_array($services) && !empty($services)){
                                            $profile->services=implode(',',$services);
                                        }
                                    }

                                    if($profile->save()){
                                        if($checkMobileUpdate['message'] == 'new'){
                                            $user->otp='1234';
                                            $user->mobile_verified=0;
                                            if($user->save()){
                                                $response["status"] = 1;
                                                $response["error"] = false;
                                                $response["otp_alert"]=1;
                                                $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);
                                                $response['message']='Profile saved & otp sended';
                                            }
                                            else{
                                                $response=DrsPanel::validationErrorMessage($user->getErrors());
                                            }
                                        }
                                        else{
                                            $getResponseMessage=DrsPanel::getDoctorProfileMsg($profile->user_id);
                                            $response["status"] = 1;
                                            $response["error"] = false;
                                            $response["otp_alert"]=0;
                                            $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);
                                            $response['message']=$getResponseMessage;

                                        }
                                    }
                                    else{
                                        $response=DrsPanel::validationErrorMessage($profile->getErrors());
                                    }
                                }
                                else{
                                    $response=DrsPanel::validationErrorMessage($user->getErrors());
                                }
                            }
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = Yii::t('db', 'User not found');
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = Yii::t('db', 'UserId required');
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = Yii::t('db', 'Mandatory fields are required.');
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for add/edit doctor education details
     */
    public function actionUpsertSpecialityTreatment(){
        $fields=ApiFields::specialityTreatment();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();

      

        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $user_id=$params['user_id'];
            $userProfile=UserProfile::findOne($user_id);
            $metakey=MetaKeys::findOne(['key'=>'Treatment']);

            $metakey_speciality=MetaKeys::findOne(['key'=>'speciality']);

            if(!empty($userProfile)){
                $Userspecialities=$params['speciality'];

                $getSpecilaity= MetaValues::find()->where(['key'=>$metakey_speciality->id,'value'=>$Userspecialities])->one();

                $Usertreatments=$params['treatment'];
                if(isset($Userspecialities) && isset($Usertreatments)){
                    $Usertreatments=json_decode($params['treatment']);
                        if(!empty($Usertreatments)){
                            $existing_services=array();
                            $new_services=array();
                            foreach($Usertreatments as $Usertreatment){
                                if(isset($Usertreatment->id)){
                                    $existing_services[]=$Usertreatment->value;
                                }
                                else{
                                    $new_services[]=$Usertreatment->value;

                                    $checkValue=MetaValues::find()->where(['key'=>$metakey->id,'value'=>$Usertreatment->value])->one();
                                    if(empty($checkValue)){
                                        $model = new MetaValues();
                                        if(!empty($metakey)){
                                            if(!empty($getSpecilaity)){
                                                $model->parent_key=$getSpecilaity->id;
                                            }
                                            $model->key=$metakey->id;
                                            $model->label=$Usertreatment->label;
                                            $model->value=$Usertreatment->value;
                                            $model->status=0;
                                            $model->save();
                                        }
                                    }
                                }
                            }
                            $treatments=array_merge($existing_services,$new_services);
                            $userProfile->speciality=$Userspecialities;
                            $userProfile->treatment=implode(',',$treatments);
                        }
                        else{
                            $userProfile->speciality=$Userspecialities;
                            $userProfile->treatment='';
                        }

                    if($userProfile->save()){
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response['message'] = 'Speciality & treatment updated';
                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response["data"]=DrsPanel::validationErrorMessage($userProfile->getErrors());
                        $response['message'] = 'Please try again';
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = 'Please select speciality & treatment';
                }

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'User not found.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for get doctor education list
     */
    public function actionEducationList(){
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);
        if(isset($post['doctor_id']) && !empty($post['doctor_id'])){
            $list=DrsPanel::getDoctorEducation($post['doctor_id']);
            if($list){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= DrsPanel::listEducation($list);
            }else{
                $response["status"] = 1;
                $response["error"] = false;
                $response['message'] = 'You have not any education.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for add/edit doctor education details
     */
    public function actionUpsertEducation(){
        $fields=ApiFields::doctorEduction();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $model=DrsPanel::upsertEducation($post);
            if($model){
                if($model->getErrors()){
                    $response=DrsPanel::validationErrorMessage($model->getErrors());

                }else{
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['message']= 'Success';
                    $response['data']= $model;
                }

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Edcuation not saved.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for get doctor experience list
     */
    public function actionExperienceList(){
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);
        if(isset($post['doctor_id']) && !empty($post['doctor_id'])){
            $list=DrsPanel::getDoctorExperience($post['doctor_id']);
            if($list){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= DrsPanel::listExperience($list);
            }else{
                $response["status"] = 1;
                $response["error"] = false;
                $response['message'] = 'You have not any experience.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for add/edit doctor experience details
     */
    public function actionUpsertExperience(){
        $fields=ApiFields::doctorExperience();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $startyears=$post['start'];
            $model=DrsPanel::upsertExperience($post);
            if($model){
                if($model->getErrors()){
                    $response=DrsPanel::validationErrorMessage($model->getErrors());

                }else{
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['message']= 'Success';
                    $response['data']= $model;
                }

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Experience not saved.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for edit hospital profile details
     */
    public function actionEditHospitalProfile(){
        $fields=ApiFields::editHospitalFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){

            if (!in_array(null, $params)) {
                if(!empty($params['user_id'])){
                    $user_id=$params['user_id'];
                    $user = User::findOne(['id' => $user_id]);
                    $profile = UserProfile::findOne(['user_id' => $user_id]);
                    if(!empty($profile)){
                        $groupid =$profile->groupid;
                        $params['countrycode']=(isset($params['countrycode']))?$params['countrycode']:'91';
                        $checkMobileUpdate=DrsPanel::checkmobileUpdate($user_id,$params['countrycode'],$params['mobile'],$groupid);
                        if($checkMobileUpdate['type'] == 'error'){
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message'] = $checkMobileUpdate['message'];
                        }
                        else{
                            $checkEmail=DrsPanel::checkemailUpdate($user_id,$params['email']);
                            if($checkEmail['type'] == 'error'){
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = $checkEmail['message'];
                            }
                            else{
                                $user->email=$params['email'];
                                $user->countrycode=$params['countrycode'];
                                $user->phone=$params['mobile'];

                                if (isset($_FILES['image'])){
                                    $imageUpload=DrsImageUpload::updateProfileImageApp($user->id,$_FILES);
                                }
                                if($user->save()){
                                    $profile->name=$params['name'];
                                    $profile->email=$params['email'];
                                    $profile->gender=0;
                                    if($profile->save()){
                                        if($checkMobileUpdate['message'] == 'new'){
                                            $user->otp='1234';
                                            $user->mobile_verified=0;
                                            if($user->save()){
                                                $response["status"] = 1;
                                                $response["error"] = false;
                                                $response["otp_alert"]=1;
                                                $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);
                                                $response['message']='Profile saved & otp sended';
                                            }
                                            else{
                                                $response=DrsPanel::validationErrorMessage($user->getErrors());
                                            }
                                        }
                                        else{
                                            $getResponseMessage=DrsPanel::getDoctorProfileMsg($profile->user_id);
                                            $response["status"] = 1;
                                            $response["error"] = false;
                                            $response["otp_alert"]=0;
                                            $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);
                                            $response['message']=$getResponseMessage;

                                        }
                                    }
                                    else{
                                        $response=DrsPanel::validationErrorMessage($profile->getErrors());
                                    }
                                }
                                else{
                                    $response=DrsPanel::validationErrorMessage($user->getErrors());
                                }
                            }
                        }
                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = Yii::t('db', 'User not found');
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = Yii::t('db', 'UserId required');
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = Yii::t('db', 'Mandatory fields are required.');
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for edit patient profile details
    */
    public function actionEditPatientProfile(){
        $fields=ApiFields::editPatientFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){

            if (!in_array(null, $params)) {
                if(!empty($params['user_id'])){
                    $user_id=$params['user_id'];
                    $user = User::findOne(['id' => $user_id]);
                    $profile = UserProfile::findOne(['user_id' => $user_id]);
                    if(!empty($profile)){
                        $groupid =$profile->groupid;
                        $params['countrycode']=(isset($params['countrycode']))?$params['countrycode']:'91';
                        $checkMobileUpdate=DrsPanel::checkmobileUpdate($user_id,$params['countrycode'],$params['mobile'],$groupid);
                        if($checkMobileUpdate['type'] == 'error'){
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message'] = $checkMobileUpdate['message'];
                        }
                        else{
                            $checkEmail=DrsPanel::checkemailUpdate($user_id,$params['email']);
                            if($checkEmail['type'] == 'error'){
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message'] = $checkEmail['message'];
                            }
                            else{
                                if (isset($_FILES['image'])){
                                    $imageUpload=DrsImageUpload::updateProfileImageApp($user->id,$_FILES);
                                }
                                $user->username=$params['email'];
                                $user->email=$params['email'];
                                $user->countrycode=$params['countrycode'];
                                $user->phone=$params['mobile'];
                                if($user->save()){
                                    $profile->name=$params['name'];
                                    $profile->email=$params['email'];
                                    $profile->dob=$params['dob'];
                                    $profile->gender=(int)$params['gender'];
                                    $profile->blood_group=isset($params['blood_group'])?$params['blood_group']:'';
                                    $profile->weight=isset($params['weight'])?$params['weight']:0;
                                    $height=array('feet'=>isset($params['height_feet'])?$params['height_feet']:0,'inch'=>isset($params['height_inch'])?$params['height_inch']:0);
                                    $profile->height=json_encode($height);
                                    $profile->marital=isset($params['marital'])?$params['marital']:'';
                                    $profile->location=isset($params['location'])?$params['location']:'';
                                    if($profile->save()){
                                        if($checkMobileUpdate['message'] == 'new'){
                                            $user->otp='1234';
                                            $user->mobile_verified=0;
                                            if($user->save()){
                                                $response["status"] = 1;
                                                $response["error"] = false;
                                                $response["otp_alert"]=1;
                                                $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);
                                                $response['message']='Profile saved & otp sended';
                                            }
                                            else{
                                                $response=DrsPanel::validationErrorMessage($user->getErrors());
                                            }
                                        }
                                        else{
                                            $response["status"] = 1;
                                            $response["error"] = false;
                                            $response["otp_alert"]=0;
                                            $response['data']=DrsPanel::profiledetails($user,$profile,$groupid);

                                            $response['message']='Profile saved successfully';

                                        }
                                    }
                                    else{
                                        $response=DrsPanel::validationErrorMessage($profile->getErrors());
                                    }
                                }
                                else{
                                    $response=DrsPanel::validationErrorMessage($user->getErrors());
                                }
                            }
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = Yii::t('db', 'User not found');
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = Yii::t('db', 'UserId required');
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = Yii::t('db', 'Mandatory fields are required.');
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
   * @Param Null
   * @Function is used for add address
   */
    public function actionAddNewAddress(){
        $fields=ApiFields::addressFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
        }
        if(empty($required)) {
            if(isset($params['user_id']) && $params['user_id'] != ''){
                $user=User::findOne(['id'=>$params['user_id']]);
                if(!empty($user)){
                    $addAddress=new UserAddress();
                    $data['UserAddress']['user_id']=$params['user_id'];
                    $data['UserAddress']['type']=$params['type'];
                    $data['UserAddress']['name']=$params['name'];
                    $data['UserAddress']['address']=$params['name'];
                    if(isset($params['address'])){
                        $data['UserAddress']['address_2']=$params['address'];
                    }
                    $data['UserAddress']['city']=$params['city'];
                    $data['UserAddress']['state']=$params['state'];
                    $data['UserAddress']['zipcode']=$params['zipcode'];
                    $data['UserAddress']['country']=$params['country'];
                    $data['UserAddress']['phone']=$params['mobile'];
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);
                    if($addAddress->save()){
                        $imageUpload='';
                        if (isset($_FILES['image'])){
                            $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                        }
                        if (isset($_FILES['images'])){
                            $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'images');
                        }
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"] = $imageUpload;
                        $response['message']= 'Address added successfully';
                    }
                    else{
                        $response=DrsPanel::validationErrorMessage($addAddress->getErrors());
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'User not found';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'UserId Required';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
   * @Param Null
   * @Function is used for update address
   */
    public function actionUpdateAddress(){
        $fields=ApiFields::updateaddressFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
        }
        if(empty($required)) {
            $addAddress=UserAddress::findOne(['id'=>$params['address_id']]);
            if(!empty($addAddress)){
                $data['UserAddress']['type']=$params['type'];
                $data['UserAddress']['name']=$params['name'];
                $data['UserAddress']['address']=$params['name'];
                if(isset($params['address'])){
                    $data['UserAddress']['address_2']=$params['address'];
                }
                $data['UserAddress']['city']=$params['city'];
                $data['UserAddress']['state']=$params['state'];
                $data['UserAddress']['zipcode']=$params['zipcode'];
                $data['UserAddress']['country']=$params['country'];
                $data['UserAddress']['phone']=$params['mobile'];
                $addAddress->load($data);
                if($addAddress->save()){
                    $imageUpload='';
                    if (isset($_FILES['image'])){
                        $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                    }

                    if (isset($params['deletedImages'])){
                        $images=json_decode($params['deletedImages']);
                        foreach($images as $image){
                            $address_file=UserAddressImages::findOne($image);
                            $address_file->delete();
                        }
                    }

                    if (isset($_FILES['images'])){
                        $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'images');
                    }

                    $response["status"] = 1;
                    $response["error"] = false;
                    // $response["data"] = $imageUpload;
                    $response['message']= 'Success';
                }
                else{
                    $response=DrsPanel::validationErrorMessage($addAddress->getErrors());
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Address not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for add/edit profile service details
    */
    public function actionAddUpdateServices(){
        $fields=ApiFields::addupdateServices();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $user_id=$post['user_id'];
            $profile=UserProfile::findOne($post['user_id']);
            if(!empty($profile)){
                $metakey=MetaKeys::findOne(['key'=>'Services']);

                if(isset($post['services'])){
                    $services=json_decode($post['services']);
                    if(!empty($services)){
                        $existing_services=array();
                        $new_services=array();
                        foreach($services as $service){
                            if(isset($service->id)){
                                $existing_services[]=$service->value;
                            }
                            else{
                                $new_services[]=$service->value;

                                $checkValue=MetaValues::find()->where(['key'=>$metakey->id,'value'=>$service->value])->one();
                                if(empty($checkValue)){
                                    $model = new MetaValues();
                                    if(!empty($metakey)){
                                        $model->key=$metakey->id;
                                        $model->label=$service->label;
                                        $model->value=$service->value;
                                        $model->status=0;
                                        $model->save();
                                    }
                                }
                            }
                        }
                        $services=array_merge($existing_services,$new_services);
                        if(!empty($services)){
                            $val_serv=implode(',',$services);
                            $profile->services=$val_serv;
                        }
                        else{
                            $profile->services='';
                        }

                    }
                    else{
                        $profile->services='';
                    }
                }
                if($profile->save()){
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['message']= 'Services/Facilities updated';
                    $response['services']=DrsPanel::getMetaData('services',$user_id);
                    $response['data']= $profile;
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = 'Services/Facilities not saved.';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'User not found.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
     * @Param Null
     * @Function is used for add/edit profile description details
     */
    public function actionAddUpdateAboutus(){
        $fields=ApiFields::addupdateAboutus();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $profile=UserProfile::findOne($post['user_id']);
            $useraboutus=UserAboutus::find()->where(['user_id'=>$post['user_id']])->one();
            if(empty($useraboutus)){
               $useraboutus= new UserAboutus();
           }
           $useraboutus->user_id=$post['user_id'];
           $useraboutus->description=$post['description'];
           if(isset($post['vision']) & !empty($post['vision'])){
            $useraboutus->vision=$post['vision'];
        }
        else{
            $useraboutus->vision='';
        }

        if(isset($post['mission']) & !empty($post['mission'])){
            $useraboutus->mission=$post['mission'];
        }
        else{
            $useraboutus->mission='';
        }

        if(isset($post['timing']) & !empty($post['timing'])){
            $useraboutus->timing=$post['timing'];
        }
        else{
            $useraboutus->timing='';
        }
        if($useraboutus->save()){
            $response["status"] = 1;
            $response["error"] = false;
            $response['message']= 'About us updated';
            $response['data']= $profile;
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'About us not saved.';
        }
    }else{
        $response["status"] = 0;
        $response["error"] = true;
        $fields_req= implode(',',$required);
        $response['message'] = $fields_req.' required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

    public function actionAddShift(){
        $fields=ApiFields::addShiftFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }

        if(empty($required)){
            if(isset($params['user_id']) && $params['user_id'] != ''){
                $user=User::findOne(['id'=>$params['user_id']]);
                if(!empty($user)){
                    $addAddress=new UserAddress();
                    $data['UserAddress']['user_id']=$params['user_id'];
                    $data['UserAddress']['name']=$params['name'];
                    $data['UserAddress']['city']=$params['city'];
                    $data['UserAddress']['state']=$params['state'];
                    $data['UserAddress']['address']=$params['address'];
                    $data['UserAddress']['area']=$params['area'];
                    $data['UserAddress']['phone']=$params['mobile'];
                    $data['UserAddress']['landline']=isset($params['landline'])?$params['landline']:'';
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);
                    if($addAddress->save()){
                        $imageUpload='';
                        if (isset($_FILES['image'])){
                            $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                        }
                        if (isset($_FILES['images'])){
                            $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'images');
                        }

                        $shift=array();
                        $address_id=$addAddress->id;
                        if((isset($params['shiftList']) && !empty($params['shiftList']))){
                            $shiftList=json_decode($params['shiftList']);
                            foreach($shiftList as $shift_v){
                                $weekArray=explode(',',$shift_v->weekday);
                                //foreach($weekArray as $weekDay){
                                $shift=array();
                                $shift['AddScheduleForm']['weekday']=$weekArray;
                                $shift['AddScheduleForm']['address_id']=$address_id;
                                $shift['AddScheduleForm']['user_id']=$params['user_id'];
                                $shift['AddScheduleForm']['start_time']=$shift_v->start_time;
                                $shift['AddScheduleForm']['end_time']=$shift_v->end_time;
                                $shift['AddScheduleForm']['appointment_time_duration']=$shift_v->appointment_time_duration;

                                $shift['AddScheduleForm']['patient_limit']=$shift_v->patient_limit;

                                $shift['AddScheduleForm']['consultation_fees']=(isset($shift_v->consultation_fees) && ($shift_v->consultation_fees > 0) )?$shift_v->consultation_fees:0;
                                $shift['AddScheduleForm']['emergency_fees']=(isset($shift_v->emergency_fees) && ($shift_v->emergency_fees > 0) )?$shift_v->emergency_fees:0;
                                $shift['AddScheduleForm']['consultation_fees_discount']=(isset($shift_v->consultation_fees_discount) && ($shift_v->consultation_fees_discount > 0) )?$shift_v->consultation_fees:0;
                                $shift['AddScheduleForm']['emergency_fees_discount']=(isset($shift_v->emergency_fees_discount) && ($shift_v->emergency_fees_discount > 0) )?$shift_v->emergency_fees_discount:0;

                                $addUpdateShift=DrsPanel::upsertShift($shift,0,$address_id);
                               // }

                            }

                        }
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"] = $addUpdateShift;
                        $response['message']= 'Shift added successfully';
                    }
                    else{
                        $response=DrsPanel::validationErrorMessage($addAddress->getErrors());
                    }
                }
            }
        }
        else {
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionEditShiftWithCancelAppointment(){
        $response = $data =  array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && $params['user_id'] != '')
        {
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user))
            {
                $shifts_for_cancel=json_decode($params['shifts_for_cancel']);
                foreach ($shifts_for_cancel as $key => $value) {

                    $userAppointment = UserSchedule::findOne($value->id);

                    if(!empty($userAppointment)){
                        $appointment = UserAppointment::find()->where(['doctor_id' => $value->user_id,'schedule_id' => $value->id])->all();

                        $query = UserAppointment::updateAll(['status' => UserAppointment::STATUS_CANCELLED],['doctor_id' => $value->user_id,'schedule_id' => $value->id]);

                        $userAppointment->load(['UserSchedule' =>$value]);
                        $userAppointment->save();
                    }

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response["data"] = [];
                    $response['message']= 'Shift cancelled Successfully';



                }

            }

        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function saveShiftData($day_shift,$newshiftInsert,$params){
        $shift=array();
        $shift['AddScheduleForm']['weekday']=$day_shift->weekday;
        $shift['AddScheduleForm']['user_id']=$params['user_id'];
        if(isset($newshiftInsert->id))
        {
            $shift['AddScheduleForm']['id']=$newshiftInsert->id;
        }
        $shift['AddScheduleForm']['start_time']=$newshiftInsert->start_time;
        $shift['AddScheduleForm']['end_time']=$newshiftInsert->end_time;
        $shift['AddScheduleForm']['appointment_time_duration']=$newshiftInsert->appointment_time_duration;

        $shift['AddScheduleForm']['patient_limit']=$newshiftInsert->patient_limit;

        $shift['AddScheduleForm']['consultation_fees']=(isset($newshiftInsert->consultation_fees) && ($newshiftInsert->consultation_fees > 0) )?$newshiftInsert->consultation_fees:0;
        $shift['AddScheduleForm']['emergency_fees']=(isset($newshiftInsert->emergency_fees) && ($newshiftInsert->emergency_fees > 0) )?$newshiftInsert->emergency_fees:0;
        $shift['AddScheduleForm']['consultation_fees_discount']=(isset($newshiftInsert->consultation_fees_discount) && ($newshiftInsert->consultation_fees_discount > 0) )?$newshiftInsert->consultation_fees_discount:0;
        $shift['AddScheduleForm']['emergency_fees_discount']=(isset($newshiftInsert->emergency_fees_discount) && ($newshiftInsert->emergency_fees_discount > 0) )?$newshiftInsert->emergency_fees_discount:0;
        return $shift;

    }

    public function actionEditShift(){
        $fields=ApiFields::addShiftFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();

        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }

        if(empty($required)){
            if(isset($params['user_id']) && $params['user_id'] != ''){
                $user=User::findOne(['id'=>$params['user_id']]);
                if(!empty($user)){
                    $addAddress=UserAddress::findOne(['id' => $params['address_id']]);
                    $data['UserAddress']['user_id']=$params['user_id'];
                    $data['UserAddress']['name']=$params['name'];
                    $data['UserAddress']['city']=$params['city'];
                    $data['UserAddress']['state']=$params['state'];
                    $data['UserAddress']['address']=$params['address'];
                    $data['UserAddress']['area']=$params['area'];
                    $data['UserAddress']['phone']=$params['mobile'];
                    $data['UserAddress']['landline']=isset($params['landline'])?$params['landline']:'';
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);

                    if($addAddress->save()){

                        $imageUpload='';
                        if (isset($_FILES['images'])){
                            $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                        }
                       /* if (isset($_FILES['images'])){
                            $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES);
                        }*/

                        $shift=array();
                        $address_id=$addAddress->id;

                        if((isset($params['shiftList']) && !empty($params['shiftList']))){
                            $shiftList=json_decode($params['shiftList']);
                            // echo '<pre>';
                            // print_r($shiftList);
                            // die;

                            foreach($shiftList as $shift_v){
                                $weekArray=explode(',',$shift_v->weekday);
                                //foreach($weekArray as $weekDay){
                                $shift=array();
                                $shift['AddScheduleForm']['weekday']=$weekArray;
                                $shift['AddScheduleForm']['address_id']=$address_id;
                                $shift['AddScheduleForm']['user_id']=$params['user_id'];
                                $shift['AddScheduleForm']['start_time']=$shift_v->start_time;
                                $shift['AddScheduleForm']['end_time']=$shift_v->end_time;
                                $shift['AddScheduleForm']['appointment_time_duration']=$shift_v->appointment_time_duration;

                                $shift['AddScheduleForm']['patient_limit']=$shift_v->patient_limit;

                                $shift['AddScheduleForm']['consultation_fees']=(isset($shift_v->consultation_fees) && ($shift_v->consultation_fees > 0) )?$shift_v->consultation_fees:0;
                                $shift['AddScheduleForm']['emergency_fees']=(isset($shift_v->emergency_fees) && ($shift_v->emergency_fees > 0) )?$shift_v->emergency_fees:0;
                                $shift['AddScheduleForm']['consultation_fees_discount']=(isset($shift_v->consultation_fees_discount) && ($shift_v->consultation_fees_discount > 0) )?$shift_v->consultation_fees:0;
                                $shift['AddScheduleForm']['emergency_fees_discount']=(isset($shift_v->emergency_fees_discount) && ($shift_v->emergency_fees_discount > 0) )?$shift_v->emergency_fees_discount:0;
                                $addUpdateShift=DrsPanel::upsertShift($shift,0,$address_id);
                               // }




                            }

                        }

                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"] = $addUpdateShift;
                        $response['message']= 'Shift Edit successfully';
                    }
                    else{
                        $response=DrsPanel::validationErrorMessage($addAddress->getErrors());
                    }
                }
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for getting doctor my Shifts list
    */
    public function actionGetMyShifts(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            $shift_array=array();
            if(!empty($user)){
                $address_list=DrsPanel::doctorHospitalList($params['user_id']);
                $address_list=(!empty($address_list))?$address_list['apiList']:[];
                foreach($address_list as $key=>$list) {
                    $shift_array['address'][$key]=$list;
                    $shifts=DrsPanel::getShiftListByAddress($params['user_id'],$list['id']);
                    $shift_array['address'][$key]['shifts']=$shifts;
                }
                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $shift_array;
                $response['message']='Address & Shift List';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
  * @Param Null
  * @Function is used for delete shift
  */
    public function actionDeleteShift(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['address_id']) && !empty($params['address_id'])){
            if(isset($params['shifts_id']) && !empty($params['shifts_id'])){
                $deleteShift=DrsPanel::deleteShift($params['user_id'],$params['address_id'],$params['shifts_id']);
                if($deleteShift['type'] == 'error'){
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = $deleteShift['message'];
                }
                else{
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['message']='Shift Deleted';

                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Missing Required Fields';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for get shift details week day wise
    */
    public function actionGetShiftsDetail(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                if(isset($params['weekday']) && $params['weekday'] != ''){
                    $weeks=DrsPanel::getWeekArray();
                    if(in_array($params['weekday'],$weeks)){
                        $data_array=DrsPanel::getAllShiftDetail($params['user_id'],$params['weekday']);
                    }
                    else{
                        $data_array=DrsPanel::getAllShiftDetail($params['user_id']);
                    }
                }
                else{
                    $data_array=DrsPanel::getAllShiftDetail($params['user_id']);
                }
                $hospital=DrsPanel::doctorHospitalList($params['user_id']);
                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $data_array;
                $response['weekday'] = date('l',strtotime(date('Y-m-d')));
                $response['date'] = date('Y-m-d');
                $response['address_list']=(count($hospital['apiList'])>0)?$hospital['apiList']:[];
                $response['message']='Shift Details';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for add or update shifts week wise
    */
    public static function actionUpsertShift(){
        $fields=ApiFields::doctorShiftUpsertFields();
        $response = $data  =$schedule= $required = array();
        $id=NULL;
        $post['AddScheduleForm'] = $params = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }

        Yii::info($post, __METHOD__);
        $model= new AddScheduleForm();

        if(empty($required)) 
        {
            if(isset($params['shift_id']) ){
                $id=$params['shift_id'];
                $schedule_id=$params['schedule_id'];
                $schedule=UserSchedule::findOne($schedule_id);
                if(empty($schedule)){
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = 'User schedule does not exits.';
                    Yii::info($response, __METHOD__);
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                else{
                    $model->id=$params['shift_id'];
                    $model->start_time=$params['start_time'];
                    $model->end_time=$params['end_time'];
                }
                $post['AddScheduleForm']['weekday']=array($schedule->weekday);
            }
            else{
                $post['AddScheduleForm']['weekday']=explode(',', $params['weekday']);
            }
            $post['AddScheduleForm']['consultation_fees']=(isset($params['consultation_fees']))?$params['consultation_fees']:0;
            $post['AddScheduleForm']['emergency_fees']=(isset($params['emergency_fees']))?$params['emergency_fees']:0;

            $post['AddScheduleForm']['consultation_fees_discount']=(isset($params['consultation_fees_discount']))?$params['consultation_fees_discount']:0;
            $post['AddScheduleForm']['emergency_fees_discount']=(isset($params['emergency_fees_discount']))?$params['emergency_fees_discount']:0;

            $post['AddScheduleForm']['id']=$id;

            $model->load($post);
            if($model){
                $addUpdateShift=DrsPanel::updateShiftTiming($id,$post,$schedule_id);
                if(isset($addUpdateShift['error']) && $addUpdateShift['error']==true)
                {
                    $response["status"] = 0;
                    $response["error"] = $addUpdateShift['error'];
                    $response["data"] = [];
                    $response['message']= $addUpdateShift['message'];
                    Yii::info($response, __METHOD__);
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                else 
                {
                    $response["status"] = 1;
                    $response["error"] = 0;
                    $response["data"] = [];
                    $response['message']= 'Shift Updated Successfully';
                }
            }
            else 
            {
                $response["status"] = 0;
                $response["error"] = true;
                $response["data"] = [];
                $response['message']= 'Something went wrong';
                Yii::info($response, __METHOD__);
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
        
    }

    /*
    * @Param Null
    * @Function is used for getting all shifts for particular date
    */
    public function actionGetBookingShifts(){
        $response = $datameta  = $required = $logindUser=array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $userLogin=User::find()->where(['id'=>$params['user_id']])->one();
            $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
            if(!empty($userLogin) && !empty($doctor)){
                if(isset($params['date']) && !empty($params['date'])){
                    $date= $params['date'];
                }else{
                    $date= date('Y-m-d');
                }
                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);

                $datameta['date']=$date;
                $datameta['week']=DrsPanel::getDateWeekDay($date);
                $datameta['shifts']=$getSlots;
                $datameta['address_list']=DrsPanel::getBookingAddressShifts($params['doctor_id'],$date, $params['user_id']);
                $response["status"] = 1;
                $response["error"] = false;
                $response["data"]=$datameta;
                $response['message'] = 'Success';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Something went wrong, Please try again.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id  and user id required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for getting all slots for particular shift
    */
    public function actionGetBookingShiftSlots(){
        $response = $data  = $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        if(isset($params['doctor_id']) && !empty($params['doctor_id'])) {
            $doctor=User::find()->where(['id'=>$params['doctor_id']])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->one();
            if($doctor){
                if(isset($params['schedule_id']) && !empty($params['schedule_id']) && isset($params['date']) && !empty($params['date'])){
                    $date=$params['date'];
                    $getSlots=DrsPanel::getBookingShiftSlots($params['doctor_id'],$date,$params['schedule_id'],'available');
                    if(!empty($getSlots)){
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"]=$getSlots;
                        $response['message'] = 'Success';
                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Shift completed or cancelled';
                    }

                }else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = 'Required parameter missing';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Doctor id is not registered';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for current appoint affair shift update(start or complete) for today
     */
    public function actionUpdateShift(){
        $fields=ApiFields::updateshift();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
                $userLogin=User::find()->where(['id'=>$params['user_id']])->one();
                $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
                if(!empty($userLogin) && !empty($doctor)){
                    $date = date('Y-m-d');
                    $shift = $params['schedule_id'];
                    $status = $params['status'];

                    if ($status == 'start') {
                        $schedule_check=UserScheduleGroup::find()->where(['user_id'=>$doctor->id,'date'=>$date, 'status' => array('pending','current')])->orderBy('shift asc')->one();
                        if (!empty($schedule_check)) {
                            if($schedule_check->schedule_id == $shift){
                                $schedule_check->status = 'current';
                                if ($schedule_check->save()) {

                                    DrsPanel::shiftStartNotification($params['doctor_id'],$schedule_id);
                                    $checkFirstAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>'active'])->orderBy('token asc')->one();
                                    if(empty($checkFirstAppointment)){
                                        $checkFirstAppointment=UserAppointment::find()->where(['doctor_id'=>$params['doctor_id'],'date'=>$date,'schedule_id'=>$shift,'status'=>'pending'])->orderBy('token asc')->one();
                                        if(!empty($checkFirstAppointment)){
                                            $checkFirstAppointment->status=UserAppointment::STATUS_ACTIVE;
                                            $checkFirstAppointment->save();
                                        }
                                    }
                                    $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                                    $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                                    if(!empty($checkForCurrentShift)){
                                        $response=$this->getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$shift,$getSlots);
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
                    } elseif ($status == 'completed') {
                        $schedule_check = UserScheduleGroup::find()->where(['user_id' => $params['doctor_id'], 'date' => $date, 'status' => 'current'])->one();
                        if (!empty($schedule_check)) {
                            $schedule_check->status = 'completed';
                            if ($schedule_check->save()) {
                                $date=date('Y-m-d');
                                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                                $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                                if(!empty($checkForCurrentShift)){
                                    $response=$this->getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$shift,$getSlots);
                                }

                            }
                        } else {
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message'] = 'Shift not found';
                        }
                    } else {
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Please try again.';
                    }

                } else {
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message'] = 'Something went wrong, Please try again.';
                }
            } else {
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Doctor id  and user id required';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for updating appointment by doctor
    */
    public function actionDoctorAppointmentUpdate(){
        $fields=ApiFields::doctorappointmentupdate();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $doctor_id=$params['doctor_id'];
            $user=User::findOne($doctor_id);
            if(!empty($user)){
                $doctorProfile=UserProfile::findOne(['user_id'=>$doctor_id]);
                $appointment=UserAppointment::findOne($params['appointment_id']);
                if(!empty($appointment)){
                    if($params['status'] == 'next'){
                        $appointment->status=UserAppointment::STATUS_COMPLETED;
                    }
                    elseif($params['status'] == 'skip'){
                        $appointment->status=UserAppointment::STATUS_SKIP;
                    }
                    elseif($params['status'] == 'notpaid'){
                        $appointment->payment_type='cash';
                        $appointment->status=UserAppointment::STATUS_COMPLETED;
                    }
                    else{

                    }
                    if($appointment->save()){
                        $date=date('Y-m-d');
                        /*$getAppointments=DrsPanel::getCurrentAppointmentsAffairs($doctor_id,$date,$params['schedule_id']);
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"]=$getAppointments;
                        $response['message']= 'Appointment updated successfully';*/
                        $addLog=Logs::appointmentLog($appointment->id,'Appointment status updated by doctor');

                        $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                        $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                        if(!empty($checkForCurrentShift)){
                            $response=$this->getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$params['schedule_id'],$getSlots);
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response["data"]=$appointment->getErrors();
                        $response['message']= 'Appointment not found';
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Appointment not found';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Doctor Details not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDoctorUpdateStatus(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $appointment_id=$params['appointment_id'];
        $status=$params['status'];
        $appointment=UserAppointment::findOne($appointment_id);
        if(!empty($appointment)){

            if($params['status'] == 'paid'){
                $appointment->status=UserAppointment::STATUS_AVAILABLE;
            }
            else{

            }
            if($appointment->save()){
                $date=date('Y-m-d');
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=DrsPanel::getappointmentarray($appointment);
                $response['message']= 'Appointment updated';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['data']=$appointment->getErrors();
                $response['message']= 'Appointment not updated';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Appointment not found';
        }


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for adding appointment from doctor panel
    */
    public function actionDoctorAddAppointment(){
        $fields=ApiFields::doctorAppointmentFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $response=DrsPanel::addTemporaryAppointment($params,'doctor');
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for updating appointment by patient
    */
    public function actionPatientAddAppointment(){
        $fields=ApiFields::patientAppointmentFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $response=DrsPanel::addTemporaryAppointment($params,'patient');
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for listing doctor current appointment affair
    */
    public function actionCurrentAppointmentAffair(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $userLogin=User::find()->where(['id'=>$params['user_id']])->one();
            $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
            if(!empty($userLogin) && !empty($doctor)){
                $date= date('Y-m-d');

                if(isset($params['schedule_id']) && !empty($params['schedule_id'])){
                    $shift=$params['schedule_id'];
                }
                else{
                    $shift='';
                }

                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                if(!empty($checkForCurrentShift)){
                    $response=$this->getCurrentAffair($checkForCurrentShift,$params['doctor_id'],$date,$shift,$getSlots);
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Please try again';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Something went wrong, Please try again.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id  and user id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /*
    * @Param Null
    * @Function is used for listing doctor current appointment list
    */
    public function actionCurrentAppointments(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $userLogin=User::find()->where(['id'=>$params['user_id']])->one();
            $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
            if(!empty($userLogin) && !empty($doctor)){
                if(isset($params['date']))
                {
                    $date = $params['date'];
                }else 
                {
                   $date= date('Y-m-d');
                }
                if(isset($params['schedule_id']) && !empty($params['schedule_id'])){
                    $current_selected=$params['schedule_id'];
                }
                else{
                    $current_selected=0;
                }

                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);

                $getAppointments=DrsPanel::getCurrentAppointments($params['doctor_id'],$date,$current_selected,$getSlots);
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$getAppointments;
                $response['data']['date']=$date;
                $response['message']= 'Today Appointments List';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Something went wrong, Please try again.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Doctor id  and user id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionAddUpdateReminder(){
        $fields=ApiFields::patientaddreminder();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $user_id=$params['user_id'];
            $appointment_id=$params['appointment_id'];
            $reminder=UserReminder::find()->where(['user_id'=>$user_id,'appointment_id'=>$appointment_id])->one();
            if(empty($reminder)){
                $reminder= new UserReminder();
            }

            $reminder->user_id=$user_id;
            $reminder->appointment_id=$params['appointment_id'];
            $reminder->reminder_date=$params['date'];
            $reminder->reminder_time=$params['time'];
            $reminder->reminder_datetime=(int) strtotime($params['date'].' '.$params['time']);;
            $reminder->status='pending';
            if($reminder->save()){
                $response["status"] = 1;
                $response["error"] = false;
                $response["message"]="Success";
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response["data"]=$reminder->getErrors();
                $response["message"]="Error";
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetReminders(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                $getReminders=DrsPanel::getPatientReminders($params['user_id']);
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$getReminders;
                $response['message']= 'Reminder List';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }



    public function actionDoctorDetail(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['doctor_id']) && $params['doctor_id'] != '' && isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['doctor_id'],'groupid'=>Groups::GROUP_DOCTOR]);
            if(!empty($user)){
                $profile = UserProfile::findOne(['user_id' => $user->id]);
                $data_array=DrsPanel::profiledetails($user,$profile,$user->groupid);
                $data_array['is_favorite']=DrsPanel::checkProfileFavorite($params['user_id'],$params['doctor_id']);
                unset($data_array['shift_time']);
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$data_array;
                $response['date']=date('Y-m-d');
                $response['message']= 'Data';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Doctor not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionFindDoctors(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;

        $groupid=Groups::GROUP_DOCTOR;
        if(isset($params['lat']) && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '') {
            $latitude = $params['lat'];
            $longitude = $params['lng'];
        }

        if(isset($params['type']) && $params['type'] != '') {
            $type = $params['type'];
        }
        else{
            $type = 'list';
        }

        $lists= new Query();
        $lists=UserProfile::find();
        $lists->joinWith('user');
        $lists->where(['user_profile.groupid'=>$groupid]);
        $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
            'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);

        $term='';$v1='';
        if(isset($params['search']) && !empty($params['search'])){
            $term=$params['search'];
        }

        if($term != ''){
            $q_explode=explode(' ',$term);
            $usersearch=array();
            foreach($q_explode as $word){
                $usersearch[] ="user_profile.name LIKE '%".$word."%'";
            }
            $v1=implode(' or ', $usersearch);
        }
        if($v1 != ''){
            $lists->andFilterWhere(['or', $v1]);
        }

        $command = $lists->createCommand();

        $listcat=$valuecat=$listTreatment=[];
        $gender='';

        if(isset($params['filter'])){
            $filters=json_decode($params['filter']);

            foreach($filters as $filter ){
                if($filter->type == 'speciality'){
                    $listcat=$filter->list;
                }

                if($filter->type == 'treatment'){
                    $listTreatment=$filter->list;
                }

                if($filter->type == 'gender'){
                    $gender=$filter->list;
                }
            }

            if(!empty($listcat)){
                $valuecat=[];
                foreach($listcat as $cateval){
                    $metavalues=MetaValues::find()->where(['id'=>$cateval])->one();
                    if($metavalues)
                        $valuecat[]=$metavalues->value;
                }

                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                }
            }

            if(!empty($listTreatment)){
                $valuetreatment=[];
                foreach($listTreatment as $item){
                    $itemvalues=MetaValues::find()->where(['id'=>$item])->one();
                    if($itemvalues)
                        $valuetreatment[]=$itemvalues->value;
                }

                foreach($valuetreatment as $treat){
                    $lists->andWhere('find_in_set(:key3, `user_profile`.`treatment`)', [':key3'=>$treat]);
                }
            }

            if($gender != ''){
                $lists->andWhere(['user_profile.gender'=>$gender]);
            }
        }

        if(isset($params['sort']) && !empty($params['sort'])){
            $sort=json_decode($params['sort']);
            if($sort->type == 'price'){
                if($sort->value == 'low to high'){
                    $lists->orderBy('user_profile.consultation_fees asc');
                }
                else{
                    $lists->orderBy('user_profile.consultation_fees desc');
                }
            }

            if($sort->type == 'rating'){
                if($sort->value == 'low to high'){
                    $lists->orderBy('user_profile.rating asc');
                }
                else{
                    $lists->orderBy('user_profile.rating desc');
                }
            }
        }


        if($type == 'list'){
            if(isset($params['offset']) && $params['offset'] != ''){
                $offset=$params['offset'];
            }
            $countQuery = clone $lists;
            $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
            $lists->limit($recordlimit);
            $lists->offset($offset);
            $lists->all();
            $command = $lists->createCommand();
            $lists = $command->queryAll();

            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            if($count_result == null){
                $count_result=count($lists);
                $offset=count($lists);

            }
            else{
                $oldoffset=$offset;
                $offset = $offset + $recordlimit;
                if($offset > $count_result){
                    $offset=$oldoffset + count($lists);
                }
            }

            $totallist['totalcount']=$count_result;
            $totallist['offset']=$offset;

            $list_a=$this->getList($lists,'list');
            $data_array = array_values($list_a);
            $response["status"] = 1;
            $response["error"] = false;
            $response['pagination']=$totallist;
            $response['data'] = $data_array;
            $response['filters']=$this->getFilterArray();
            $response['sort']=$this->getSortArray();
            $response['message'] = 'Doctors List';

        }
        else{
            $lists = $command->queryAll();
            $list_a=$this->getList($lists,'list');
            $data_array = array_values($list_a);
            $response["status"] = 1;
            $response["error"] = false;
            $response['mapdata'] = $data_array;
            $response['message'] = 'Doctors List';
        }


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionFindHospitals(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;

        $groupid=Groups::GROUP_HOSPITAL;
        if(isset($params['lat']) && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '') {
            $latitude = $params['lat'];
            $longitude = $params['lng'];
            //$user=Appelavocat::getLocationUserList($latitude,$longitude);
        }

        if(isset($params['type']) && $params['type'] != '') {
            $type = $params['type'];
        }
        else{
            $type = 'list';
        }

        $lists= new Query();
        $lists=UserProfile::find();
        $lists->joinWith('user');
        $lists->where(['user_profile.groupid'=>$groupid]);
        $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
            'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
        $addSpeciality=Drspanel::addHospitalSpecialityCount($lists->createCommand()->queryAll());

        $lists->joinWith('hospitalSpecialityTreatment');

        $term='';$v1='';
        if(isset($params['search']) && !empty($params['search'])){
            $term=$params['search'];
        }

        if($term != ''){
            $q_explode=explode(' ',$term);
            $usersearch=array();
            foreach($q_explode as $word){
                $usersearch[] ="user_profile.name LIKE '%".$word."%'";
            }
            $v1=implode(' or ', $usersearch);
        }
        if($v1 != ''){
            $lists->andFilterWhere(['or', $v1]);
        }

        $command = $lists->createCommand();

        $listcat=$valuecat=$listTreatment=[];
        $gender='';

        if(isset($params['filter'])){
            $filters=json_decode($params['filter']);

            foreach($filters as $filter ){
                if($filter->type == 'speciality'){
                    $listcat=$filter->list;
                }

                if($filter->type == 'treatment'){
                    $listTreatment=$filter->list;
                }

                if($filter->type == 'gender'){
                    $gender=$filter->list;
                }
            }

            if(!empty($listcat)){
                foreach($listcat as $cateval){
                    $metavalues=MetaValues::find()->where(['id'=>$cateval])->one();
                    $valuecat[]=$metavalues->value;
                }

                foreach($valuecat as $sev){
                    $lists->andWhere('find_in_set(:key2, `hospital_speciality_treatment`.`speciality`)', [':key2'=>$sev]);
                }
            }

            if(!empty($listTreatment)){
                foreach($listTreatment as $item){
                    $itemvalues=MetaValues::find()->where(['id'=>$item])->one();
                    $valuetreatment[]=$itemvalues->value;
                }

                foreach($valuetreatment as $treat){
                    $lists->andWhere('find_in_set(:key3, `hospital_speciality_treatment`.`treatment`)', [':key3'=>$treat]);
                }
            }

            if($gender != ''){
                $lists->andWhere(['user_profile.gender'=>$gender]);
            }
        }

        if(isset($params['sort']) && !empty($params['sort'])){
            $sort=json_decode($params['sort']);
            if($sort->type == 'price'){
                if($sort->value == 'low to high'){
                    $lists->orderBy('user_profile.consultation_fees asc');
                }
                else{
                    $lists->orderBy('user_profile.consultation_fees desc');
                }
            }

            if($sort->type == 'rating'){
                if($sort->value == 'low to high'){
                    $lists->orderBy('user_profile.rating asc');
                }
                else{
                    $lists->orderBy('user_profile.rating desc');
                }
            }
        }


        if($type == 'list'){
            if(isset($params['offset']) && $params['offset'] != ''){
                $offset=$params['offset'];
            }
            $countQuery = clone $lists;
            $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
            $lists->limit($recordlimit);
            $lists->offset($offset);
            $lists->all();
            $command = $lists->createCommand();
            $lists = $command->queryAll();

            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            if($count_result == null){
                $count_result=count($lists);
                $offset=count($lists);

            }
            else{
                $oldoffset=$offset;
                $offset = $offset + $recordlimit;
                if($offset > $count_result){
                    $offset=$oldoffset + count($lists);
                }
            }

            $totallist['totalcount']=$count_result;
            $totallist['offset']=$offset;

            $list_a=$this->getList($lists,'list');
            $data_array = array_values($list_a);
            $response["status"] = 1;
            $response["error"] = false;
            $response['pagination']=$totallist;
            $response['data'] = $data_array;
            $response['filters']=$this->getFilterArray();
            $response['sort']=$this->getSortArray();
            $response['message'] = 'Hospitals List';

        }
        else{
            $lists = $command->queryAll();
            $list_a=$this->getList($lists,'list');
            $data_array = array_values($list_a);
            $response["status"] = 1;
            $response["error"] = false;
            $response['mapdata'] = $data_array;
            $response['message'] = 'Hospitals List';
        }


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetMyDoctors(){
        $response = $data  = $dataarray = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && $params['user_id'] != ''){

            $userList=DrsPanel::patientMyDoctorsList($params['user_id']);
            /*$appointment=UserAppointment::find()->where(['user_id'=>$params['user_id']])->orderBy('id asc')->all();
            foreach($appointment as $appointment){
                $userList[$appointment->doctor_id]=$appointment->doctor_id;
            }*/

            $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;

            if(isset($params['offset']) && $params['offset'] != ''){
                $offset=$params['offset'];
            }
            $groupid=Groups::GROUP_DOCTOR;
            $lists= new Query();
            $lists=UserProfile::find();
            $lists->joinWith('user');
            $lists->where(['user_profile.groupid'=>$groupid]);
            $lists->andWhere(['user.id'=>$userList]);
            $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);

            $countQuery = clone $lists;
            $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
            $lists->limit($recordlimit);
            $lists->offset($offset);
            $lists->all();
            $command = $lists->createCommand();
            $lists = $command->queryAll();

            if(isset($totalpages)){
                $count_result=$totalpages->totalCount;
            }
            if($count_result == null){
                $count_result=count($lists);
                $offset=count($lists);

            }
            else{
                $oldoffset=$offset;
                $offset = $offset + $recordlimit;
                if($offset > $count_result){
                    $offset=$oldoffset + count($lists);
                }
            }

            $totallist['totalcount']=$count_result;
            $totallist['offset']=$offset;

            $list_a=$this->getList($lists,'list');
            $data_array = array_values($list_a);
            $response["status"] = 1;
            $response["error"] = false;
            $response['pagination']=$totallist;
            $response['data'] = $data_array;
            $response['filters']=$this->getFilterArray();
            $response['sort']=$this->getSortArray();
            $response['message'] = 'Doctors List';

        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetMyPatients(){
        $response = $data  = $data_array = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['doctor_id']) && $params['doctor_id'] != ''){
            $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
            if(isset($params['offset']) && $params['offset'] != ''){
                $offset=$params['offset'];
            }
            $result=DrsPanel::myPatients($params);
            $response["status"] = 1;
            $response["error"] = false;
            $response['pagination']=$result['pagination'];
            $response['data'] = $result['data'];
            $response['message'] = 'Patient List';

        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'DoctorId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetMyAppointments(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                if(isset($params['type']) && $params['type'] != ''){
                    $type = $params['type'];
                    if($type == 'upcoming'){
                        $allckecked=false;
                        $upcomingchecked=true;
                        $pastchecked=false;
                    }
                    elseif($type == 'past'){
                        $allckecked=false;
                        $upcomingchecked=false;
                        $pastchecked=true;
                    }
                    else{
                        $allckecked=true;
                        $upcomingchecked=false;
                        $pastchecked=false;
                    }
                }
                else{
                    $type = 'all';
                    $allckecked=true;
                    $upcomingchecked=false;
                    $pastchecked=false;
                }

                $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;

                if(isset($params['offset']) && $params['offset'] != ''){
                    $offset=$params['offset'];
                }


                $lists=DrsPanel::getPatientAppointments($params['user_id'],$type);
                $countQuery = clone $lists;
                $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
                $lists->limit($recordlimit);
                $lists->offset($offset);
                $lists->all();
                $command = $lists->createCommand();
                $lists = $command->queryAll();

                if(isset($totalpages)){
                    $count_result=$totalpages->totalCount;
                }
                if($count_result == null){
                    $count_result=count($lists);
                    $offset=count($lists);

                }
                else{
                    $oldoffset=$offset;
                    $offset = $offset + $recordlimit;
                    if($offset > $count_result){
                        $offset=$oldoffset + count($lists);
                    }
                }

                $totallist['totalcount']=$count_result;
                $totallist['offset']=$offset;

                $list_a=DrsPanel::getPatientAppointmentsList($lists);
                $getAppointments = array_values($list_a);

                $appointmentList[0]=array('key'=>'all','label'=>'All','isChecked'=>$allckecked);
                $appointmentList[1]=array('key'=>'upcoming','label'=>'Upcoming','isChecked'=>$upcomingchecked);
                $appointmentList[2]=array('key'=>'past','label'=>'Past','isChecked'=>$pastchecked);

                $data['type']=$appointmentList;
                $data['selected']=$type;
                $data['appointments']=$getAppointments;

                $response["status"] = 1;
                $response["error"] = false;
                $response["pagination"]=$totallist;
                $response['data']=$data;
                $response['message']= 'Appointments List';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for getting appointment details
     */
    public function actionGetAppointmentDetails(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['appointment_id']) && $params['appointment_id'] != ''){
            $appointment_id=$params['appointment_id'];
            $appointment=UserAppointment::find()->where(['id'=>$appointment_id])->one();
            if(!empty($appointment)){
                $getAppointments=DrsPanel::patientgetappointmentarray($appointment);
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$getAppointments;
                $response['message']= 'Appointments Detail';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Appointment not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'AppointmentId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionAddReviewRating(){
        $fields=ApiFields::addreviewrating();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $user_id=$params['user_id'];
            $review= new UserRatingLogs();
            $review->user_id=$user_id;
            $review->doctor_id=$params['doctor_id'];
            $review->rating=$params['rating'];
            $review->review=$params['review'];
            if($review->save()){
                $oldRating = UserRating::find()->where(['user_id'=>$params['doctor_id']])->one();
                if(!empty($oldRating)){
                    $totalReviewLogs=UserRatingLogs::find()->where(['doctor_id'=>$params['doctor_id']])->count();
                    if($totalReviewLogs >= 1){
                        $oldratevalue=$oldRating->users_rating + $params['rating'];
                        $rat_new=$oldratevalue/$totalReviewLogs;
                        $rat_new = round(2*$rat_new)/2;
                        $oldRating->users_rating=$rat_new;
                        $oldRating->save();

                    }
                    else{
                        $oldRating->users_rating=$params['rating'];
                        $oldRating->save();
                    }
                }
                else{
                    $rating= new UserRating();
                    $rating->user_id=$params['doctor_id'];
                    $rating->show_rating='User';
                    $rating->admin_rating=0;
                    $rating->users_rating=$params['rating'];
                    $rating->save();
                }
                $updateRatingToProfile=DrsPanel::ratingUpdateToProfile($params['doctor_id']);
                $response["status"] = 1;
                $response["error"] = false;
                $response["message"]="Success";
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response["data"]=$review->getErrors();
                $response["message"]="Error";
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionGetReviewRating(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && $params['user_id'] != ''){
            $user=User::findOne(['id'=>$params['user_id'],'groupid'=>Groups::GROUP_DOCTOR]);
            if(!empty($user)){

                $offset=0;$recordlimit=3;

                if(isset($params['offset']) && $params['offset'] != ''){
                    $offset=$params['offset'];
                }

                $listarray=DrsPanel::getRatingList($params['user_id'],$offset,$recordlimit);
                $response["status"] = 1;
                $response["error"] = false;
                $response["pagination"]=$listarray['totallist'];
                $response['data']=$listarray['list'];
                $response['message']= 'Data';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Doctor not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetPagesList(){
        $pages=Page::find()->where(['status'=>1])->all();
        $response = $static=array();
        $l=0;
        foreach($pages as $page){
            $static[$l]['id']=$page->id;
            $static[$l]['title']=$page->title;
            $static[$l]['slug']=$page->slug;
            $l++;
        }
        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $static;
        $response['message'] = 'Success';
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionPage() {
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        $rows = array();

        if(isset($params['slug']) && !empty($params['slug'])){
            $model = Page::find()->where(['slug' => '' . $params['slug'] . ''])->one();
            if ($model) {
                $rows['title'] = $model['title'];
                $rows['body'] = $model['body'];

                $response["status"] = 1;
                $response["error"] = false;
                $response['data'] = $rows;
                $response['message'] = 'Success';

            } else {
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Page not found.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Required parameter missing.';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetFilterArray(){
        $response = $data  =$data_array = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        $groups_v=array();

        $l=0;
        $key=MetaKeys::findOne(['key'=>'speciality']);
        if(!empty($key)){
            $metavalues=MetaValues::find()->where(['key'=>$key->id])->all();
            $m=0;
            $groups_v[$l]['type']=$key->key;
            $groups_v[$l]['list']=array();
            foreach($metavalues as $values){
                $groups_v[$l]['list'][$m]['id']=$values->id;
                $groups_v[$l]['list'][$m]['label']=$values->label;
                $groups_v[$l]['list'][$m]['value']=$values->value;
                $m++;
            }
            $l++;
        }
        $groups_v[$l]['type']='gender';
        $groups_v[$l]['list']=array();
        $m=0;
        $gender[0]=array('id'=>UserProfile::GENDER_MALE,'label'=>'Male');
        $gender[1]=array('id'=>UserProfile::GENDER_FEMALE,'label'=>'Female');
        $gender[2]=array('id'=>UserProfile::GENDER_OTHER,'label'=>'Other');
        foreach($gender as $values){
            $groups_v[$l]['list'][$m]['id']=$values['id'];
            $groups_v[$l]['list'][$m]['label']=$values['label'];
            $groups_v[$l]['list'][$m]['value']=$values['id'];
            $m++;
        }
        $l++;


        $groups_v[$l]['type']='availability_slot';
        $groups_v[$l]['list']=array();
        $m=0;
        $shift[0]=array('id'=>UserSchedule::SHIFT_MORNING,'label'=>'Morning');
        $shift[1]=array('id'=>UserSchedule::SHIFT_AFTERNOON,'label'=>'Afternoon');
        $shift[2]=array('id'=>UserSchedule::SHIFT_EVENING,'label'=>'Evening');
        foreach($shift as $values){
            $groups_v[$l]['list'][$m]['id']=$values['id'];
            $groups_v[$l]['list'][$m]['label']=$values['label'];
            $groups_v[$l]['list'][$m]['value']=$values['id'];
            $m++;
        }


        $response["status"] = 1;
        $response["error"] = false;
        $response['data'] = $groups_v;
        $response['message']='Success';


        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionHomeScreenData(){
        $response["status"] = 1;
        $response["error"] = false;
        $homeData=DrsPanel::homeScreenData();
        $data['list'][0]['type']='speciality';
        $data['list'][0]['label']='Popular Categories';
        $data['list'][0]['sub_categories']=$homeData['speciality'];
        $data['list'][1]['type']='treatment';
        $data['list'][1]['label']='Popular Diseases';
        $data['list'][1]['sub_categories']=$homeData['treatment'];
        $data['list'][2]['type']='hospitals';
        $data['list'][2]['label']='Popular Hospitals';
        $data['list'][2]['sub_categories']=$homeData['hospitals'];
        $response['sliders']=$homeData['slider_images'];
        $response['cities']=DrsPanel::getCitiesList();
        $selected['id']=15098;
        $selected['name']='Jaipur';
        $response['selected_city']=$selected;
        $response['data'] = $data['list'];
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDoctorHospitals(){
        $response = $data  = $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        if(isset($params['doctor_id']) && !empty($params['doctor_id'])) {
           $doctor=User::find()->where(['id'=>$params['doctor_id']])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->one();
           if($doctor){
            $data=DrsPanel::doctorHospitalList($params['doctor_id']);
            if(count($data['apiList'])){

                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= $data['apiList'];  

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Please add address.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Are You sour doctor.';
        }

    }else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message'] = 'Doctor id required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

public function actionAppointmentShedules(){
    $response = $data  = $required = array();
    $params = Yii::$app->request->post();
    Yii::info($params, __METHOD__);
    if(isset($params['doctor_id']) && !empty($params['doctor_id'])) {
        $andwhere['user_id']=$params['doctor_id'];
        if(isset($params['date']) && !empty($params['date'])){
            $andwhere['weekday']=date('l', strtotime($params['date']));
        }else{
            $andwhere['weekday']=date('l', strtotime(date('Y-m-d')));
        }

        if(isset($params['shift_id']) && !empty($params['shift_id'])){
            $andwhere['id']=$params['shift_id'];
        }
        $doctor=User::find()->where(['id'=>$params['doctor_id']])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->one();
        if($doctor){
            $data=DrsPanel::appointmentShedules($andwhere);
            if(count($data)){

                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= $data;  

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'You have no any appointment.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Are You sour doctor.';
        }

    }else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message'] = 'Doctor id required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

public function actionGetSettingDetail(){
    $response = $data  = $dataarray = array();
    $params = Yii::$app->request->queryParams;
    Yii::info($params, __METHOD__);
    if(isset($params['user_id']) && $params['user_id'] != ''){
        $settings=UserSettings::find()->where(['user_id'=>$params['user_id']])->all();
        if(empty($settings)){
            $setting=new UserSettings();
            $setting->user_id=$params['user_id'];
            $setting->key_name='show_countdown';
            $setting->key_value=1;
            $setting->save();

            $setting1=new UserSettings();
            $setting1->user_id=$params['user_id'];
            $setting1->key_name='popup_notify';
            $setting1->key_value=1;
            $setting1->save();

            $setting1=new UserSettings();
            $setting1->user_id=$params['user_id'];
            $setting1->key_name='show_fees';
            $setting1->key_value=1;
            $setting1->save();

            $settings=UserSettings::find()->where(['user_id'=>$params['user_id']])->all();
        }

        $s=0;
        foreach($settings as $setting){
            $dataarray[$s]['setting_id']=$setting->id;
            $dataarray[$s]['key']=$setting->key_name;
            $dataarray[$s]['value']=$setting->key_value;
            $s++;
        }
        $response["status"] = 1;
        $response["error"] = false;
        $response['data']=$dataarray;
        $response['message']= 'Setting Data';
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'UserId Required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

public function actionAddSettingDetail(){
    $response = $data  = $dataarray = array();
    $params = Yii::$app->request->post();
    Yii::info($params, __METHOD__);

    if(isset($params['user_id']) && $params['user_id'] != ''){
        if(isset($params['setting_id']) && $params['setting_id'] != '' && isset($params['value']) && $params['value'] != '' ){
            $setting=UserSettings::find()->where(['user_id'=>$params['user_id'],'id'=>$params['setting_id']])->one();
            $setting->key_value=$params['value'];
            if($setting->save()){
                $settings=UserSettings::find()->where(['user_id'=>$params['user_id']])->all();
                $s=0;
                foreach($settings as $setting){
                    $dataarray[$s]['setting_id']=$setting->id;
                    $dataarray[$s]['key']=$setting->key_name;
                    $dataarray[$s]['value']=$setting->key_value;
                    $s++;
                }
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$dataarray;
                $response['message']= 'Setting Updated';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['data']=$setting->getErrors();
                $response['message']= 'Please try again';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Setting key id/value Required';
        }
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'UserId Required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

    public function actionAddAttender(){
        $fields=ApiFields::attenderUpsert();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }
        if(empty($required)){
            $attender= new AttenderForm();
            $post['groupid']=Groups::GROUP_ATTENDER;
            $attender->load(['AttenderForm'=>$post]);
            if($attender->signup()){
                $user=User::find()->where(['email'=>$attender->email])->one();
                if(!empty($user)){
                    if (isset($_FILES['image'])){
                        $imageUpload=DrsImageUpload::updateProfileImageApp($user->id,$_FILES);
                    }
                    if(isset($post['shift_id']) && ($post['shift_id'] != '')){

                        $addressList=DrsPanel::doctorHospitalList($post['parent_id']);
                        $listadd=$addressList['apiList'];
                        $shift_array = array(); $s=0;
                        $shift_value = array(); $sv=0;
                        foreach($listadd as $address){
                            $shifts=DrsPanel::getShiftListByAddress($post['parent_id'],$address['id']);
                            foreach($shifts as $key => $shift){
                                $shift_array[$s]['value'] = $shift['shifts_ids'];
                                $shift_array[$s]['label'] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                                $shift_value[$sv] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                                $s++;$sv++;
                            }
                        }

                        $sel_shift=explode(',', $post['shift_id']);
                        $shift_val=array();
                        foreach($sel_shift as $s){
                            $shift_selected_ids=$shift_array[$s];
                            $list=$shift_selected_ids['value'];
                            foreach($list as $list){
                                $shift_val[]=$list;
                            }
                        }
                        $addupdateAttender=DrsPanel::addUpdateAttenderToShifts($shift_val,$user->id);
                    }

                    if(isset($post['doctor_id']) && !empty($post['doctor_id'])){
                        $doctors=explode(',', $post['doctor_id']);
                        $addupdateHospitalDoctors=DrsPanel::addUpdateDoctorsToHospitalAttender($doctors,$user->id,$post['parent_id']);
                    }

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['data']=$user;
                    $response['message']= 'Attender successfully added.';
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Please try again';
                }

            }
            else{
                $validation_error=Drspanel::validationErrorMessage($attender->getErrors());
                $response["status"] = 0;
                $response["error"] = true;
                if(!empty($validation_error)){
                    $response['errordata']= $validation_error;
                    $response['message']= $validation_error['message'];
                }
                else{
                    $response['message']= 'Require fields does not match.';
                }

            }

        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionUpdateAttender(){
        $fields=ApiFields::attenderUpdate();
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }

        if(empty($required)){
            if(isset($post['id']) && !empty($post['id'])){
                $attender=User::findOne($post['id']);
                if(empty($attender)){
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Attender id does not match.';
                    Yii::info($response, __METHOD__);
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $userProfile=UserProfile::findOne($post['id']);
                $userData['email']=$post['email'];
                $userData['phone']=$post['phone'];
                $profileData['name']=$post['name'];
                if(isset($post['dob']) && !empty($post['dob'])){
                    $profileData['dob']=$post['dob'];
                }
                if(isset($post['gender']) && !empty($post['gender'])){
                    $profileData['gender']=$post['gender'];
                }
                $userData['countrycode']=isset($post['countrycode'])?$post['countrycode']:91;
                $attender->load(['User'=>$userData]);
                $userProfile->load(['UserProfile'=>$profileData]);
                if($attender->save() && $userProfile->save()) {
                    if (isset($_FILES['image'])){
                        $imageUpload=DrsImageUpload::updateProfileImageApp($attender->id,$_FILES);
                    }
                    if(isset($post['shift_id']) && ($post['shift_id'] != '') ){
                        $addressList=DrsPanel::doctorHospitalList($post['parent_id']);
                        $listadd=$addressList['apiList'];
                        $shift_array = array(); $s=0;
                        $shift_value = array(); $sv=0;
                        foreach($listadd as $address){
                            $shifts=DrsPanel::getShiftListByAddress($post['parent_id'],$address['id']);
                            foreach($shifts as $key => $shift){
                                $shift_array[$s]['value'] = $shift['shifts_ids'];
                                $shift_array[$s]['label'] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                                $shift_value[$sv] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                                $s++;$sv++;
                            }
                        }

                        $sel_shift=explode(',', $post['shift_id']);
                        $shift_val=array();
                        foreach($sel_shift as $s){
                            $shift_selected_ids=$shift_array[$s];
                            $list=$shift_selected_ids['value'];
                            foreach($list as $list){
                                $shift_val[]=$list;
                            }
                        }
                        $addupdateAttender=DrsPanel::addUpdateAttenderToShifts($shift_val,$attender->id);
                    }else{
                        $addupdateAttender=DrsPanel::addUpdateAttenderToShifts(array(),$attender->id);
                    }

                    if(isset($post['doctor_id']) && !empty($post['doctor_id'])){
                        $doctors=explode(',', $post['doctor_id']);
                        $addupdateHospitalDoctors=DrsPanel::addUpdateDoctorsToHospitalAttender($doctors,$attender->id,$attender->parent_id);
                    }
                    else{
                        $addupdateHospitalDoctors=DrsPanel::addUpdateDoctorsToHospitalAttender(array(),$attender->id,$attender->parent_id);
                    }

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['data']=$post;
                    $response['message']= 'Attender successfully updated.';
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['data']=$attender->getErrors();
                    $response['message']= 'Require fields does not match.';
                }
            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Attender id required.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionAttenderList(){
        $fields=ApiFields::attenderList();
        $response = $data  = $required = array();
        $id=NULL;
        $params = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }

        Yii::info($params, __METHOD__);
        if(empty($required)) {
            $search['parent_id']=$params['doctor_id'];
            if(isset($params['address_id']) && !empty($params['address_id']))
            {
                $search['address_id']=$params['address_id'];
            }
            $data=DrsPanel::attenderList($search,'apilist');
            if($data){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= $data;

            }else{
                $response["status"] = 1;
                $response["error"] = false;
                $response['message'] = 'Please added attender.';
            }

        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDeleteAttender(){
        $response = $data =  $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);
       
        if(isset($post['id']) && !empty($post['id'])){
            $attender=User::findOne($post['id']);
            if(empty($attender)){
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Attender id does not match.';
                Yii::info($response, __METHOD__);
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
            $userProfile=UserProfile::findOne($post['id']);
            $addupdateAttender=DrsPanel::addUpdateAttenderToShifts(array(),$attender->id);
            
            $cond['id']=$attender->id;
            $cond1['user_id']=$userProfile->user_id;
            User::deleteAll($cond);
            UserProfile::deleteAll($cond1);

            $response["status"] = 1;
            $response["error"] = false;
            $response['message']= 'Attender deleted.';            
            
            
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Attender id required.';
        }   
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDoctorShiftList(){
        $fields=ApiFields::shiftList();
        $response = $data  = $required = array();
        $post = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }

        Yii::info($post, __METHOD__);
        if(empty($required)) {
            $doctor_id=$post['doctor_id'];
            $addressList=DrsPanel::doctorHospitalList($doctor_id);
            $listadd=$addressList['apiList'];
            $shift_array = array(); $s=0;
            foreach($listadd as $address){
                $shifts=DrsPanel::getShiftListByAddress($doctor_id,$address['id']);
                foreach($shifts as $key => $shift){
                    $shift_array[$s]['id'] = $s;
                    $shift_array[$s]['value'] = $shift['shifts_ids'];
                    $shift_array[$s]['label'] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                    $s++;
                }
            }
            if(!empty($shift_array)){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= $shift_array;

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'You have not any shift added.';
            }

        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionHospitalShiftList(){
        $response = $data  = $required = array();
        $post = Yii::$app->request->post();
        Yii::info($post, __METHOD__);
        if(isset($post['hospital_id']) && !empty($post['hospital_id'])) {
            $search['user_id']=$post['hospital_id'];
            $shifts=Drspanel::shiftList($search,'apilist','hospital');
            if($shifts){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Success';
                $response['data']= $shifts;

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'You have not any shift.';
            }

        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'hospital id required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for paytm callback action to update payment details
     */
    public function actionPaytmWalletCallback(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        $request = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        Yii::info($request, __METHOD__);
        $callback=Payment::paytm_wallet_callback($params,$request);
        if (!empty($callback) && isset($callback['STATUS'])){
            if ($callback['STATUS'] != 'TXN_SUCCESS') {
                $response["status"] = 0;
                $response["error"] = true;
                $response["message"] =$callback['RESPMSG'] ;
                $response["data"] = $callback;
                Yii::$app->response->statusCode = 201;
                Yii::info($response, __METHOD__);
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }else {
                $response["status"] = 1;
                $response["error"] = false;
                $response["message"] = "Appointment booked successfully";
                $response["data"] = $callback;
                Yii::$app->response->statusCode = 200;
                Yii::info($response, __METHOD__);
                \Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response["message"] = 'Callback failed' ;
            $response["data"] = [];
            Yii::$app->response->statusCode = 201;
            Yii::info($response, __METHOD__);
            \Yii::$app->response->format = Response::FORMAT_JSON;
            return $response;
        }
    }

    /**
     * @Param Null
     * @Function is used for sending paytm response to api end
     */
    public function actionPaytmResponse(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['appointment_id']) && $params['appointment_id'] != ''){
            $appointment_id=$params['appointment_id'];
            $appointment_temp=UserAppointmentTemp::find()->where(['id'=>$appointment_id])->one();

            if(!empty($appointment_temp)) {
                if ($appointment_temp->payment_status == UserAppointment::PAYMENT_COMPLETED) {
                    $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
                    $appointment_id=$transaction->appointment_id;
                    $appointment=UserAppointment::find()->where(['id'=>$appointment_id])->one();
                    if(!empty($appointment)){
                        if($appointment->payment_status == UserAppointment::PAYMENT_COMPLETED){
                            $response["data"]=DrsPanel::patientgetappointmentarray($appointment);
                            $response["status"] = 1;
                            $response["error"] = false;
                            $response['message']= 'Payment successfully done!';
                        }
                        else{
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message']= 'Payment Pending';
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message']= 'Appointment not found';
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Payment Pending or Failed';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Appointment not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'AppointmentId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionHospitalAppointmentDoctors(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id'])) {

            $user=User::find()->where(['id'=>$params['user_id']])->one();
            if(!empty($user)){
                $groupid=Groups::GROUP_DOCTOR;
                if(isset($params['lat']) && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '') {
                    $latitude = $params['lat'];
                    $longitude = $params['lng'];
                    //$user=Appelavocat::getLocationUserList($latitude,$longitude);
                }

                if(isset($params['type']) && $params['type'] != '') {
                    $type = $params['type'];
                }
                else{
                    $type = 'list';
                }

                $lists= new Query();
                $lists=UserProfile::find();
                $lists->joinWith('user');
                $lists->where(['user_profile.groupid'=>$groupid]);
                $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                    'user.admin_status'=>User::STATUS_ADMIN_APPROVED]);
                if($user->groupid==Groups::GROUP_ATTENDER){
                    $hospitals_id=$user->parent_id;
                    $attender=$user;
                }else{
                    $hospitals_id=$params['user_id'];
                }
                if(isset($params['date']) && !empty($params['date'])){
                    $date=$params['date'];
                }else{
                    $date=date('Y-m-d');
                }
                $ids=DrsPanel::dateWiseHospitalDoctors($hospitals_id,$date,$attender);

                $lists->andWhere(['user.id'=>$ids]);
                $command = $lists->createCommand();

                $listcat=array();$valuecat=array();
                $gender='';

                if(isset($params['filter'])){
                    $filters=json_decode($params['filter']);

                    foreach($filters as $filter ){
                        if($filter->type == 'speciality'){
                            $listcat=$filter->list;
                        }

                        if($filter->type == 'gender'){
                            $gender=$filter->list;
                        }
                    }

                    if(!empty($listcat)){
                        foreach($listcat as $cateval){
                            $metavalues=MetaValues::find()->where(['id'=>$cateval])->one();
                            $valuecat[]=$metavalues->value;
                        }

                        //$lists->andWhere(['in', 'user_profile.speciality', $valuecat]);
                        //$searchvalue=implode(',',$valuecat);

                        foreach($valuecat as $sev){
                            $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                        }
                    }

                    if($gender != ''){
                        $lists->andWhere(['user_profile.gender'=>$gender]);
                    }
                }

                if(isset($params['sort']) && !empty($params['sort'])){
                    $sort=json_decode($params['sort']);
                    if($sort->type == 'price'){
                        if($sort->value == 'low to high'){
                            $lists->orderBy('user_profile.consultation_fees asc');
                        }
                        else{
                            $lists->orderBy('user_profile.consultation_fees desc');
                        }
                    }

                    if($sort->type == 'rating'){
                        if($sort->value == 'low to high'){
                            $lists->orderBy('user_profile.rating asc');
                        }
                        else{
                            $lists->orderBy('user_profile.rating desc');
                        }
                    }
                }


                if($type == 'list'){
                    if(isset($params['offset']) && $params['offset'] != ''){
                        $offset=$params['offset'];
                    }
                    $countQuery = clone $lists;
                    $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
                    $lists->limit($recordlimit);
                    $lists->offset($offset);
                    $lists->all();
                    $command = $lists->createCommand();
                    $lists = $command->queryAll();

                    if(isset($totalpages)){
                        $count_result=$totalpages->totalCount;
                    }
                    if($count_result == null){
                        $count_result=count($lists);
                        $offset=count($lists);

                    }
                    else{
                        $oldoffset=$offset;
                        $offset = $offset + $recordlimit;
                        if($offset > $count_result){
                            $offset=$oldoffset + count($lists);
                        }
                    }

                    $totallist['totalcount']=$count_result;
                    $totallist['offset']=$offset;

                    $list_a=$this->getList($lists,'list');
                    $data_array = array_values($list_a);
                    $response['pagination']=$totallist;
                    $response['data'] = $data_array;
                    $response['filters']=$this->getFilterArray();
                    $response['sort']=$this->getSortArray();


                }
                else{
                    $lists = $command->queryAll();
                    $list_a=$this->getList($lists,'list');
                    $data_array = array_values($list_a);
                    $response['mapdata'] = $data_array;
                }
                $response["status"] = 1;
                $response["error"] = false;
                $response['message'] = 'Doctors List';
                $response['profile'] = DrsPanel::hospitalProfile($params['user_id']);
                $s_list=DrsPanel::getMetaData('speciality');
                $groups_v['id']=0;
                $groups_v['value']='All';
                $groups_v['label']='All';
                $groups_v['count']=0;
                $groups_v['icon']='';
                $groups_v['isChecked']=true;
                array_unshift($s_list,$groups_v);
                $response['speciality']=$s_list;
                $response['date']=$date;

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Are you sour login with hospital.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionFindDoctorHospitals(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id'])) {

            $user=User::find()->where(['id'=>$params['user_id']])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->one();
            if(!empty($user)){
                $groupid=Groups::GROUP_HOSPITAL;
                if(isset($params['lat']) && isset($params['lng']) && $params['lat'] != '' && $params['lng'] != '') {
                    $latitude = $params['lat'];
                    $longitude = $params['lng'];
                    //$user=Appelavocat::getLocationUserList($latitude,$longitude);
                }

                if(isset($params['type']) && $params['type'] != '') {
                    $type = $params['type'];
                }
                else{
                    $type = 'list';
                }

                $lists= new Query();
                $lists=UserProfile::find();
                $lists->joinWith('user');
                $lists->where(['user_profile.groupid'=>$groupid]);
                $lists->andWhere(['user.status'=>User::STATUS_ACTIVE,
                    'user.admin_status'=>User::STATUS_ADMIN_LIVE_APPROVED]);
                $doctor_id=$params['user_id'];

                if(isset($params['list_type']) && $params['list_type']=='Requested'){ //  Requested hospitals list
                    $reqUserSearch=['status'=>UserRequest::Requested,'request_to'=>$doctor_id,'groupid'=>Groups::GROUP_HOSPITAL];
                    $requested=UserRequest::requestedUser($reqUserSearch,'request_to');
                    $lists->andWhere(['user.id'=>$requested]);
                }else { // Confirm hospitals list
                    $confirmDrSearch=['request_to'=>$doctor_id,'groupid'=>Groups::GROUP_HOSPITAL,'status'=>[UserRequest::Request_Confirmed,UserRequest::Requested]];
                    $confirmDr=UserRequest::requestedUser($confirmDrSearch,'request_to');
                    $lists->andWhere(['user.id'=>$confirmDr]);
                }

                $command = $lists->createCommand();

                $listcat=array();$valuecat=array();
                $gender='';

                if(isset($params['filter'])){
                    $filters=json_decode($params['filter']);

                    foreach($filters as $filter ){
                        if($filter->type == 'speciality'){
                            $listcat=$filter->list;
                        }

                    }

                    if(!empty($listcat)){
                        foreach($listcat as $cateval){
                            $metavalues=MetaValues::find()->where(['id'=>$cateval])->one();
                            $valuecat[]=$metavalues->value;
                        }

                        //$lists->andWhere(['in', 'user_profile.speciality', $valuecat]);
                        //$searchvalue=implode(',',$valuecat);

                        foreach($valuecat as $sev){
                            $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                        }
                    }

                }

                if(isset($params['sort']) && !empty($params['sort'])){
                    $sort=json_decode($params['sort']);
                    if($sort->type == 'price'){
                        if($sort->value == 'low to high'){
                            $lists->orderBy('user_profile.consultation_fees asc');
                        }
                        else{
                            $lists->orderBy('user_profile.consultation_fees desc');
                        }
                    }

                    if($sort->type == 'rating'){
                        if($sort->value == 'low to high'){
                            $lists->orderBy('user_profile.rating asc');
                        }
                        else{
                            $lists->orderBy('user_profile.rating desc');
                        }
                    }
                }


                if($type == 'list'){
                    if(isset($params['offset']) && $params['offset'] != ''){
                        $offset=$params['offset'];
                    }
                    $countQuery = clone $lists;
                    $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
                    $lists->limit($recordlimit);
                    $lists->offset($offset);
                    $lists->all();
                    $command = $lists->createCommand();
                    $lists = $command->queryAll();

                    if(isset($totalpages)){
                        $count_result=$totalpages->totalCount;
                    }
                    if($count_result == null){
                        $count_result=count($lists);
                        $offset=count($lists);

                    }
                    else{
                        $oldoffset=$offset;
                        $offset = $offset + $recordlimit;
                        if($offset > $count_result){
                            $offset=$oldoffset + count($lists);
                        }
                    }

                    $totallist['totalcount']=$count_result;
                    $totallist['offset']=$offset;

                    $list_a=$this->getList($lists,'list');
                    $data_array = array_values($list_a);
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['pagination']=$totallist;
                    $response['data'] = $data_array;
                    $response['filters']=$this->getFilterArray();
                    $response['sort']=$this->getSortArray();
                    $response['message'] = 'Hospitals List';

                }
                else{
                    $lists = $command->queryAll();
                    $list_a=$this->getList($lists,'hospital_doctors',$params['user_id']);
                    $data_array = array_values($list_a);
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['mapdata'] = $data_array;
                    $response['message'] = 'Hospitals List';
                }

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Are you sure login with hospital.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionFindAllDoctors(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id'])){
            $user=User::find()->where(['id'=>$params['user_id']])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->one();
            if(!empty($user)){
                $hospital_id=$params['user_id'];
                $lists=DrsPanel::doctorsHospitalList($hospital_id,'all',$usergroup = $user->groupid,$params['user_id']);

                $term='';$v1='';
                if(isset($params['search']) && !empty($params['search'])){
                    $term=$params['search'];
                }

                if($term != ''){
                    $q_explode=explode(' ',$term);
                    $usersearch=array();
                    foreach($q_explode as $word){
                        $usersearch[] ="user_profile.name LIKE '%".$word."%'";
                    }
                    $v1=implode(' or ', $usersearch);
                }
                if($v1 != ''){
                    $lists->andFilterWhere(['or', $v1]);
                }

                $command = $lists->createCommand();

                if(isset($params['offset']) && $params['offset'] != ''){
                    $offset=$params['offset'];
                }
                $countQuery = clone $lists;
                $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
                $lists->limit($recordlimit);
                $lists->offset($offset);
                $lists->all();
                $command = $lists->createCommand();
                $lists = $command->queryAll();

                if(isset($totalpages)){
                    $count_result=$totalpages->totalCount;
                }
                if($count_result == null){
                    $count_result=count($lists);
                    $offset=count($lists);

                }
                else{
                    $oldoffset=$offset;
                    $offset = $offset + $recordlimit;
                    if($offset > $count_result){
                        $offset=$oldoffset + count($lists);
                    }
                }
                $totallist['totalcount']=$count_result;
                $totallist['offset']=$offset;
                $list_a=$this->getList($lists,'hospital_doctors',$hospital_id);
                $data_array = array_values($list_a);
                $response["status"] = 1;
                $response["error"] = false;
                $response['pagination']=$totallist;
                $response['data'] = $data_array;
                $response['message'] = 'Doctors List';
            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Are you sure you are login with hospital or hospital attender.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionHospitalDoctorsList(){
        $response = $data =  $required = $user= $lists= $list_a=$search = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id'])){
            $user=User::find()->where(['id'=>$params['user_id']])->andWhere(['groupid'=>[Groups::GROUP_HOSPITAL,Groups::GROUP_ATTENDER]])->one();
            if(!empty($user)){
                if($user->groupid==Groups::GROUP_ATTENDER){
                    $hospital_id=$user->parent_id;
                }else{
                    $hospital_id=$params['user_id'];
                }

                if(isset($params['shift'])){
                    $search['shift']=true;
                }
                if(isset($params['current'])){
                    $search['current']=true;
                }

                $lists=DrsPanel::doctorsHospitalList($hospital_id,'Confirm',$usergroup = $user->groupid,$params['user_id'],$search);
                $command = $lists->createCommand();

                $countQuery_speciality = clone $lists;
                $countTotal=$countQuery_speciality->count();

                $fetchCount=Drspanel::fetchSpecialityCount($command->queryAll());

                if(isset($params['filter'])){
                    $filters=json_decode($params['filter']);

                    foreach($filters as $filter ){
                        if($filter->type == 'speciality'){
                            $listcat=$filter->list;
                        }
                    }
                    $valuecat=array();
                    if(!empty($listcat)){
                        foreach($listcat as $cateval){
                            $metavalues=MetaValues::find()->where(['id'=>$cateval])->one();
                            if(!empty($metavalues)){
                                $valuecat[]=$metavalues->value;
                            }
                        }
                        foreach($valuecat as $sev){
                            if($v=1){
                                $lists->andWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                            }
                            else{
                                $lists->orWhere('find_in_set(:key2, `user_profile`.`speciality`)', [':key2'=>$sev]);
                            }
                        }
                    }
                }
                $command = $lists->createCommand();

                if(isset($params['offset']) && $params['offset'] != ''){
                    $offset=$params['offset'];
                }
                $countQuery = clone $lists;
                $totalpages = new Pagination(['totalCount' => $countQuery->count()]);
                $lists->limit($recordlimit);
                $lists->offset($offset);
                $lists->all();
                $command = $lists->createCommand();
                $lists = $command->queryAll();

                if(isset($totalpages)){
                    $count_result=$totalpages->totalCount;
                }
                if($count_result == null){
                    $count_result=count($lists);
                    $offset=count($lists);

                }
                else{
                    $oldoffset=$offset;
                    $offset = $offset + $recordlimit;
                    if($offset > $count_result){
                        $offset=$oldoffset + count($lists);
                    }
                }

                $totallist['totalcount']=$count_result;
                $totallist['offset']=$offset;

                $list_a=$this->getList($lists,'hospital_doctors',$hospital_id);
                $data_array = array_values($list_a);

                $response["status"] = 1;
                $response["error"] = false;
                $response['pagination']=$totallist;
                $response['data'] = $data_array;
                $response['message'] = 'Doctors List';

                if($user->groupid==Groups::GROUP_HOSPITAL){
                    $user=User::findOne($params['user_id']);
                    $profile = UserProfile::findOne(['user_id' => $user->id]);
                    $response['profile'] = DrsPanel::profiledetails($user,$profile,$user->groupid);
                }
                else{
                    $response['profile'] = DrsPanel::hospitalProfile($params['user_id']);
                }
                $s_list=DrsPanel::getSpecialityWithCount('speciality',$fetchCount);
                $groups_v['id']=0;
                $groups_v['value']='All';
                $groups_v['label']='All';
                $groups_v['count']=$countTotal;
                $groups_v['icon']='';
                $groups_v['isChecked']=true;
                array_unshift($s_list,$groups_v);
                $response['speciality']=$s_list;

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Are you sure you are login with hospital or hospital attender.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for sending request to doctor
     */
    public function actionHospitalSendRequest(){
        $fields=ApiFields::userRequestFields();
        $response = $data  = $required = array();
        $id=NULL;
        $post = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }

        Yii::info($post, __METHOD__);
        if(empty($required)) {
            $user=User::find()->andWhere(['id'=>$post['request_from']])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->one();
            if(!empty($user)){
                $address=UserAddress::find()->where(['user_id'=>$post['request_from']])->one();
                if(empty($address)){
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Please add address to your profile first';

                    Yii::info($response, __METHOD__);
                    \Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }
                $post['groupid']=Groups::GROUP_HOSPITAL;
                $post['request_from']=$post['request_from'];
                $req_to_ids=explode(',',$post['request_to']);
                if(count($req_to_ids)){
                    foreach ($req_to_ids as $key => $value) {
                        $post['request_to']=$value;
                        UserRequest::updateStatus($post,'Add');
                    }
                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['message']= 'Request Submitted.';
                }else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Request to ids required in comma separated.';
                }

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDoctorAcceptRequest(){
        $fields=ApiFields::userRequestFields();
        $response = $data  = $required = array();
        $id=NULL;
        $post = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }

        Yii::info($post, __METHOD__);
        if(empty($required)) {

            $user=User::find()->andWhere(['id'=>$post['request_from']])->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->one();
            if(!empty($user)){
                $req_to_ids=explode(',',$post['request_to']);
                if(count($req_to_ids)){
                    $i=$j=0;
                    $accepted=$doNot=[];
                    foreach ($req_to_ids as $key => $value) {
                        $update['status']=2;
                        $update['request_from']=$value;
                        $update['request_to']=$post['request_from'];
                        if(UserRequest::updateStatus($update,'edit')){
                            $accepted[$i]=$value;
                            $i++;
                        }else{
                            $doNot[$j]=$value;
                            $j++;
                        }
                    }
                    if(count($accepted)>0){
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response['message']= 'Request Accepted.';
                    }else{
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"] = implode(',',$doNot);
                        $response['message']= 'Request Not Accepted.';
                    }
                }else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Request to ids required in comma separated.';
                }

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionPatientMembers(){
        $response = $data =  $required = $user= $lists= $list_a= $memberData= array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        $MemberFiles = DrsPanel::membersList($params['user_id']);
        foreach ($MemberFiles as  $value) {
            $row['user_id'] = $value['user_id'];
            $row['member_id'] = $value['id'];
            $row['name'] = $value['name'];
            $row['phone'] = $value['phone'];
            $row['gender'] = $value['gender'];
            $memberData[] = $row;
        }
        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id'])) {
            if($user=User::find()->andWhere(['id'=>$params['user_id']])->andWhere(['groupid'=>Groups::GROUP_PATIENT])->one()){
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']=$memberData;
                $response['message']= 'success.';

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'You have not access.';
            }

        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionPatientMembersRecordsList(){
        $response = $data =  $required = $user= $lists= $list_a= $memberData= array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $MemberFiles = DrsPanel::membersListFiles($params['member_id']);
        $response["status"] = 1;
        $response["error"] = false;
        $response['data']=$MemberFiles;
        $response['message']= 'success.';

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionPatientMemberImagesUpload(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        $offset=0;$recordlimit=3; $totalpages=0;$count_result=0;
        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['member_id']) && !empty($params['member_id']) && isset($_FILES['file']) && !empty($_FILES)) {
            $member=PatientMembers::find()->andWhere(['user_id'=>$params['user_id']])->andWhere(['id'=>$params['member_id']])->one();

            if(isset($params['record_label'])){
                $record_label=$params['record_label'];
            }
            else{
                $record_label='Record';
            }
            if(!empty($member)){
                $image_upload=DrsImageUpload::memberImages($member,$record_label,$_FILES);
                $response["status"] = 1;
                $response["error"] = false;
                $response["data"]= DrsPanel::membersListFiles($params['member_id']);
                $response['message']= 'success.';

            }else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'You have not members.';
            }

        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionDeletePatientRecord(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $record_id = $params['record_id'];
        $member_id=$params['member_id'];
        $member  = PatientMembers::find()->where(['id'=> $member_id])->one();
        if(is_array($record_id)){
            foreach($record_id as $record){
                $record_file = PatientMemberFiles::find()->where(['id' => $record])->one();
                if(!empty($record_id)){
                    $record_file->delete();
                }

            }
        }
        else{
            $record_file = PatientMemberFiles::find()->where(['id' => $record_id])->one();
            if(!empty($record_id)){
                $record_file->delete();
            }
        }

        $response["status"] = 1;
        $response["error"] = false;
        $response['message']= 'success.';

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionExperienceDelete() {
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $experience_id = $params['id'];
        $experience = UserExperience::findOne($experience_id);
        if(!empty($experience))
        {
            $experience->delete();
            $response["status"] = 1;
            $response["error"] = false;
            $response['message']= 'Experience deleted successfully';
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Experience already deleted';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }
    public function actionEducationDelete(){
        $response = $data =  $required = $user= $lists= $list_a=array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        $education_id = $params['id'];
        $education = UserEducations::findOne($education_id);
        if(!empty($education))
        {
            $education->delete();
            $response["status"] = 1;
            $response["error"] = false;
            $response['message']= 'Education deleted successfully';
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Education already deleted';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionGetPatientAppointments(){
        $response = $data =  $required = $user= $lists= $list_a= $memberData= array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        $member_id=$params['member_id'];
        $member  = PatientMembers::find()->where(['id'=> $member_id])->one();
        if(!empty($member)){
            $appList= new Query();
            $appList=UserAppointment::find();
            $appList->where(['user_id'=>$member->user_id]);
            $appList->andWhere(['user_name'=>$member->name,'user_phone'=>$member->phone]);
            $appList->all();
            $command = $appList->createCommand();
            $lists = $command->queryAll();

            $appointments=array();
            if(!empty($lists)){
                $i=0;
                foreach($lists as $list){
                    $lista=UserAppointment::findOne($list['id']);
                    $appointments[$i]=DrsPanel::patientgetappointmentarray($lista);
                    $i++;
                }
            }

            $response["status"] = 1;
            $response["error"] = false;
            $response['data']=$appointments;
            $response['message']= 'success.';
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'Member not found';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionFavoriteUpsert(){
        $fields=ApiFields::favoriteUpsertFields();
        $response = $data  = $required = array();
        $post = Yii::$app->request->post();
        foreach($fields as  $field){
            if (array_key_exists($field,$post)){}
                else{ $required[]=$field;}
        }


        Yii::info($post, __METHOD__);
        if(empty($required)) {
            $data['user_id']=$post['user_id'];
            $data['profile_id']=$post['profile_id'];
            $data['status']=$post['status'];
            $upsert = DrsPanel::userFavoriteUpsert($data);
            if ($upsert){
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Favorite Successfully.';
            }
            else{
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Error.';
            }
        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }



    /**
     * @Param Null
     * @Function is used by doctor for block online appointment (Daily Patient Limit Screen)
     */
    public function actionUpdateShiftStatus(){
        $fields=ApiFields::updateShiftStatus();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $response=DrsPanel::updateShiftStatus($params);
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for getting patient history date wise on doctor side
     */
    public function actionPatientHistory(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                if(isset($params['date']) && $params['date'] != ''){
                    $date=$params['date'];
                }
                else{
                    $date=date('Y-m-d');
                }

                if(isset($params['schedule_id']) && $params['schedule_id'] != ''){
                    $current_selected=$params['schedule_id'];
                }
                else{
                    $current_selected=0;
                }

                $checkForCurrentShift=0;
                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                if(!empty($getSlots)){
                    $checkForCurrentShift=$getSlots[0]['schedule_id'];

                    if($current_selected == 0){
                        $current_selected = $checkForCurrentShift;
                    }
                    $getAppointments=DrsPanel::appointmentHistory($params['doctor_id'],$date,$current_selected,$getSlots,'');

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['data']=$getAppointments;
                    $response['data']['all_shifts']= DrsPanel::getDoctorAllShift($params['doctor_id'],$date, $checkForCurrentShift,$getSlots,$current_selected);
                    $response['message']= 'Today Appointments List';
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'No shifts available';
                }

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for getting user statistics data date wise on doctor side
     */
    public function actionUserStatisticsData(){
        $response = $data  = array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);
        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user)){
                if(isset($params['date']) && $params['date'] != ''){
                    $date=$params['date'];
                }
                else{
                    $date=date('Y-m-d');
                }

                if(isset($params['schedule_id']) && $params['schedule_id'] != ''){
                    $current_selected= (int)$params['schedule_id'];
                }
                else{
                    $current_selected=0;
                }

                if(isset($params['type']) && $params['type'] != ''){
                    $typewise=$params['type'];
                }
                else{
                    $typewise=UserAppointment::BOOKING_TYPE_ONLINE;
                }

                $checkForCurrentShift=0;
                $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                if(!empty($getSlots)){
                    $checkForCurrentShift=$getSlots[0]['schedule_id'];

                    if($current_selected == 0){
                        $current_selected = $checkForCurrentShift;
                    }
                    $getAppointments=DrsPanel::appointmentHistory($params['doctor_id'],$date,$current_selected,$getSlots,$typewise);

                    $response["status"] = 1;
                    $response["error"] = false;
                    $response['data']=$getAppointments;
                    $response['data']['all_shifts']= DrsPanel::getDoctorAllShift($params['doctor_id'],$date, $checkForCurrentShift,$getSlots,$current_selected);
                    $response['message']= 'Today Appointments List';
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'No shifts available';
                }

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    /**
     * @Param Null
     * @Function is used for deleting patient appointment history
     */
    public function actionDeletePatientHistory(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);


        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $doctor_id=$params['doctor_id'];
            $user=User::findOne(['id'=>$doctor_id]);
            if(!empty($user)){
                if((isset($params['from_date']) && !empty($params['from_date']))
                    && (isset($params['to_date']) && !empty($params['to_date']))){
                    $from_date=$params['from_date'];
                $to_date=$params['to_date'];

                $period = new \DatePeriod(
                    new \DateTime($from_date),
                    new \DateInterval('P1D'),
                    new \DateTime($to_date)
                    );

                foreach ($period as $key => $value) {
                    $date = $value->format('Y-m-d');
                    $getSlots=DrsPanel::getBookingShifts($params['doctor_id'],$date,$params['user_id']);
                    $getShiftSlots=array();

                    foreach($getSlots as $shift){
                        $getShiftSlots[]=$shift['schedule_id'];
                    }

                    $appointments = UserAppointment::find()->where(['date' => $date,'schedule_id'=>$getShiftSlots])->andWhere(['doctor_id'=>$doctor_id])->all();
                    foreach($appointments as $appointment){
                        $appointment->is_deleted=1;
                        $appointment->deleted_by='Doctor';
                        if($appointment->save()){
                            $addLog=Logs::appointmentLog($appointment->id,'Appointment deleted');
                        }
                    }
                }
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Appointments history deleted successfully';

            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Required parameter missing';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'User not found';
        }
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'UserId required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

    /**
     * @Param Null
     * @Function is used for export patient appointment history
     */
    public function actionExportPatientHistory(){
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);


        if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
            $doctor_id=$params['doctor_id'];
            $current_login=$params['user_id'];
            $user=User::findOne(['id'=>$doctor_id]);
            if(!empty($user)){
                if((isset($params['from_date']) && !empty($params['from_date']))
                    && (isset($params['to_date']) && !empty($params['to_date']))){
                    $from_date=$params['from_date'];
                $to_date=$params['to_date'];
                $export=DrsPanel::exportPatientHistoryExcel($doctor_id,$current_login,$from_date,$to_date);
                $response["status"] = 1;
                $response["error"] = false;
                $response['message']= 'Email send successfully';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'Required parameter missing';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'User not found';
        }
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'UserId required';
    }

    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}


    public function actionGetHospitalDoctors(){

        $response = $data = $lists =array();
        $params = Yii::$app->request->queryParams;
        Yii::info($params, __METHOD__);

        if(isset($params['user_id']) && !empty($params['user_id'])){
            $hospital_id=$params['user_id'];
            $lists=DrsPanel::myHospitalDoctors($hospital_id,'Confirm');

            $l=0; $list_a=array();
            foreach($lists as $list){
                $user=User::findOne($list);
                $profile=UserProfile::findOne($list);
                $groupid=$profile->groupid;

                $list_a[$l]['user_id']=$profile->user_id;
                $list_a[$l]['groupid']=$groupid;
                $list_a[$l]['name']=$profile->name;
                $list_a[$l]['profile_image']=Drspanel::getUserAvator($profile->user_id);
                $list_a[$l]['countrycode']=$user->countrycode;
                $list_a[$l]['phone']=$user->phone;
                $list_a[$l]['gender']=$profile->gender;
                $list_a[$l]['blood_group']=$profile->blood_group;
                $list_a[$l]['dob']=$profile->dob;
                if(!empty($profile->dob)){
                    $list_a[$l]['age']=Drspanel::getAge($profile->dob);
                }
                else{
                    $list_a[$l]['age']='';
                }
                $list_a[$l]['degree']=$profile->degree;
                $list_a[$l]['speciality']=$profile->speciality;
                $list_a[$l]['experience']=$profile->experience;
                $list_a[$l]['description']=$profile->description;
                $list_a[$l]['address']=Drspanel::getAddress($profile->user_id);
                $list_a[$l]['fees']= Drspanel::getProfileFees($profile->user_id);

                $list_a[$l]['show_fees']=DrsPanel::getUserSetting($profile->user_id,'show_fees');

                $rating=Drspanel::getRatingStatus($profile->user_id);
                $list_a[$l]['rating']=$rating['rating'];


                $l++;
            }
            $response["status"] = 1;
            $response["error"] = false;
            $response["data"]=$list_a;
            $response['message']= 'Doctors list';


        }else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message']= 'UserId Required';
        }

        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

public function actionGetBookingAddressShifts(){
    $response = $datameta  = $required = $logindUser=array();
    $params = Yii::$app->request->queryParams;
    Yii::info($params, __METHOD__);
    if(isset($params['user_id']) && !empty($params['user_id']) && isset($params['doctor_id']) && !empty($params['doctor_id']) ) {
        $userLogin=User::find()->where(['id'=>$params['user_id']])->one();
        $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
        if(!empty($doctor)){
            if(isset($params['date']) && !empty($params['date'])){
                $date= $params['date'];
            }else{
                $date= date('Y-m-d');
            }
            $getSlots=DrsPanel::getBookingAddressShifts($params['doctor_id'],$date,$params['user_id']);
            $datameta['date']=$date;
            $datameta['week']=DrsPanel::getDateWeekDay($date);
            $response["status"] = 1;
            $response["error"] = false;
            $response["data"]=$getSlots;
            $response['message'] = 'Success';

        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Something went wrong, Please try again.';
        }
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $response['message'] = 'Doctor id required';
    }
    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

public function actionGetShiftBookingDays(){
    $fields=ApiFields::shiftbookingdays();
    $response = $data =  $required = array();
    $params = Yii::$app->request->post();
    Yii::info($params, __METHOD__);

    foreach($fields as  $field){
        if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
    }
    if(empty($required)) {
        $doctor=User::find()->where(['id'=>$params['doctor_id']])->one();
        if(!empty($doctor)){
            $date= $params['next_date'];

            $getSlots=DrsPanel::getAddressShiftsDays($params);
            $datameta['date']=$date;
            $datameta['week']=DrsPanel::getDateWeekDay($date);
            $response["status"] = 1;
            $response["error"] = false;
            $response["data"]=$getSlots;
            $response['message'] = 'Success';

        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Something went wrong, Please try again.';
        }
    }
    else{
        $response["status"] = 0;
        $response["error"] = true;
        $fields_req= implode(',',$required);
        $response['message'] = $fields_req.' required';
    }
    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

function getFilterArray(){
    $groups_v=array();
    $l=0;
    $key=MetaKeys::findOne(['key'=>'speciality']);
    if(!empty($key)){
        $metavalues=MetaValues::find()->where(['key'=>$key->id])->all();
        $m=0;
        $groups_v[$l]['type']=$key->key;
        $groups_v[$l]['label']='Speciality';
        $groups_v[$l]['select_type']='multiple';
        $groups_v[$l]['list']=array();
        foreach($metavalues as $values){
            $groups_v[$l]['list'][$m]['id']=$values->id;
            $groups_v[$l]['list'][$m]['label']=$values->label;
            $groups_v[$l]['list'][$m]['value']=$values->value;
            $groups_v[$l]['list'][$m]['select_type']='multiple';
            $m++;
        }
        $l++;
    }
    $key=MetaKeys::findOne(['key'=>'treatment']);
    if(!empty($key)){
        $metavalues=MetaValues::find()->where(['key'=>$key->id])->all();
        $m=0;
        $groups_v[$l]['type']=$key->key;
        $groups_v[$l]['label']='Treatments';
        $groups_v[$l]['select_type']='multiple';
        $groups_v[$l]['list']=array();
        foreach($metavalues as $values){
            $groups_v[$l]['list'][$m]['id']=$values->id;
            $groups_v[$l]['list'][$m]['label']=$values->label;
            $groups_v[$l]['list'][$m]['value']=$values->value;
            $groups_v[$l]['list'][$m]['select_type']='multiple';
            $m++;
        }
        $l++;
    }

    $groups_v[$l]['type']='gender';
    $groups_v[$l]['label']='Gender';
    $groups_v[$l]['select_type']='single';
    $groups_v[$l]['list']=array();
    $m=0;
    $gender[0]=array('id'=>UserProfile::GENDER_MALE,'label'=>'Male');
    $gender[1]=array('id'=>UserProfile::GENDER_FEMALE,'label'=>'Female');
    $gender[2]=array('id'=>UserProfile::GENDER_OTHER,'label'=>'Other');
    foreach($gender as $values){
        $groups_v[$l]['list'][$m]['id']=$values['id'];
        $groups_v[$l]['list'][$m]['label']=$values['label'];
        $groups_v[$l]['list'][$m]['value']=$values['id'];
        $groups_v[$l]['list'][$m]['select_type']='single';
        $m++;
    }
    $l++;

    $groups_v[$l]['type']='rating';
    $groups_v[$l]['label']='Rating';
    $groups_v[$l]['select_type']='single';
    $groups_v[$l]['list']=array();

    $shift[0]=array('id'=>'0-1','label'=>'0-1');
    $shift[1]=array('id'=>'1-2','label'=>'1-2');
    $shift[2]=array('id'=>'2-3','label'=>'2-3');
    $shift[3]=array('id'=>'3-4','label'=>'3-4');
    $shift[4]=array('id'=>'4-5','label'=>'4-5');

    $m=0;
    foreach($shift as $values){
        $groups_v[$l]['list'][$m]['id']=$values['id'];
        $groups_v[$l]['list'][$m]['label']=$values['label'];
        $groups_v[$l]['list'][$m]['value']=$values['id'];
        $groups_v[$l]['list'][$m]['select_type']='single';
        $m++;
    }



    return $groups_v;
}

function getSortArray(){
    $groups_v=array();
    $groups_v[0]['type']='price';
    $groups_v[0]['label']='Price';
    $groups_v[0]['value']='high to low';
    $groups_v[1]['type']='price';
    $groups_v[1]['label']='Price';
    $groups_v[1]['value']='low to high';



    $groups_v[2]['type']='rating';
    $groups_v[2]['label']='Rating';
    $groups_v[2]['value']='high to low';
    $groups_v[3]['type']='rating';
    $groups_v[3]['label']='Rating';
    $groups_v[3]['value']='low to high';

    return $groups_v;
}

function titletype($groupid){
    if($groupid == Groups::GROUP_PATIENT){
        $lista=DrsPanel::prefixingList('patient');
        $list=array();
        $l=0;
        foreach($lista as $li){
            $list[$l]['value']=$li;
            $list[$l]['label']=$li;
            $l++;
        }
    }
    elseif($groupid == Groups::GROUP_DOCTOR){
        $lista=DrsPanel::prefixingList('doctor');
        $list=array();
        $l=0;
        foreach($lista as $li){
            $list[$l]['value']=$li;
            $list[$l]['label']=$li;
            $l++;
        }
    }
    else{
        $list=array();
    }
    return $list;
}

function getList($lists,$listtype='',$current_login = 0){
    $l=0; $list_a=array();
    foreach($lists as $list){
        $user=User::findOne($list['user_id']);
        $profile=UserProfile::findOne($list['user_id']);
        $groupid=$profile->groupid;

        $list_a[$l]['user_id']=$profile->user_id;
        $list_a[$l]['groupid']=$groupid;
        $list_a[$l]['name']=$profile->name;
        $list_a[$l]['user_verified']=$user->admin_status;
        $list_a[$l]['profile_image']=Drspanel::getUserAvator($profile->user_id);
        $list_a[$l]['countrycode']=$user->countrycode;
        $list_a[$l]['phone']=$user->phone;
        $list_a[$l]['gender']=$profile->gender;
        $list_a[$l]['blood_group']=$profile->blood_group;
        $list_a[$l]['dob']=$profile->dob;
        if(!empty($profile->dob)){
            $list_a[$l]['age']=Drspanel::getAge($profile->dob);
        }
        else{
            $list_a[$l]['age']='';
        }
        $list_a[$l]['degree']=$profile->degree;

        if($profile->groupid == Groups::GROUP_HOSPITAL){
            $details=DrsPanel::getMyHospitalSpeciality($profile->user_id);
            $list_a[$l]['speciality']=($details['speciality'])?explode(',',$details['speciality']):[];
            $list_a[$l]['treatments']=($details['treatments'])?explode(',',$details['treatments']):[];
        }
        else{
            $list_a[$l]['speciality']=$profile->speciality;
            $list_a[$l]['treatments']=$profile->treatment;
        }
        $list_a[$l]['experience']=$profile->experience;
        $list_a[$l]['description']=$profile->description;
        $list_a[$l]['address']=Drspanel::getAddress($profile->user_id);

        $list_a[$l]['address']=DrsPanel::getBookingAddressShifts($profile->user_id,date('Y-m-d'));

        $list_a[$l]['fees']= Drspanel::getProfileFees($profile->user_id);

        $list_a[$l]['show_fees']=DrsPanel::getUserSetting($profile->user_id,'show_fees');
        $rating=Drspanel::getRatingStatus($profile->user_id);
        $list_a[$l]['rating']=$rating['rating'];

        $lat=Drspanel::getLatLong($profile->user_id);
        $list_a[$l]['lat']=$lat['lat'];
        $list_a[$l]['lng']=$lat['lng'];

        if($listtype == 'hospital_doctors'){
            $list_a[$l]['status']=DrsPanel::sendRequestCheck($current_login,$profile->user_id);
        }

        $l++;
    }
    return $list_a;
}

function treatment($speciality,$user_id = ''){
    $arraytreat=array();
    $getId=DrsPanel::getIDOfMetaKey('speciality');
    $speciality_id=MetaValues::findOne(['key'=>$getId,'value'=>$speciality]);
    if(!empty($speciality_id)){
        $treatments=MetaValues::find()->where(['parent_key'=>$speciality_id->id])->all();
        $t=0;$all_active_values=array();
        foreach($treatments as $treat){
            $all_active_values[]= $treat->value;

            $arraytreat[$t]['id']=$treat->id;
            $arraytreat[$t]['value']=$treat->value;
            $arraytreat[$t]['label']=$treat->label;
            $t++;
        }

        if(!empty($user_id)){
            $profile=UserProfile::findOne($user_id);
            $treatments=$profile->treatment;
            if(!empty($treatments)){
                $treatments=explode(',',$treatments);
                foreach($treatments as $treatment){
                    if(!in_array($treatment,$all_active_values)){
                        $checkValue=MetaValues::find()->where(['parent_key'=>$speciality_id->id,'value'=>$treatment])->one();
                        if(!empty($checkValue)){
                            $arraytreat[$t]['id']=$checkValue->id;
                            $arraytreat[$t]['value']=$checkValue->value;
                            $arraytreat[$t]['label']=$checkValue->label;
                            $t++;
                        }
                    }
                }
            }
        }
        return $arraytreat;
    }
}

function getCurrentAffair($checkForCurrentShift,$doctor_id,$date,$shift_check='',$slots= array())
{
    $response = array();
    if ($checkForCurrentShift['status'] == 'error') {
        $response["status"] = 0;
        $response["error"] = true;
        $response['message'] = 'No Shifts for today';
    } elseif ($checkForCurrentShift['status'] == 'success') {
        $response["status"] = 1;
        $response["error"] = false;
        $response['schedule_id'] = $checkForCurrentShift['shift_id'];
        $response['shift_label'] = $checkForCurrentShift['shift_label'];
        $response['date'] = $date;
        $response['is_started'] = false;
        $response['is_completed'] = false;
        $response['all_shifts'] = DrsPanel::getDoctorAllShift($doctor_id, $date, $checkForCurrentShift['shift_id'], $slots, $shift_check);
        $response['data'] = [];
        $response['message'] = 'Shift not started';

    } else {
        $shift = $checkForCurrentShift['shift_id'];
        if ($shift_check == '') {
            $getAppointments = DrsPanel::getCurrentAppointmentsAffairs($doctor_id, $date, $shift);
        } elseif ($shift_check == $shift) {
            $getAppointments = DrsPanel::getCurrentAppointmentsAffairs($doctor_id, $date, $shift);
        } else {
            $getAppointments = array();
        }
        $response["status"] = 1;
        $response["error"] = false;
        if ($checkForCurrentShift['status'] == 'success_appointment_completed') {
            $response['is_started'] = false;
            $response['is_completed'] = true;
            $response['all_shifts'] = DrsPanel::getDoctorAllShift($doctor_id, $date, $checkForCurrentShift['shift_id'], $slots, $shift_check);
            $response['data'] = [];
            $response['message'] = 'Success';
        } else {
            $response['schedule_id'] = $checkForCurrentShift['shift_id'];
            $response['shift_label'] = $checkForCurrentShift['shift_label'];
            $response['date'] = $date;
            $response['is_started'] = true;
            $response['is_completed'] = false;
            $response['all_shifts'] = DrsPanel::getDoctorAllShift($doctor_id, $date, $checkForCurrentShift['shift_id'], $slots, $shift_check);
            $response['data'] = $getAppointments;
            $response['message'] = 'Appointment List';
        }

    }
    return $response;
}



    public function actionGetLiveStatus(){
        $fields=ApiFields::liveStatus();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $user=User::find()->where(['id'=>$params['doctor_id']])->one();
            if(!empty($user)){
                $scheduleGroup=UserScheduleGroup::find()->andWhere(['user_id'=>$params['doctor_id'],'schedule_id'=>$params['schedule_id'],'status'=>'current'])->one();
                if($scheduleGroup){
                    $appointment=DrsPanel::appointmentUsers($params['doctor_id'],$params['schedule_id'],$params['appointment_date']);
                    if($appointment){
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response["data"]=$appointment;
                        $response["appointment"]= ['doctor_status' => 1,'doctor_sheet_status' => 1,'approximate_time' => 20];
                        $response['message'] = 'Success';
                    }else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Something went wrong, Please try again.';
                    }
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Something went wrong, Please try again.';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionDeleteShiftForDays(){
        $fields=ApiFields::deleteShift();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
                else{ $required[]=$field;}
        }
        if(empty($required)) {
            $schedule_ids = explode(',', $params['schedule_id']);
            $deleteAllShift =  DrsPanel::deleteShiftForDays($params['doctor_id'],$schedule_ids);
            $response["status"] = 1;
            $response["error"] = false;
            $response['message'] = 'Shift Deleted Successfully';
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionEditShiftForDate(){
    $fields=ApiFields::todayTimingShift();
    $response = $data =  $required = array();
    $params = Yii::$app->request->post();
    Yii::info($params, __METHOD__);
    foreach($fields as  $field){
        if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
    }
    if(empty($required))
    {
        if(isset($params['user_id']) && $params['user_id'] != '')
        {
            $user=User::findOne(['id'=>$params['user_id']]);
            if(!empty($user))
            {
                $canAddEdit = true;
                $msg = ' invalid';
                if(isset($params['address_id']) && isset($params['shift_id']) && isset($params['date']) && isset($params['schedule_id']))
                {
                    $weekday=DrsPanel::getDateWeekDay($params['date']);
                    $schedule= UserSchedule::findOne($params['schedule_id']);
                    if(!empty($schedule))
                    {
                        $dayShiftsFromDb=UserScheduleDay::find()->where(['user_id' =>$params['user_id']])->andwhere(['address_id' => $params['address_id'],'schedule_id' => $schedule->id])->andwhere(['!=','id',$params['shift_id']])->andwhere(['date' => $params['date']])->all();

                        if(!empty($dayShiftsFromDb))
                        {
                            foreach ($dayShiftsFromDb as $key => $dayshiftValuedb) 
                            {
                                $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);

                                $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);

                                $nstart_time = $dbstart_time.' '.$params['start_time'];

                                $nend_time = $dbend_time.' '.$params['end_time'];

                                $startTimeClnt = strtotime($nstart_time);

                                $endTimeClnt = strtotime($nend_time);

                                $startTimeDb =$dayshiftValuedb->start_time;

                                $endTimeDb = $dayshiftValuedb->end_time;

                                if($startTimeClnt >= $startTimeDb && $startTimeClnt <= $endTimeDb)
                                {
                                    $canAddEdit = false;
                                    $msg = ' already exists';
                                }
                                elseif($endTimeClnt >= $startTimeDb && $endTimeClnt <= $endTimeDb)
                                {
                                    $canAddEdit = false;
                                }
                                elseif($startTimeDb >= $startTimeClnt && $startTimeDb <= $endTimeClnt)
                                {
                                    $canAddEdit = false;
                                }
                                elseif($endTimeDb >= $startTimeClnt && $endTimeDb <= $endTimeClnt)
                                {
                                    $canAddEdit = false;
                                }
                                elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb)
                                {
                                    $canAddEdit = false;
                                }
                                if($canAddEdit==false) {
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = [];
                                    $response['message']= 'Shift '.date('h:i a',$startTimeClnt). ' - ' .date('h:i a',$endTimeClnt).' on '.$dayshiftValuedb->weekday.$msg;
                                    Yii::info($response, __METHOD__);
                                    \Yii::$app->response->format = Response::FORMAT_JSON;
                                    return $response;
                                }
                            }
                        } 
                    }
                    else
                    {
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message']= 'Schedule not found';  
                    }
                }
                else
                {
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'Required field missing';
                }
                if($canAddEdit == true)  
                {
                    $schedulegroup = UserScheduleGroup::findOne($params['schedule_id']);
                    if(!empty($schedulegroup))
                    {
                        $schedulegroup->load(['UserScheduleGroup' => $params]);
                        $schedulegroup->address_id= $params['address_id'];
                       
                         $schedulegroup->shift_label='Shift '.$params['start_time'].' - '.$params['end_time'];
                        if($schedulegroup->save())
                        {
                            $scheduleDay=UserScheduleDay::find()->where(['schedule_id'=>$params['schedule_id'],'date'=>$params['date'],'user_id'=>$params['user_id']])->one();
                            if(empty($schedulegroup)){
                                $schedulegroup=new UserScheduleDay();
                            }
                          
                            $scheduleDay->user_id=$schedule->user_id;
                            $scheduleDay->schedule_id=$schedule->id;
                            $scheduleDay->shift_belongs_to=$schedule->shift_belongs_to;
                            $scheduleDay->attender_id=$schedule->attender_id;
                            $scheduleDay->hospital_id=$schedule->hospital_id;
                            $scheduleDay->address_id=$schedule->address_id;
                            $scheduleDay->shift=(string)$schedule->shift;
                            $scheduleDay->start_time= strtotime($params['start_time']);
                            $scheduleDay->end_time= strtotime($params['end_time']);
                            $scheduleDay->patient_limit=$params['patient_limit'];
                            $scheduleDay->appointment_time_duration=$params['appointment_time_duration'];
                            $scheduleDay->consultation_fees=$params['consultation_fees'];
                            $scheduleDay->emergency_fees=$params['emergency_fees'];
                            $scheduleDay->consultation_fees_discount=$params['consultation_fees_discount'];
                            $scheduleDay->emergency_fees_discount=$params['emergency_fees_discount'];
                            $scheduleDay->date=$params['date'];
                            $scheduleDay->weekday=$weekday;
                            $scheduleDay->status='pending';
                            $scheduleDay->booking_closed=1;
                            $scheduleDay->save();
                            UserScheduleSlots::deleteAll(['schedule_id' => $params['schedule_id'],'date'=>$params['date']]);
                        }
                        $response["status"] = 1;
                        $response["error"] = false;
                        $response['message'] = 'Today Shift Updated Successfully';
                    }
                    else
                    {
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Shift Id Not Found';
                    }
                }

            }else {
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'User Not Found';
            } 
        }else {
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'User Not Found';
        }
    }else {
        $response["status"] = 0;
        $response["error"] = true;
        $fields_req= implode(',',$required);
        $response['message'] = $fields_req.' required';
    }
    Yii::info($response, __METHOD__);
    \Yii::$app->response->format = Response::FORMAT_JSON;
    return $response;
}

    public function actionAddShiftWithAddress(){
        $fields=ApiFields::addShiftFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
        }
        if(empty($required)){
            //echo "<pre>"; print_r($params);die;
            if(isset($params['user_id']) && $params['user_id'] != ''){
                $user=User::findOne(['id'=>$params['user_id']]);
                if(!empty($user)){
                    $user_ids=$params['user_id'];
                    $addAddress=new UserAddress();
                    $data['UserAddress']['user_id']=$params['user_id'];
                    $data['UserAddress']['name']=$params['name'];
                    $data['UserAddress']['city']=$params['city'];
                    $data['UserAddress']['state']=$params['state'];
                    $data['UserAddress']['address']=$params['address'];
                    $data['UserAddress']['area']=$params['area'];
                    $data['UserAddress']['phone']=$params['mobile'];
                    $data['UserAddress']['landline']=isset($params['landline'])?$params['landline']:'';
                    $data['UserAddress']['lat']=isset($params['latitude'])?$params['latitude']:'';
                    $data['UserAddress']['lng']=isset($params['longitude'])?$params['longitude']:'';
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);


                    if((isset($params['dayShifts']) && !empty($params['dayShifts']))) {
                        $shiftsarray=json_decode($params['dayShifts']);
                        $postmeta=array();
                        foreach($shiftsarray as $key=>$shift){
                            $postmeta['weekday'][$key]=$shift->weekday;
                            $postmeta['start_time'][$key]=$shift->shiftList->start_time;
                            $postmeta['end_time'][$key]=$shift->shiftList->end_time;
                            $postmeta['appointment_time_duration'][$key]=$shift->shiftList->appointment_time_duration;
                            $postmeta['consultation_fees'][$key]=$shift->shiftList->consultation_fees;
                            $postmeta['emergency_fees'][$key]=$shift->shiftList->emergency_fees;
                            $postmeta['consultation_fees_discount'][$key]=$shift->shiftList->consultation_fees_discount;
                            $postmeta['emergency_fees_discount'][$key]=$shift->shiftList->emergency_fees_discount;
                        }

                        $shift=array();
                        $shiftcount=$postmeta['start_time'];
                        $canAddEdit = true;$msg = ' invalid';
                        $errorIndex = 0;$newInsertIndex = 0;
                        $errorShift = array();$insertShift = array();
                        $newshiftInsert =0;$insertShift = array();
                        $addAddress->load(Yii::$app->request->post());
                        $addAddress->user_id = $user_ids;
                        $upload = UploadedFile::getInstance($addAddress, 'image');
                        $userAddressLastId  = UserAddress::find()->orderBy(['id'=> SORT_DESC])->one();
                        $countshift =  count($shiftcount);
                        $newshiftcheck=array(); $errormsgloop=array();
                        $nsc=0; $error_msg=0;

                        if(!empty($postmeta)){
                            foreach ($postmeta['weekday'] as $keyClnt => $day_shift) {
                                foreach ($day_shift as $keydata => $value) {
                                    $dayShiftsFromDb=UserSchedule::find()->where(['user_id' =>$user_ids])->andwhere(['weekday' => $value])->all();
                                    if(!empty($dayShiftsFromDb)) {
                                        foreach ($dayShiftsFromDb as $keydb => $dayshiftValuedb) {
                                            $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);
                                            $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);
                                            $nstart_time = $dbstart_time.' '.$postmeta['start_time'][$keyClnt];
                                            $nend_time = $dbend_time.' '.$postmeta['end_time'][$keyClnt];
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
                                                    /*elseif($endtime_check > $startTimeClnt && $endtime_check <= $endTimeClnt) {
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
                                                    }*/

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
                                                $errormsgloop[$error_msg]['message']= ' already exists';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' already exists';
                                            } elseif ($startTimeClnt > $startTimeDb && $startTimeClnt < $endTimeDb) {
                                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                $errormsgloop[$error_msg]['weekday']= $value;
                                                $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' msg1';
                                            } elseif ($endTimeClnt > $startTimeDb && $endTimeClnt < $endTimeDb) {
                                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                $errormsgloop[$error_msg]['weekday']= $value;
                                                $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' msg2';
                                            } elseif ($startTimeDb > $startTimeClnt && $startTimeDb < $endTimeClnt) {
                                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                $errormsgloop[$error_msg]['weekday']= $value;
                                                $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' msg3';
                                            } elseif ($endTimeDb > $startTimeClnt && $endTimeDb < $endTimeClnt) {
                                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                $errormsgloop[$error_msg]['weekday']= $value;
                                                $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' msg4';
                                            }

                                           /*elseif ($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb) {
                                                $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                $errormsgloop[$error_msg]['weekday']= $value;
                                                $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                $canAddEdit = false;
                                                $errorIndex++;$error_msg++;
                                                $msg = ' msg5';
                                            }*/
                                           else {
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
                                            $insertShift[$newInsertIndex] = DrsPanel::loadShiftData($user_ids,$keyClnt,$postmeta,$value,$countshift= NULL);
                                            $newInsertIndex++;
                                        }

                                    }
                                    else{
                                        $dbstart_time=date('Y-m-d');
                                        $nstart_time = $dbstart_time.' '.$postmeta['start_time'][$keyClnt];
                                        $nend_time = $dbstart_time.' '.$postmeta['end_time'][$keyClnt];
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
                                                }*/
                                            }
                                        }
                                        if($canAddEdit==true) {
                                            $nsc_add = $nsc++;
                                            $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                            $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                            $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                            $newshiftcheck[$nsc_add]['weekday'] = $value;
                                            $insertShift[$newInsertIndex] = DrsPanel::loadShiftData($user_ids,$keyClnt,$postmeta,$value,$countshift = NULL);
                                            $newInsertIndex++;
                                        }
                                    }
                                }
                            }

                            if($canAddEdit==false || !empty($errormsgloop)) {
                                if(!empty($errormsgloop)){
                                    $html=array(); $remove_duplicate=array();
                                    $weekdaysl=array();
                                    foreach($errormsgloop as $msgloop){
                                        $keyshifts=$msgloop['shift'];
                                        if(!in_array($keyshifts.'-'.$msgloop['weekday'], $remove_duplicate)){
                                            $remove_duplicate[]=$keyshifts.'-'.$msgloop['weekday'];
                                            $weekdaysl[$keyshifts][]=$msgloop['weekday'];
                                            $html[$keyshifts]='Shift time '.date('h:i a',$msgloop['start_time']). ' - ' .date('h:i a',$msgloop['end_time']).' on '.implode(',',$weekdaysl[$keyshifts]).' '.$msgloop['message'];
                                        }
                                    }
                                    $error_msg=implode(" , ", $html);
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = '';
                                    $response['message']= $error_msg;
                                    Yii::info($response, __METHOD__);
                                    \Yii::$app->response->format = Response::FORMAT_JSON;
                                    return $response;
                                }
                                else{
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = '';
                                    $response['message']= 'Shift time invalid';
                                    Yii::info($response, __METHOD__);
                                    \Yii::$app->response->format = Response::FORMAT_JSON;
                                    return $response;
                                }

                            }
                            elseif($canAddEdit == true) {
                                if($addAddress->save()) {
                                    $imageUpload='';
                                    if (isset($_FILES['image'])){
                                        $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                                    }
                                    if (isset($_FILES['images'])){
                                        $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'images');
                                    }
                                    if(!empty($insertShift)) {
                                        foreach ($insertShift as $key => $value) {
                                            $saveScheduleData = new UserSchedule();
                                            $saveScheduleData->load(['UserSchedule'=>$value['AddScheduleForm']]);
                                            $saveScheduleData->address_id= $addAddress->id;
                                            $saveScheduleData->start_time= strtotime($value['AddScheduleForm']['start_time']);
                                            $saveScheduleData->end_time= strtotime($value['AddScheduleForm']['end_time']);
                                            $saveScheduleData->save();
                                        }
                                    }
                                    //add shift keys to user_schedule table
                                    $shifts_keys=Drspanel::addUpdateShiftKeys($user_ids);
                                    $response["status"] = 1;
                                    $response["error"] = false;
                                    $response["data"] = '';
                                    $response['message']= 'Shift Added Successfully';
                                }
                                else{
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = $addAddress->getErrors();
                                    $response['message']= 'Please try again';
                                }
                            }
                            else{
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message']= 'Please try again';
                            }
                        }
                        else{
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message']= 'Please add atleast one shift';
                        }
                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message']= 'Please add atleast one shift';
                    }
                }
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }

    public function actionEditShiftWithAddress(){
        $fields=ApiFields::addShiftFields();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);
        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
        }

        if(empty($required)){
            if(isset($params['user_id']) && $params['user_id'] != ''){
                $user=User::findOne(['id'=>$params['user_id']]);
                if(!empty($user)){
                    $user_ids=$params['user_id'];
                    $addAddress=UserAddress::findOne($params['address_id']);
                    if(empty($addAddress)){
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Invalid Address';
                        Yii::info($response, __METHOD__);
                        \Yii::$app->response->format = Response::FORMAT_JSON;
                        return $response;
                    }

                    $data['UserAddress']['user_id']=$params['user_id'];
                    $data['UserAddress']['name']=$params['name'];
                    $data['UserAddress']['city']=$params['city'];
                    $data['UserAddress']['state']=$params['state'];
                    $data['UserAddress']['address']=$params['address'];
                    $data['UserAddress']['area']=$params['area'];
                    $data['UserAddress']['phone']=$params['mobile'];
                    $data['UserAddress']['landline']=isset($params['landline'])?$params['landline']:'';
                    $data['UserAddress']['lat']=isset($params['latitude'])?$params['latitude']:'';
                    $data['UserAddress']['lng']=isset($params['longitude'])?$params['longitude']:'';
                    $data['UserAddress']['is_request']=0;
                    $addAddress->load($data);

                    if((isset($params['dayShifts']) && !empty($params['dayShifts']))) {
                        $shiftsarray=json_decode($params['dayShifts']);
                        $postmeta=array();$postmeta_shift=array();
                        foreach($shiftsarray as $key=>$shift){
                            $postmeta['weekday'][$key]=$shift->weekday;
                            $postmeta['start_time'][$key]=$shift->shiftList->start_time;
                            $postmeta['end_time'][$key]=$shift->shiftList->end_time;
                            $postmeta['appointment_time_duration'][$key]=$shift->shiftList->appointment_time_duration;
                            $postmeta['consultation_fees'][$key]=$shift->shiftList->consultation_fees;
                            $postmeta['emergency_fees'][$key]=$shift->shiftList->emergency_fees;
                            $postmeta['consultation_fees_discount'][$key]=$shift->shiftList->consultation_fees_discount;
                            $postmeta['emergency_fees_discount'][$key]=$shift->shiftList->emergency_fees_discount;
                            if(isset($shift->shiftList->id)){
                                $postmeta_shift['shift_ids'][$key]=(array)$shift->shiftList->id;
                            }
                        }
                        $shift=array();
                        $shiftcount=$postmeta['start_time'];
                        $canAddEdit = true;$msg = ' invalid';
                        $errorIndex = 0;$newInsertIndex = 0;
                        $errorShift = array();$insertShift = array();
                        $newshiftInsert =0;$insertShift = array();
                        $addAddress->load(Yii::$app->request->post());
                        $addAddress->user_id = $user_ids;
                        $upload = UploadedFile::getInstance($addAddress, 'image');
                        $userAddressLastId  = UserAddress::find()->orderBy(['id'=> SORT_DESC])->one();
                        $countshift =  count($shiftcount);
                        $newshiftcheck=array(); $errormsgloop=array();
                        $nsc=0; $error_msg=0;

                        if(!empty($postmeta)){
                            foreach ($postmeta['weekday'] as $keyClnt => $day_shift) {
                                if(!empty($day_shift)){
                                    foreach ($day_shift as $keydata => $value) {

                                        if(isset($postmeta_shift['shift_ids']) && isset($postmeta_shift['shift_ids'][$keyClnt]) && isset($postmeta_shift['shift_ids'][$keyClnt][$value])){
                                            $existing_shift=UserSchedule::findOne($postmeta_shift['shift_ids'][$keyClnt][$value]);
                                        }
                                        else{
                                            $existing_shift=array();
                                        }
                                        $dayShiftsFromDb=UserSchedule::find()->where(['user_id' =>$user_ids])->andwhere(['weekday' => $value])->all();
                                        if(!empty($dayShiftsFromDb)) {
                                            foreach ($dayShiftsFromDb as $keydb => $dayshiftValuedb) {
                                                $dbstart_time = date('Y-m-d',$dayshiftValuedb->start_time);
                                                $dbend_time = date('Y-m-d',$dayshiftValuedb->end_time);
                                                $nstart_time = $dbstart_time.' '.$postmeta['start_time'][$keyClnt];
                                                $nend_time = $dbend_time.' '.$postmeta['end_time'][$keyClnt];
                                                $startTimeClnt = strtotime($nstart_time);
                                                $endTimeClnt = strtotime($nend_time);
                                                $startTimeDb =$dayshiftValuedb->start_time;
                                                $endTimeDb = $dayshiftValuedb->end_time;


                                                if(!empty($existing_shift) && $existing_shift->id == $dayshiftValuedb->id){
                                                    if($startTimeClnt == $startTimeDb && $endTimeClnt == $endTimeDb) {
                                                        $nsc_add = $nsc++;
                                                        $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                                        $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                                        $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                                        $newshiftcheck[$nsc_add]['weekday'] = $value;
                                                        $canAddEdit = true;
                                                        break;
                                                    }
                                                    elseif($startTimeClnt > $endTimeClnt){
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= '(end time should be greater than start time)';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' (end time should be greater than start time)';
                                                    }
                                                    elseif($startTimeClnt == $endTimeClnt) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= '(start time & end time should not be same)';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' (start time & end time should not be same)';
                                                    }
                                                }
                                                else{
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
                                                           /* elseif($starttime_check >= $startTimeClnt && $starttime_check <= $endTimeClnt) {
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
                                                            }*/

                                                        }
                                                    }

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
                                                    elseif ($startTimeClnt == $endTimeClnt) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= '(start time & end time should not be same)';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' (start time & end time should not be same)';
                                                    }
                                                    elseif($startTimeClnt == $startTimeDb && $endTimeClnt == $endTimeDb) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is already exists';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' already exists';
                                                    }
                                                    elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' msg1';
                                                    }
                                                    elseif($endTimeClnt > $startTimeDb && $endTimeClnt <= $endTimeDb) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' msg2';
                                                    }
                                                    elseif($startTimeDb >= $startTimeClnt && $startTimeDb < $endTimeClnt) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' msg3';
                                                    }
                                                    elseif($endTimeDb > $startTimeClnt && $endTimeDb <= $endTimeClnt) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' msg4';
                                                    }
                                                    elseif($startTimeClnt >= $startTimeDb && $startTimeClnt < $endTimeDb) {
                                                        $errormsgloop[$error_msg]['start_time']=$startTimeClnt;
                                                        $errormsgloop[$error_msg]['end_time']= $endTimeClnt;
                                                        $errormsgloop[$error_msg]['shift']= $keyClnt;
                                                        $errormsgloop[$error_msg]['weekday']= $value;
                                                        $errormsgloop[$error_msg]['message']= 'is invalid time';
                                                        $canAddEdit = false;
                                                        $errorIndex++;$error_msg++;
                                                        $msg = ' msg5';
                                                    }
                                                    else {
                                                        if($canAddEdit==true) {
                                                            $nsc_add = $nsc++;
                                                            $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                                            $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                                            $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                                            $newshiftcheck[$nsc_add]['weekday'] = $value;
                                                        }
                                                    }
                                                }
                                            }
                                            if($canAddEdit==true) {
                                                $insertShift[$newInsertIndex] = DrsPanel::loadShiftData($user_ids,$keyClnt,$postmeta,$value,$countshift= NULL);
                                                if(!empty($existing_shift)){
                                                    $insertShift[$newInsertIndex]['AddScheduleForm']['id']=$existing_shift->id;
                                                }
                                                $newInsertIndex++;
                                            }
                                        }
                                        else{
                                            $dbstart_time=date('Y-m-d');
                                            $nstart_time = $dbstart_time.' '.$postmeta['start_time'][$keyClnt];
                                            $nend_time = $dbstart_time.' '.$postmeta['end_time'][$keyClnt];
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


                                            if($canAddEdit==true) {
                                                $nsc_add = $nsc++;
                                                $newshiftcheck[$nsc_add]['start_time'] = $startTimeClnt;
                                                $newshiftcheck[$nsc_add]['end_time'] = $endTimeClnt;
                                                $newshiftcheck[$nsc_add]['weekday'] = $value;
                                                $newshiftcheck[$nsc_add]['keyclnt'] = $keyClnt;
                                                $insertShift[$newInsertIndex] = DrsPanel::loadShiftData($user_ids,$keyClnt,$postmeta,$value,$countshift = NULL);
                                                if(!empty($existing_shift)){
                                                    $insertShift[$newInsertIndex]['AddScheduleForm']['id']=$existing_shift->id;
                                                }
                                                $newInsertIndex++;
                                            }
                                        }
                                    }
                                }
                                if($canAddEdit==false) {
                                    break;
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
                                    $error_msg=implode(" , ", $html);
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = '';
                                    $response['message']= $error_msg;
                                    Yii::info($response, __METHOD__);
                                    \Yii::$app->response->format = Response::FORMAT_JSON;
                                    return $response;
                                }
                                else{
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = '';
                                    $response['message']= 'Shift time invalid';
                                    Yii::info($response, __METHOD__);
                                    \Yii::$app->response->format = Response::FORMAT_JSON;
                                    return $response;
                                }
                            }
                            elseif($canAddEdit == true) {
                                $errores=array();
                                if($addAddress->save()) {
                                //delete images
                                    if(isset($params['deletedImages'])){
                                        $deletedImages=json_decode($params['deletedImages']);
                                        foreach ($deletedImages as $key_del => $value_del) {
                                            $deleteAddressimg = UserAddressImages::findOne($value_del);
                                                if(!empty($deleteAddressimg)){
                                                    $deleteAddressimg->delete();
                                                }
                                        }
                                    }

                                    $imageUpload='';
                                    if (isset($_FILES['image'])){
                                        $imageUpload=DrsImageUpload::updateAddressImage($addAddress->id,$_FILES);
                                    }
                                    if (isset($_FILES['images'])){
                                        $imageUpload=DrsImageUpload::updateAddressImageList($addAddress->id,$_FILES,'images');
                                    }
                                    if(!empty($insertShift)) {
                                        $oldshift_ids=array();
                                        $currentshift_ids=array();
                                        if(isset($postmeta_shift['shift_ids'])){
                                            foreach($postmeta_shift['shift_ids'] as $keyids => $valueids){
                                                foreach($valueids as $valueid){
                                                    $oldshift_ids[]=$valueid;
                                                }

                                            }
                                        }

                                        foreach ($insertShift as $key => $value) {
                                            if(isset($value['AddScheduleForm']['id'])){
                                                $currentshift_ids[]=$value['AddScheduleForm']['id'];
                                                $saveScheduleData = UserSchedule::findOne($value['AddScheduleForm']['id']);
                                                $old_insert=1;
                                                $olddata['id']=$saveScheduleData->id;
                                                $olddata['start_time']=$saveScheduleData->start_time;
                                                $olddata['end_time']=$saveScheduleData->end_time;
                                                $olddata['appointment_time_duration']= $saveScheduleData->appointment_time_duration;
                                                $olddata['consultation_fees']= $saveScheduleData->consultation_fees;
                                                $olddata['emergency_fees']= $saveScheduleData->emergency_fees;
                                                $olddata['consultation_fees_discount']= $saveScheduleData->consultation_fees_discount;
                                                $olddata['emergency_fees_discount']= $saveScheduleData->emergency_fees_discount;
                                            }
                                            else{
                                                $saveScheduleData = new UserSchedule();
                                                $old_insert=0;
                                            }
                                            $saveScheduleData->load(['UserSchedule'=>$value['AddScheduleForm']]);
                                            $saveScheduleData->address_id= $addAddress->id;
                                            $saveScheduleData->start_time= strtotime($value['AddScheduleForm']['start_time']);
                                            $saveScheduleData->end_time= strtotime($value['AddScheduleForm']['end_time']);
                                            if($saveScheduleData->save()){
                                                if($old_insert == 1){
                                                    $checkandcleardata=Drspanel::oldShiftsDataUpdate($olddata,$value);
                                                }
                                            }
                                            else{
                                                $errores[$key]=$saveScheduleData->getErrors();
                                            }
                                        }
                                    }
                                    if(!empty($oldshift_ids)){
                                        foreach($oldshift_ids as $id_check){
                                            if(in_array($id_check,$currentshift_ids)){

                                            }
                                            else{
                                                //delete shift with all slots & its respective appointments to be cancelled
                                                $statusarray=array('pending','available','active','deactivate','skip','booked');
                                                $appointments=UserAppointment::find()->where(['schedule_id'=>$id_check,'status'=> $statusarray])->all();
                                                if(!empty($appointments)){
                                                    foreach($appointments as $appointment){
                                                        $appointment->status=UserAppointment::STATUS_CANCELLED;
                                                        $appointment->is_deleted=1;
                                                        $appointment->deleted_by='Doctor';
                                                        if($appointment->save()){
                                                            $addLog=Logs::appointmentLog($appointment->id,'Appointment cancelled by doctor');
                                                        }
                                                    }
                                                }
                                                $deleteschedule = UserSchedule::findOne($id_check);
                                                if(!empty($deleteschedule)){
                                                    $deleteschedule->delete();
                                                }


                                                $deleteDateSchedule=UserScheduleDay::deleteAll(['schedule_id'=>$id_check]);
                                                $deleteGroupSchedule=UserScheduleGroup::deleteAll(['schedule_id'=>$id_check]);
                                                $deleteSlotsSchedule=UserScheduleSlots::deleteAll(['schedule_id'=>$id_check]);

                                            }
                                        }
                                    }
                                    $shifts_keys=Drspanel::addUpdateShiftKeys($user_ids);
                                    $response["status"] = 1;
                                    $response["error"] = false;
                                    $response["data"] = '';
                                    $response['message']= 'Shift Updated Successfully';
                                }
                                else{
                                    $response["status"] = 0;
                                    $response["error"] = true;
                                    $response["data"] = $addAddress->getErrors();
                                    $response['message']= 'Please try again';
                                }
                            }
                            else{
                                $response["status"] = 0;
                                $response["error"] = true;
                                $response['message']= 'Please try again';
                            }
                        }
                        else{
                            $response["status"] = 0;
                            $response["error"] = true;
                            $response['message']= 'Please add atleast one shift';
                        }

                    }
                    else{
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message']= 'Please add atleast one shift';
                    }
                }
                else{
                    $response["status"] = 0;
                    $response["error"] = true;
                    $response['message']= 'User not found';
                }
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response['message']= 'User not found';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }

    public function actionDeleteShiftAddress(){
        $fields=ApiFields::deleteShiftAddress();
        $response = $data =  $required = array();
        $params = Yii::$app->request->post();
        Yii::info($params, __METHOD__);

        foreach($fields as  $field){
            if (array_key_exists($field,$params)){}
            else{ $required[]=$field;}
        }
        if(empty($required)) {
            $address_id = $params['address_id'];
            $address_delete =  DrsPanel::deleteAddresswithShifts($params['doctor_id'],$address_id);
            $response["status"] = $address_delete['status'];
            $response["error"] = $address_delete['error'];
            $response['message'] = $address_delete['message'];
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $fields_req= implode(',',$required);
            $response['message'] = $fields_req.' required';
        }
        Yii::info($response, __METHOD__);
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


}