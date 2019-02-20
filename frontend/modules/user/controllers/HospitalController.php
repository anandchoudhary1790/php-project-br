<?php
namespace frontend\modules\user\controllers;

use backend\models\AddScheduleForm;
use common\components\DrsImageUpload;
use common\models\MetaKeys;

use common\models\UserSchedule;
use common\models\UserScheduleSlots;
use frontend\models\AppointmentForm;
use Yii;
use yii\authclient\AuthAction;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserProfile;
use common\models\UserRequest;
use common\models\Groups;
use backend\models\AttenderForm;
use backend\models\AttenderEditForm;
use common\components\DrsPanel;
use common\models\UserAppointment;
use common\models\UserAddress;
use common\models\UserAboutus;
use common\models\MetaValues;
use frontend\modules\user\models\SignupForm;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\db\Query;





/**
 * Class HospitalController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class HospitalController extends \yii\web\Controller
{

    /**
     * @return array
     */
    private $loginUser;

    public function actions()
    {
        return [
            'oauth' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'successOAuthCallback']
            ]
        ];
    }

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
                            return $this->loginUser->groupid==Groups::GROUP_HOSPITAL;
                        }
                    ],
                ]
            ]
        ];
    }

    public function actionProfile(){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $groupid = Groups::GROUP_HOSPITAL;
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $userAddress=UserAddress::findOne(['user_id'=>$id]);
        $userAboutus=UserAboutus::findOne(['user_id'=>$id]);
        $genderlist=[UserProfile::GENDER_MALE=>'Male',UserProfile::GENDER_FEMALE=>'Female'];
        if (Yii::$app->request->isAjax) {
            $userProfile->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($userProfile);
        }
        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            if(isset($post['UserProfile']['speciality']) &&  !empty($post['UserProfile']['speciality'])) {
                $Userspecialities=$post['UserProfile']['speciality'];
                $Usertreatments=$post['UserProfile']['treatment'];
                if(!empty($Userspecialities)){
                    $metakey_speciality=MetaKeys::findOne(['key'=>'speciality']);
                    $getSpecilaity= MetaValues::find()->where(['key'=>$metakey_speciality->id,'value'=>$Userspecialities])->one();
                    if(!empty($Usertreatments)){
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
                                $treatmentModel->status = 1;
                                $treatmentModel->save();
                            }
                        }
                    }
                    else{
                        $post['UserProfile']['treatment']='';
                    }
                }
                $modelUpdate= UserProfile::upsert($post,$id,$groupid);
                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', "'Speciality/Treatments Updated'");
                    return $this->redirect(['/hospital/profile']);
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
                        $servicesModel->status = 1;
                        $servicesModel->save();
                    }
                }
                $modelUpdate= UserProfile::upsert($post,$id,$groupid);
                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', "'Services Updated'");
                    return $this->redirect(['/hospital/profile']);
                }else{
                    Yii::$app->session->setFlash('error', "'Sorry hospital Facility Not Added'");

                }
            }
        }

        $specialityList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        $speciality=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>5])->all();
        $treatment=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>9])->all();

        $treatmentList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        $profilepercentage=DrsPanel::profiledetails($userModel,$userProfile,$groupid);

        $servicesList=UserProfile::find()->andWhere(['user_id'=>$id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        $services=DrsPanel::getMetaData('services',$id);
        return $this->render('/hospital/profile',
            ['userModel'=>$userModel,'userProfile'=>$userProfile,'speciality' => $speciality,'specialityList' => $specialityList,'treatments' =>$treatment,'treatmentList' =>$treatmentList,'services' => $services,'servicesList' => $servicesList,'useraddressList' => $userAddress,'userAboutus'=>$userAboutus]);
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

                echo $this->renderAjax('/hospital/ajax-treatment-list',['form'=>$form,'treatment_list'=>$treatment_list,'userProfile'=>$userProfile]); exit();
            }
        }

    }

    public function actionEditProfile(){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $userAddress=UserAddress::findOne(['user_id'=>$id]);
        if(empty($userAddress)) {
            $userAddress=new UserAddress();
            $userAddress->type ='Hospital';
            $userAddress->user_id =$id;
        }
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $userModel->load($post);
            $old_image = $userProfile->avatar;
            $userProfile->load($post);
            $userProfile->avatar= $old_image;
            $userProfile->gender=0;

            if($userModel->groupUniqueNumber(['phone'=>$post['User']['phone'],'groupid'=>$userModel->groupid,'id'=>$userModel->id])){
                $userModel->addError('phone', 'This phone number already exists.');
            }
            $upload = UploadedFile::getInstance($userProfile, 'avatar');

            if(isset($post['UserAddress'])){
                $userAddress->load($post);
                $userAddress->phone = $post['User']['phone'];
            }
            if($userModel->save() && $userProfile->save() && $userAddress->save()){
                if(isset($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                    $imageUpload=DrsImageUpload::updateProfileImageWeb('hospitals',$id,$upload);
                }
                Yii::$app->session->setFlash('success', "Profile Updated");
                return $this->redirect(['/hospital/edit-profile']);
            }
        }
        return $this->render('/hospital/edit-profile',['model' => $userModel,'userModel'=>$userModel,'userProfile'=>$userProfile,'userAddress'=>$userAddress]);
    }

    public function actionAboutus(){
        $user_id=$this->loginUser->id;
        $userAboutus = UserAboutus::find()->where(['user_id' => $user_id])->one();
        if(empty($userAboutus))
        {
            $userAboutus = new UserAboutus();
        }

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $userAboutus->load($post);
            $userAboutus->user_id = $user_id;
            if($userAboutus->save()){
                Yii::$app->session->setFlash('success', "About Us Added");
                return $this->redirect(['/hospital/aboutus']);
            }

        }
        return $this->render('/hospital/aboutus',['userProfile'=>$userAboutus]);
    }

    public function actionServices($service_id = NULL){
        $user_id=Yii::$app->user->id;
        $groupid = Groups::GROUP_HOSPITAL;
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
                    Yii::$app->session->setFlash('success', 'hospital Services Updated');
                    return $this->redirect(['/hospital/services']);
                }else{
                    Yii::$app->session->setFlash('error', 'Sorry hospital Facility Not Added');

                }
            }

        }

        $servicesList=UserProfile::find()->andWhere(['user_id'=>$user_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();
        $services=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>11])->all();

        return $this->render('/hospital/services', ['model' => $model,'services' => $services,'servicesList' => $servicesList]);
    }

    public function actionSpeciality($speciality_id = NULL){
        $user_id=Yii::$app->user->id;
        $groupid = Groups::GROUP_HOSPITAL;

        $getspecialities = Drspanel::getMyHospitalSpeciality($user_id);

        $specialityList=UserProfile::find()->andWhere(['user_id'=>$user_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();
        $speciality=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>5])->all();

        $treatment=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>9])->all();

        $treatmentList=UserProfile::find()->andWhere(['user_id'=>$user_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();

        return $this->render('/hospital/speciality', ['specialities' => $getspecialities,'specialityList' => $specialityList,'treatments' =>$treatment,'treatmentList' =>$treatmentList]);
    }

    public function actionCustomerCare(){
        $customer = MetaValues::find()->orderBy('id asc')->where(['key'=>8])->all();
        return $this->render('/hospital/customer-care', ['customer' => $customer]);
    }

    public function actionAppointments($slug = ''){
        $id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($id);
        $string=Yii::$app->request->queryParams;
        $date=date('Y-m-d');
        $type='';$speciality_check=0;
        if(isset($string['type'])){
            $type=$string['type'];
        }
        if($type == 'current_appointment'){
            $current_shifts=0;
            if(!empty($slug)){
                $doctorProfile=UserProfile::find()->where(['slug'=>$slug])->one();
                if(!empty($doctorProfile)) {
                    $doctor=User::findOne($doctorProfile->user_id);
                    $current_shifts = 0;
                    $bookings = array();
                    $getShists = DrsPanel::getBookingShifts($doctor->id, $date, $id);
                    $appointments = DrsPanel::getCurrentAppointments($doctor->id, $date, $current_shifts, $getShists);
                    if (!empty($appointments)) {
                        if (isset($appointments['shifts']) && !empty($appointments['shifts'])) {
                            $current_shifts = $appointments['shifts'][0]['schedule_id'];
                            $bookings = $appointments['bookings'];
                        }
                    }

                    return $this->render('/hospital/appointment/current-appointments',
                        ['defaultCurrrentDay' => strtotime($date), 'appointments' => $appointments, 'bookings' => $bookings,'current_shifts' => $current_shifts, 'doctor' => $doctor,'doctorProfile'=>$doctorProfile,'type' => $type, 'userType'=>'hospital']);

                }
            }
            else{
                $speciality_check=1;
            }
        }
        elseif($type == 'current_shift'){
            if(!empty($slug)){
                $doctorProfile=UserProfile::find()->where(['slug'=>$slug])->one();
                if(!empty($doctorProfile)){
                    $doctor=User::findOne($doctorProfile->user_id);
                    $current_shifts='';$shifts=array();$appointments=array();
                    $getSlots=DrsPanel::getBookingShifts($doctor->id,$date,$id);
                    $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
                    if(!empty($checkForCurrentShift)){
                        $current_affairs=DrsPanel::getCurrentAffair($checkForCurrentShift,$doctor->id,$date,$current_shifts,$getSlots);
                        if($current_affairs['status'] && empty($current_affairs['error'])){
                            $shifts=$current_affairs['all_shifts'];
                            $appointments=$current_affairs['data'];
                            $current_shifts=$current_affairs['schedule_id'];

                            return $this->render('/hospital/appointment/current-affair',['schedule_id'=>$current_affairs['schedule_id'],'is_completed'=>$current_affairs['is_completed'],'is_started'=>$current_affairs['is_started'],'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date,'userType'=>'hospital']);
                        }
                        else{
                            return $this->render('/hospital/appointment/current-affair',['schedule_id'=>$current_affairs['schedule_id'],'is_completed'=>0,'is_started'=>0,'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date,'userType'=>'hospital']);
                        }
                    }
                    else{
                        // no shifts
                        return $this->render('/hospital/appointment/current-affair',['schedule_id'=>0,'is_completed'=>0,'is_started'=>0,'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date,'userType'=>'hospital']);
                    }
                }
            }
            else{
                $speciality_check=1;
            }
        }
        else{
            $type='book';
            $current_shifts=0; $slots=array();
            if(!empty($slug)){
                $doctorProfile=UserProfile::find()->where(['slug'=>$slug])->one();
                if(!empty($doctorProfile)){
                    $doctor=User::findOne($doctorProfile->user_id);
                    $getShists=DrsPanel::getBookingShifts($doctor->id,$date,$id);
                    $appointments=DrsPanel::getCurrentAppointments($doctor->id,$date,$current_shifts,$getShists);
                    if(!empty($appointments)){
                        if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                            $current_shifts=$appointments['shifts'][0]['schedule_id'];
                            $slots = DrsPanel::getBookingShiftSlots($doctor->id,$date,$current_shifts,'available');
                        }
                    }
                    return $this->render('/hospital/appointment/appointments',
                        ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'slots'=>$slots,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'userType'=>'hospital']);

                }
            }
            else{
                $speciality_check=1;
            }
        }

        if($speciality_check == 1){
            if(isset($string['speciality']) && !empty($string['speciality'])){
                $selected_speciality=$string['speciality'];
            }
            else{
                $selected_speciality=0;
            }

            $params['user_id']=$id;
            $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
            $data_array =  DrsPanel::getDoctorSliders($params);

            return $this->render('/common/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'appointment','userType'=>'hospital']);
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAjaxToken(){
        $result=['status'=>false,'msg'=>'Invalid Request.'];
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $current_shifts=$post['shift_id'];
            $doctor_id=$post['doctorid'];
            $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
            $slots = DrsPanel::getBookingShiftSlots($doctor_id,$date,$current_shifts,'available');                   echo $this->renderAjax('/common/_slots',['slots'=>$slots,'doctor_id'=>$doctor_id,'userType'=>'hospital']); exit();
        }
    }

    public function actionAjaxAppointment(){
        $result=['status'=>false,'msg'=>'Invalid Request.'];
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $bookings=array();
            $current_shifts=$post['shift_id'];
            $doctor_id=$post['doctorid'];
            $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
            $getShists=DrsPanel::getBookingShifts($doctor_id,$date,$this->loginUser->id);
            $appointments=DrsPanel::getCurrentAppointments($doctor_id,$date,$current_shifts,$getShists);
            if(!empty($appointments)){
                if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                    $bookings = $appointments['bookings'];
                }
            }
            echo $this->renderAjax('/common/_bookings',['bookings'=>$bookings,'doctor_id'=>$doctor_id,'userType'=>'hospital']); exit();
        }
    }

    public function actionBookingConfirm(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $slot_id=explode('-',$post['slot_id']);
            $doctor_id=$post['doctorid'];
            $doctorProfile=UserProfile::find()->where(['user_id'=>$doctor_id])->one();
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
                        ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,
                            'address'=>UserAddress::findOne($schedule->address_id),
                            'model'=> $model,'userType'=>'hospital'
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
                        $data['UserAppointment']['doctor_fees']=$slot->fees;

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

                        $addAppointment=DrsPanel::addAppointment($data,'doctor');

                        if($addAppointment['type'] == 'model_error'){
                            $response=DrsPanel::validationErrorMessage($addAppointment['data']);
                        }
                        else{

                            Yii::$app->session->setFlash('success', "Appointment booked successfully.");
                            return $this->redirect(['/hospital/appointments']);
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
        return json_encode($response);
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
            $result['result']=$this->renderAjax('/common/_appointment_date_slider',['doctor_id'=>$user_id,'dates_range' => $dates_range,'date'=>$first,'type'=>$type,'userType'=>'hospital']);
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
            $doctor_id=$post['user_id'];
            $doctor=User::findOne($doctor_id);
            $days_plus=$post['plus'];
            $operator=$post['operator'];
            $type=$post['type'];
            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));

            $current_shifts=0; $slots=array(); $bookings=array();
            $getShists=DrsPanel::getBookingShifts($doctor_id,$date,$id);
            $appointments=DrsPanel::getCurrentAppointments($doctor_id,$date,$current_shifts,$getShists);
            if(!empty($appointments)){
                if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                    $current_shifts=$appointments['shifts'][0]['schedule_id'];
                    if($type == 'book'){
                        $slots = DrsPanel::getBookingShiftSlots($doctor_id,$date,$current_shifts,'available');
                    }
                    else{
                        $bookings = $appointments['bookings'];
                    }
                }
            }
            echo $this->renderAjax('/common/_appointment_shift_slots',['appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type,'slots'=>$slots,'bookings'=>$bookings,'userType'=>'hospital']);exit;
        }
        echo 'error'; exit;
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
                return $this->redirect(['/hospital/find-doctors']);
            }else{
                Yii::$app->session->setFlash('error', 'Sorry request couldnot sent');

            }
        }

        exit;

        return Null;
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

    public function actionShifts(){
        $date=date('Y-m-d');
        $current_shifts=0;
        $hospitals= DrsPanel::doctorHospitalList($this->loginUser->id);
        $appointments=DrsPanel::getCurrentAppointments($this->loginUser['id'],$date,$current_shifts);
        return $this->render('/hospital/shifts',['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'hospitals'=>$hospitals['apiList']]);
    }

    public function actionAjaxShiftDetails(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $userShift= UserSchedule::findOne($post['id']);
            if($userShift){
                $newShift= new AddScheduleForm();
                $newShift->setShiftData($userShift);
                return $this->render('/hospital/shift/_editShift',['userShift'=>$newShift]);
            }
        }
        return NULL;
    }

    public function actionAppointmentStatusUpdate(){
        $res=['status'=>false,'msg'=>'You have not access.'];
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $date=date('Y-m-d');
            $current_shifts=0;
            $userAppointment=UserAppointment::findOne($post['token_id']);
            $appointments=DrsPanel::getCurrentAppointmentsAffairs($this->loginUser['id'],$date);
            if(isset($appointments['bookings']) && count($appointments['bookings'])>0 && !empty($userAppointment)) {

                if(count($appointments['bookings'])>0){
                    if($appointments['bookings'][0]['id']==$post['token_id'] && $post['token']==$appointments['bookings'][0]['token'] ) {

                        $status=($post['type']=='next')?UserAppointment::STATUS_COMPLETED:UserAppointment::STATUS_SKIP;

                        //$userAppointment->payment_type='cash';
                        $userAppointment->status=$status;
                        if($userAppointment->save()){

                            $appointments=DrsPanel::getCurrentAppointmentsAffairs($this->loginUser['id'],$date);

                            $current_affair=$this->render('/common/appointment/current-affair',['appointments'=>$appointments,'type'=>'current-affairs']);
                            $res=['status'=>true,'msg'=>'Booking completed.','data'=>$current_affair];
                        }else{

                            $res=['status'=>true,'msg'=>'Booking not completed please try again.'];
                        }
                    }

                }else{
                    $res=['status'=>false,'msg'=>'Token not available.'];
                }

            }else{
                $res=['status'=>false,'msg'=>'Token Mismatch Please try again.'];
            }

        }
        return json_encode($res);
    }

    public function actionPatientHistory($slug = ''){
        $id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($id);
        $date=date('Y-m-d');
        $type='history';
        $userType='hospital';

        $current_selected=0;
        $checkForCurrentShift=0;
        $appointments=$shiftAll=$typeCount=$history=[];

        if(!empty($slug)){
            $doctorProfile=UserProfile::find()->where(['slug'=>$slug])->one();
            if(!empty($doctorProfile)){
                $doctor=User::findOne($doctorProfile->user_id);
                $getSlots=DrsPanel::getBookingShifts($doctor->id,$date,$id);
                if(!empty($getSlots)){
                    $checkForCurrentShift=$getSlots[0]['schedule_id'];
                    $current_selected = $checkForCurrentShift;
                    $getAppointments=DrsPanel::appointmentHistory($doctor->id,$date,$current_selected,$getSlots,'');
                    $shiftAll=DrsPanel::getDoctorAllShift($doctor->id,$date,$checkForCurrentShift,$getSlots,$current_selected);
                    $appointments=$getAppointments['bookings'];
                    $history=$getAppointments['total_history'];
                    $typeCount=$getAppointments['type'];
                }

                return $this->render('/hospital/history-statistics/patient-history',['type'=>$type,'userType'=>$userType,'history_count'=>$history,'typeCount'=>$typeCount,'appointments'=>$appointments,'shifts'=>$shiftAll,'defaultCurrrentDay'=>strtotime($date),'doctor'=>$doctor,'current_selected'=>$current_selected]);

            }
            else{
                // not found
            }
        }
        else{
            $string=Yii::$app->request->queryParams;
            if(isset($string['speciality']) && !empty($string['speciality'])){
                $selected_speciality=$string['speciality'];
            }
            else{
                $selected_speciality=0;
            }
            $params['user_id']=$id;
            $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
            $data_array =  DrsPanel::getDoctorSliders($params);

            return $this->render('/common/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'history','userType'=>'hospital']);
        }

    }

    public function actionAjaxHistoryContent(){
        $user_id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($user_id);
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $doctor_id=$post['user_id'];
            $doctor=User::findOne($doctor_id);
            $days_plus=$post['plus'];
            $operator=$post['operator'];
            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));
            $current_selected=0;
            $checkForCurrentShift=0;
            $appointments=$shiftAll=$typeCount=$history=[];
            $getSlots=DrsPanel::getBookingShifts($doctor_id,$date,$user_id);
            if(!empty($getSlots)){
                $checkForCurrentShift=$getSlots[0]['schedule_id'];
                if($current_selected == 0){
                    $current_selected = $checkForCurrentShift;
                }
                $getAppointments=DrsPanel::appointmentHistory($doctor_id,$date,$current_selected,$getSlots);
                $shiftAll=DrsPanel::getDoctorAllShift($doctor_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
                $appointments=$getAppointments['bookings'];
                $history=$getAppointments['total_history'];
                $typeCount=$getAppointments['type'];
            }

            echo $this->renderAjax('/hospital/history-statistics/_history-content',['history_count'=>$history,'typeCount'=>$typeCount,'appointments'=>$appointments,'shifts'=>$shiftAll,'doctor'=>$doctor,'current_selected'=>$current_selected,'userType'=>'hospital']); exit;
        }
        echo 'error'; exit;
    }

    public function actionAjaxHistoryAppointment(){
        $result=['status'=>false,'msg'=>'Invalid Request.'];
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $bookings=array();
            $doctor_id=$post['user_id'];
            $doctor=User::findOne($doctor_id);
            $current_shifts=$post['shift_id'];
            $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
            $getShists=DrsPanel::getBookingShifts($doctor_id,$date,$this->loginUser->id);
            $appointments=DrsPanel::getCurrentAppointments($doctor_id,$date,$current_shifts,$getShists);
            if(!empty($appointments)){
                if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                    $bookings = $appointments['bookings'];
                }
            }
            echo $this->renderAjax('/hospital/history-statistics/_history-patient',['appointments'=>$bookings,'doctor_id'=>$doctor_id,'userType'=>'hospital']); exit();
        }
    }

    public function actionUserStatisticsData($slug = ''){
        $id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($id);
        $date=date('Y-m-d');
        $type='user_history';
        $userType='hospital';

        $current_selected=0;
        $checkForCurrentShift=0;
        $appointments=$shiftAll=$typeCount=$history=[];
        $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;


        if(!empty($slug)){
            $doctorProfile=UserProfile::find()->where(['slug'=>$slug])->one();
            if(!empty($doctorProfile)){
                $doctor=User::findOne($doctorProfile->user_id);

                $getSlots=DrsPanel::getBookingShifts($doctor->id,$date,$id);
                if(!empty($getSlots)){
                    $checkForCurrentShift=$getSlots[0]['schedule_id'];
                    $current_selected = $checkForCurrentShift;
                    $getAppointments=DrsPanel::appointmentHistory($doctor->id,$date,$current_selected,$getSlots,'');
                    $shiftAll=DrsPanel::getDoctorAllShift($doctor->id,$date,$checkForCurrentShift,$getSlots,$current_selected);
                    $appointments=$getAppointments['bookings'];
                    $typeCount=$getAppointments['type'];
                }
                return $this->render('/hospital/history-statistics/user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'defaultCurrrentDay'=>strtotime($date),'doctor'=>$doctor,'current_selected'=>$current_selected]);
            }
            else{
                // not found
            }
        }
        else{
            $string=Yii::$app->request->queryParams;
            if(isset($string['speciality']) && !empty($string['speciality'])){
                $selected_speciality=$string['speciality'];
            }
            else{
                $selected_speciality=0;
            }
            $params['user_id']=$id;
            $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
            $data_array =  DrsPanel::getDoctorSliders($params);

            return $this->render('/common/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'user_history','page_heading' => 'User Statistics Data','userType'=>'hospital']);
        }
    }

    public function actionAjaxUserStatisticsData(){
        $user_id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($user_id);
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $doctor_id=$post['user_id'];
            $doctor=User::findOne($doctor_id);
            $days_plus=$post['plus'];
            $operator=$post['operator'];
            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));

            $current_selected=0;
            $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
            $checkForCurrentShift=0;
            $appointments=$shiftAll=$typeCount=[];
            $getSlots=DrsPanel::getBookingShifts($doctor_id,$date,$user_id);
            if(!empty($getSlots)){
                $checkForCurrentShift=$getSlots[0]['schedule_id'];
                $current_selected = $checkForCurrentShift;
                $getAppointments=DrsPanel::appointmentHistory($doctor_id,$date,$current_selected,$getSlots,$typeselected);
                $shiftAll=DrsPanel::getDoctorAllShift($doctor_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
                $appointments=$getAppointments['bookings'];
                $typeCount=$getAppointments['type'];
            }
            return $this->renderAjax('/hospital/_user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'date'=>strtotime($date),'doctor'=>$doctor,'current_shifts'=>$current_selected]);
        }
    }

    public function actionAjaxStatisticsData(){
        $user_id=Yii::$app->user->id;
        $hospital=UserProfile::findOne($user_id);
        $result['status']=false;
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $doctor_id=$post['user_id'];
            $doctor=User::findOne($doctor_id);
            $date=($post['date'])?date('Y-m-d',strtotime($post['date'])):date('Y-m-d');
            if(isset($post['type'])){
                $typeselected=($post['type']=='online')?UserAppointment::BOOKING_TYPE_ONLINE:UserAppointment::BOOKING_TYPE_OFFLINE;
            }
            else{
                $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
            }
            $checkForCurrentShift=(isset($post['shift_id']))?$post['shift_id']:0;
            $appointments=$shiftAll=$typeCount=[];
            $getSlots=DrsPanel::getBookingShifts($doctor_id,$date,$user_id);
            if(!empty($getSlots)){
                $getAppointments=DrsPanel::appointmentHistory($doctor_id,$date,$checkForCurrentShift,$getSlots,$typeselected);
                $appointments=$getAppointments['bookings'];
                $typeCount=$getAppointments['type'];
            }
            $result['status']=true;
            $result['appointments']=$this->renderAjax('/common/_appointment-token',['appointments'=>$appointments,'typeselected'=>$typeselected,'typeCount'=>$typeCount,
                'doctor'=>$doctor,'userType'=>'hospital']);
            $result['typeCount']=$typeCount;
            $result['typeselected']=$typeselected;
        }
        return json_encode($result);
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
                    $data[]=array('id'=>$result['id'],'category_check'=>$result['category'],'category'=>Yii::t('db',$result['category']), 'query'=>$result['query'],'label'=>$result['label'],'avator'=>$result['avator']);
                }

                /*Category List search*/
                $categories=DrsPanel::getSpecialitySearchListArray($words);
                if(!empty($categories)){
                    foreach($categories as $cat){
                        $data[]=array('id'=>'','category_check'=>'Specialization','category'=>Yii::t('db','Specialization'),'query'=>$cat['query'],'label'=>Yii::t('db',$cat['label']),'filters'=>'Specialization');
                    }
                }

                $data[]=array('id'=>'','category_check'=>'Search','category'=>Yii::t('db','Search'),'query'=>$q,'label'=>Yii::t('db','Doctor').' '.Yii::t('db','named').' '.$q,'filters'=>'Doctor','avator'=>'');
                $data[]=array('id'=>'','category_check'=>'Search','category'=>Yii::t('db','Search'),'query'=>$q,'label'=>Yii::t('db','Hospital').' '.Yii::t('db','named').' '.$q,'filters'=>'Hospital','avator'=>'');
                $out = array_values($data);
            }
            /*  else {

                  $data= DrsPanel::getTypeDefaultListArray();
                  foreach($data as $group){
                      $out[]=array('id'=>'','category_check'=>'Groups','category'=>'Groups','query'=>'','label'=>Yii::t('db',$group['name']),'filters'=>$group['name'],'avator'=>'');
                  }
              }*/
            return $data; exit();
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
                                $path='/hospital?results_type='.strtolower($post['filter']).'&q=';
                                $out = array('result'=>'success','fullpath'=>0,'filter'=>$post['filter'],'slug'=>$post['slug'],'path'=>$path);
                            }
                            else{
                                $out = array('result'=>'success','fullpath'=>0,'filter'=>$post['filter'],'path'=>'/hospital?results_type='.$post['filter']);
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
                    $out = array('result'=>'success','fullpath'=>0,'filter'=>$restype,'slug'=>$post['slug'],'path'=>'/hospital?results_type='.$restype.'&q=');
                }
                else{
                    $out = array('result'=>'success','fullpath'=>0,'filter'=>$restype,'path'=>'/hospital?results_type='.$restype);
                }
                $this->setSearchCookie($out);
                return $out;exit();
            }
            else{

            }
        }
        return array('result'=>'fail');exit();
    }

    public function setSearchCookie($codearray){
        $baseurl =$_SERVER['HTTP_HOST'];
        $json = json_encode($codearray, true);
        setcookie('search_filter', $json, time()+60*60, '/',$baseurl , false);
        return $codearray;
    }

    public function actionMyPatients(){
        $lists= DrsPanel::myPatients(['doctor_id'=>$this->loginUser->id]);
        Yii::$app->session->setFlash('success', "Profile Updated");
        return $this->render('/hospital/my-patients',['lists'=>$lists]);
    }

    public function actionMyDoctors(){
        /*$lists= DrsPanel::myPatients(['doctor_id'=>$this->loginUser->id]);*/
        $hospital_id = $this->loginUser->id;
        $usergroupid = Groups::GROUP_HOSPITAL;
        $lists=DrsPanel::doctorsHospitalList ($hospital_id,'Confirm',$usergroupid,$hospital_id) ;
        $command = $lists->createCommand();
        $lists = $command->queryAll();
        $userDoctorModel = new UserProfile();
        $doctorFind= '';
        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            if(isset($post['UserProfile']))
            {
                $doctorFind = UserProfile::find()->andFilterWhere(['like', 'name', $post['UserProfile']['name']]
                )->all();
            }

            return $this->render('/hospital/my-doctors',['userDoctorModel' => $userDoctorModel,'findDoctor' => $doctorFind]);
        }
        else {
            return $this->render('/hospital/my-doctors',['lists'=>$lists,'userDoctorModel' => $userDoctorModel]);
        }
    }

    public function actionFindDoctors($doctor_id = NULL){
        /*$lists= DrsPanel::myPatients(['doctor_id'=>$this->loginUser->id]);*/
        $hospital_id = $this->loginUser->id;
        $usergroupid = Groups::GROUP_HOSPITAL;
        if(!empty($doctor_id) && !empty($hospital_id) ){
            $model=UserRequest::find()->andWhere(['request_from'=>$hospital_id,'request_to'=>$hospital_id])->andWhere(['groupid'=>Groups::GROUP_HOSPITAL])->all();
        }else{
            $model = new UserRequest();
        }
        $userDoctorModel = new UserProfile();
        $doctorFind= '';
        if(Yii::$app->request->isPost){
            $post=Yii::$app->request->post();
            // pr($post);die;
            if(isset($post['UserRequest']['request_to'])){
                $Userrequstto=$post['UserRequest']['request_to'];
                // if(!empty($Userrequstto)){
                //   $post['UserRequest']['request_to']=implode(',',$Userrequstto);
                // }
                foreach ($Userrequstto as  $value) {
                    $postData['groupid']=Groups::GROUP_HOSPITAL;
                    $postData['request_from']=$hospital_id;
                    $postData['request_to']=$value;
                    $postData['status']=1;
                    $type= 'Add';
                    $modelUpdate = UserRequest::updateStatus($postData,$type);
                }

                if(count($modelUpdate)>0){
                    Yii::$app->session->setFlash('success', 'Requested sent');
                    return $this->redirect(['/hospital/find-doctors']);
                }else{
                    Yii::$app->session->setFlash('error', 'Sorry request couldnot sent');
                }
            }
            if(isset($post['UserProfile']))
            {
                $doctorFind = UserProfile::find()->andFilterWhere(['like', 'name', $post['UserProfile']['name']]
                )->all();
                return $this->render('/hospital/find-doctors',['userDoctorModel' => $userDoctorModel,'findDoctor' => $doctorFind]);
            }

        }
        else {
            $lists=DrsPanel::doctorsHospitalList($hospital_id,'All',$usergroupid,$hospital_id);
            $command = $lists->createCommand();
            $lists = $command->queryAll();
            return $this->render('/hospital/find-doctors',['lists'=>$lists,'model'=> $model,'user_id' =>$hospital_id,'userDoctorModel' => $userDoctorModel,'findDoctor' => $doctorFind]);
        }
    }

    public function actionAttendersList(){
        // echo 'dsafd';die;
        $model = new AttenderForm();
        if (Yii::$app->request->isAjax) {

            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            if(!empty($post['AttenderForm']['shift_id']) && count($post['AttenderForm']['shift_id'])>0)
            {
                $post['AttenderForm']['shift_id']=implode(',', $post['AttenderForm']['shift_id']);
            }
            if(!empty($post['AttenderForm']['doctor_id']) && count($post['AttenderForm']['doctor_id'])>0)
            {
                $post['AttenderForm']['doctor_id']=implode(',', $post['AttenderForm']['doctor_id']);
            }

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
                Yii::$app->session->setFlash('success', "Attender Added!");
                return $this->redirect(['/hospital/attenders']);
            }
        }
        $hospitalId = Groups::GROUP_HOSPITAL;
        $addressList=DrsPanel::doctorHospitalList($this->loginUser->id);
        $list=DrsPanel::attenderList(['parent_id'=>$this->loginUser->id],'apilist');
        $hospital_id = $this->loginUser->id;

        $lists=DrsPanel::doctorsHospitalList($hospital_id,'Confirm',Groups::GROUP_HOSPITAL,$hospital_id);
        $command = $lists->createCommand();
        $requests = $command->queryAll();

        // pr($requests);die;

        return $this->render('/hospital/attender/list',['list'=>$list,'hospitalId' =>$hospitalId,'user'=>$this->loginUser,
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'),
            'hospitals'=>$addressList['listaddress'],
            'doctors'=>$requests]);
    }

    public function actionAttenderDetails(){
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            // pr($post);die;
            $user =$this->findModel($post['id']);
            $addressList=DrsPanel::attenderHospitalList($post['id']);
            $shiftList=Drspanel::shiftList(['user_id'=>$this->loginUser->id],'list');

            $hospital_id = $this->loginUser->id;

            $doctorlists=DrsPanel::doctorsHospitalList($hospital_id,'Confirm',Groups::GROUP_HOSPITAL,$hospital_id);
            $selectedDoctors=Drspanel::doctorsHospitalList($hospital_id,'Confirm',Groups::GROUP_HOSPITAL,['doctor_id' => $post['id']]);
            $command2 = $selectedDoctors->createCommand();
            $requests2 = $command2->queryAll();

            $command = $doctorlists->createCommand();
            $requests = $command->queryAll();
            $model = new AttenderEditForm();
            $model->id=$post['id'];
            $model->name=$user['userProfile']['name'];
            $model->avatar=$user['userProfile']['avatar'];
            $model->avatar_base_url=$user['userProfile']['avatar'];
            $model->avatar_path=$user['userProfile']['avatar_path'];
            $model->phone=trim($user->phone);
            $model->email=$user->email;
            $doctorIDS = array();
            foreach( $requests2 as $value){
                $doctorIDS[] = $value['user_id'];
            }
            $model->doctor_id=$doctorIDS;
            $hospitalId = Groups::GROUP_HOSPITAL;
            return $this->renderAjax('/hospital/attender/edit', [
                'model' => $model,
                'hospitals'=>$addressList,
                'doctor_lists'=>$requests,
                'hospitalId' => $hospitalId
            ]);

        }
        return NULL;
    }

    public function actionAttenderUpdate(){
        $hospital_id=Yii::$app->user->id;
        $model = new AttenderEditForm();
        if (Yii::$app->request->isAjax) {

            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();


            if(!empty($post['AttenderEditForm']['shift_id']) && count($post['AttenderEditForm']['shift_id'])>0)
            {
                $post['AttenderEditForm']['shift_id']=implode(',', $post['AttenderEditForm']['shift_id']);
            }
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
                if(isset($post['AttenderEditForm']['doctor_id']) && !empty($post['AttenderEditForm']['doctor_id'])){
                    $attender_id=$post['AttenderEditForm']['id'];
                    $doctors=$post['AttenderEditForm']['doctor_id'];
                    $addupdateHospitalDoctors=DrsPanel::addUpdateDoctorsToHospitalAttender($doctors,$attender_id,$hospital_id);
                }
                if(!empty($upload)){
                    $upload->saveAs($uploadDir .$image_name );
                }
                Yii::$app->session->setFlash('success', "Attender Updated!");

                return $this->redirect(['/hospital/attenders']);
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
            if($groupid != Groups::GROUP_HOSPITAL){
       
                return  $this->redirect(array('/'));
            }
            else{
                return parent::beforeAction($action);
            }
        }
        $this->redirect(array('/'));
    }
}