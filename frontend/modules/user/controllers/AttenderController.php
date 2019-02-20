<?php
namespace frontend\modules\user\controllers;

use backend\models\AddScheduleForm;
use common\components\DrsImageUpload;
use common\models\UserAddress;
use common\models\UserAppointment;
use common\models\UserSchedule;
use common\models\MetaKeys;
use common\models\UserScheduleDay;
use common\models\UserScheduleSlots;
use frontend\models\AppointmentForm;
use Yii;
use yii\authclient\AuthAction;
use yii\base\Exception;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use common\models\MetaValues;
use common\models\User;
use common\models\UserProfile;
use common\models\Groups;
use common\components\DrsPanel;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

/**
 * Class AttenderController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AttenderController extends \yii\web\Controller
{

    /**
     * @return array
     */

    private $loginUser;

    public function actions(){
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
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $this->loginUser=Yii::$app->user->identity;
                            return $this->loginUser->groupid==Groups::GROUP_ATTENDER;
                        }
                    ],
                ]
            ]
        ];
    }

    public function actionEditProfile(){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $genderlist=DrsPanel::getGenderList();

        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $userModel->load($post);
            $old_image = $userProfile->avatar;
            $userProfile->load($post);
            $userProfile->avatar= $old_image;

            if($userModel->groupUniqueNumber(['phone'=>$post['User']['phone'],'groupid'=>$userModel->groupid,'id'=>$userModel->id])){
                $userModel->addError('phone', 'This phone number already exists.');
            }

            $upload = UploadedFile::getInstance($userProfile, 'avatar');
            if($userModel->save() && $userProfile->save()){
                if(isset($_FILES['UserProfile']['name']['avatar']) &&
                    !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                    $imageUpload=DrsImageUpload::updateProfileImageWeb('attenders',$id,$upload);
                }
                Yii::$app->session->setFlash('success', "Profile updated!");
                return $this->redirect(['/attender/edit-profile']);
            }
        }

        return $this->render('/attender/edit-profile',
            ['model' => $userModel,'userModel'=>$userModel,'userProfile'=>$userProfile,
                'genderList'=>$genderlist]);
    }

    public function actionMyPatients(){
        $id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($id);
        $parentGroup=$getParentDetails['parentGroup'];
        if($parentGroup == Groups::GROUP_HOSPITAL){

        }
        else{
            $parent_id=$getParentDetails['parent_id'];
            $lists= DrsPanel::myPatients(['doctor_id'=>$parent_id]);
            return $this->render('/attender/doctor/my-patients',['lists'=>$lists]);
        }

    }

    public function actionCustomerCare(){
        $customer = MetaValues::find()->orderBy('id asc')->where(['key'=>8])->all();
        return $this->render('/attender/customer-care', ['customer' => $customer]);
    }

    public function actionMyShifts(){
        $id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($id);
        $parentGroup=$getParentDetails['parentGroup'];
        $parent_id=$getParentDetails['parent_id'];

        $selectedShifts=Drspanel::shiftList(['user_id'=>$parent_id,'attender_id'=>$id],'list');


        $addressList=DrsPanel::doctorHospitalList($parent_id);
        $listadd=$addressList['apiList'];
        $shift_array = array(); $s=0;
        $shift_value = array(); $sv=0;
        $selectedShiftsIds= array(); $address_list=array();
        foreach($listadd as $address){
            $shifts=DrsPanel::getShiftListByAddress($parent_id,$address['id']);
            foreach($shifts as $key => $shift){
                if($shift['hospital_id']==0) {
                    $shift_array[$s]['value'] = $shift['shifts_ids'];
                    $shift_array[$s]['label'] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';
                    $shift_value[$sv] = $shift['address_line'].' ('.$shift['shift_label'].') - ('.$shift['shifts_list'].')';

                    $shift_id_list=$shift['shifts_ids'];
                    foreach($selectedShifts as $select=>$valuesel){                        
                        if(in_array($select,$shift_id_list)){
                            $selectedShiftsIds[$sv]=$sv;
                        }
                    }
                    $s++;$sv++;

                }
            }
            $address_repeat=array();
            foreach($selectedShiftsIds as $selected_shift){
                if(isset($shifts[$selected_shift])){
                    if(in_array($shifts[$selected_shift]['id'],$address_repeat)){

                    }
                    else{
                        $address_list[] = $shifts[$selected_shift];
                        $address_repeat[] = $shifts[$selected_shift]['id'];
                    }
                }
            }
        }



        return $this->render('shift/my-shifts',['doctor_id'=>$parent_id,'address_list'=>$address_list,
            'allshifts'=>$selectedShifts]);
    }

    /* Today Timing*/
    public function actionDayShifts(){

        $id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($id);
        $parentGroup=$getParentDetails['parentGroup'];
        if($parentGroup == Groups::GROUP_HOSPITAL){

        }
        else{
            $parent_id=$getParentDetails['parent_id'];

            $doctor=User::findOne($parent_id);
            $params = Yii::$app->request->queryParams;
            if(!empty($params) && isset($params['date'])){
                $date=$params['date'];
            }
            else{
                $date=date('Y-m-d');
            }
            $getShists=DrsPanel::getBookingShifts($parent_id,$date,$id);

            return $this->render('shift/day-shifts',
                ['defaultCurrrentDay'=>strtotime($date),'shifts'=>$getShists,'doctor'=>$doctor,'date'=>$date]);
        }
    }

    public function actionUpdateShiftStatus(){
        $response["status"] = 0;
        $response["error"] = true;
        $response['message']= 'You have do not permission.';
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();

            $id=Yii::$app->user->id;
            $getParentDetails=DrsPanel::getParentDetails($id);
            $parent_id=$getParentDetails['parent_id'];

            $params['booking_closed']=$post['status'];
            $params['doctor_id']=$parent_id;
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

            $id=Yii::$app->user->id;
            $getParentDetails=DrsPanel::getParentDetails($id);
            $parent_id=$getParentDetails['parent_id'];

            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));
            $appointments= DrsPanel::getBookingShifts($parent_id,$date,$id);
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
            if(isset($post['date_dayschedule'])) {
                $date=$post['date_dayschedule'];
            }
            else {
                $date=date('Y-m-d');
            }
            if(isset($update_shift['error']) && $update_shift['error']==true) {
                Yii::$app->session->setFlash('shifterror', $update_shift['message']);
            }
            else {
                Yii::$app->session->setFlash('success', "'Shift updated successfully'");
            }
            return $this->redirect(['/doctor/day-shifts','date'=>$date]);
        }
    }

    /****************************Booking/Appointment***************************************/
    public function actionAppointments($type =''){
        $id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($id);
        $parentGroup=$getParentDetails['parentGroup'];
        $date=date('Y-m-d');
        if($parentGroup == Groups::GROUP_HOSPITAL){
            $string=Yii::$app->request->queryParams;
            $slug=$type;
            $type='';
            if(isset($string['type'])){
                $type=$string['type'];
            }
            $parent_id = $getParentDetails['parent_id'];
            $hospital=$getParentDetails['parentModel'];
            return $this->hospitalAppointment($slug,$type,$parent_id,$id,$date,$hospital);
        }
        else {
            $parent_id = $getParentDetails['parent_id'];
            $doctor = $getParentDetails['parentModel'];
            return $this->doctorAppointment($type,$parent_id,$id,$date,$doctor);
        }
    }

    function hospitalAppointment($slug,$type,$parent_id,$id,$date,$hospital){
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

                    return $this->render('attender/hospital/current-appointments',
                        ['defaultCurrrentDay' => strtotime($date), 'appointments' => $appointments, 'bookings' => $bookings, 'type' => $type, 'current_shifts' => $current_shifts, 'doctor' => $doctor,'doctorProfile'=>$doctorProfile]);

                }
                else {

                    //not found
                }
            }
            else{
                if(isset($string['speciality']) && !empty($string['speciality'])){
                    $selected_speciality=$string['speciality'];
                }
                else{
                    $selected_speciality=0;
                }

                $params['user_id']=$id;
                $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
                $data_array =  DrsPanel::getDoctorSliders($params);

                return $this->render('/attender/hospital/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'appointment']);
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

                            return $this->render('/attender/hospital/current-affair',['is_completed'=>$current_affairs['is_completed'],'is_started'=>$current_affairs['is_started'],'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date]);
                        }
                        else{
                            return $this->render('/attender/hospital/current-affair',['is_completed'=>0,'is_started'=>0,'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date]);
                        }
                    }
                    else{
                        // no shifts
                        return $this->render('/attender/hospital/current-affair',['is_completed'=>0,'is_started'=>0,'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile,'type'=>'current_shift','date'=>$date]);
                    }
                }
                else {
                    // not found

                }
            }
            else {
                if(isset($string['speciality']) && !empty($string['speciality'])){
                    $selected_speciality=$string['speciality'];
                }
                else{
                    $selected_speciality=0;
                }

                $params['user_id']=$id;
                $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
                $data_array =  DrsPanel::getDoctorSliders($params);

                return $this->render('/attender/hospital/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'appointment']);
            }
        }
        else {
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
                    return $this->render('/attender/hospital/book_appointment',
                        ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'slots'=>$slots,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'doctorProfile'=>$doctorProfile]);

                    /* return $this->render('/hospital/book_appointment',['defaultCurrrentDay'=>strtotime($date),'shifts'=>$shifts,'doctor'=>$doctor,'type'=>$type]);*/
                }
                else{
                    // not found
                }
            }
            else {
                if(isset($string['speciality']) && !empty($string['speciality'])){
                    $selected_speciality=$string['speciality'];
                }
                else{
                    $selected_speciality=0;
                }
                $params['user_id']=$id;
                $params['filter']=json_encode(array(['type'=>'speciality','list'=>[$selected_speciality]]));
                $data_array =  DrsPanel::getDoctorSliders($params);
                $getShists=DrsPanel::getBookingShifts($parent_id,$date,$id);
                $appointments=DrsPanel::getCurrentAppointments($parent_id,$date,$current_shifts,$getShists);
                if(!empty($appointments)){
                    if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                        $current_shifts=$appointments['shifts'][0]['schedule_id'];
                        $slots = DrsPanel::getBookingShiftSlots($parent_id,$date,$current_shifts,'available');
                    }
                }

                return $this->render('/attender/hospital/speciality-doctor-list',['defaultCurrrentDay'=>strtotime($date),'data_array'=>$data_array,'hospital'=>$hospital,'selected_speciality'=>$selected_speciality,'type'=>$type,'actionType'=>'appointment','appointments'=>$appointments,'current_shifts'=>$current_shifts,]);
            }
        }
    }

    function doctorAppointment($type,$parent_id,$id,$date,$doctor){
        if($type == 'current_appointment'){
            $current_shifts=0; $bookings=array();
            $getShists=DrsPanel::getBookingShifts($parent_id,$date,$id);
            $appointments=DrsPanel::getCurrentAppointments($parent_id,$date,$current_shifts,$getShists);
            if(!empty($appointments)){
                if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                    $current_shifts=$appointments['shifts'][0]['schedule_id'];
                    $bookings = $appointments['bookings'];
                }
            }
            return $this->render('/attender/doctor/current-appointments',
                ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'bookings'=>$bookings,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor]);
        }
        elseif($type == 'current_shift'){
            $current_shifts='';
            $getSlots=DrsPanel::getBookingShifts($parent_id,$date,$id);
            $checkForCurrentShift=DrsPanel::getDoctorCurrentShift($getSlots);
            if(!empty($checkForCurrentShift)){
                $current_affairs=DrsPanel::getCurrentAffair($checkForCurrentShift,$parent_id,$date,$current_shifts,$getSlots);
                if($current_affairs['status'] && empty($current_affairs['error'])){
                    $shifts=$current_affairs['all_shifts'];
                    $appointments=$current_affairs['data'];
                    $current_shifts=$current_affairs['schedule_id'];
                    $is_completed=$current_affairs['is_completed'];
                    $is_started=$current_affairs['is_started'];
                }
                else{
                    $shifts=$appointments=array();$is_completed=0;$is_started=0;
                }
                return $this->render('/attender/doctor/current-affair',['is_completed'=>$is_completed,'is_started'=>$is_started,'Shifts'=>$shifts,'appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>'current_shift','date'=>$date]);

            }
            else{
                // no shifts
            }
        }
        else {
            $type='book';
            $current_shifts=0; $slots=array();
            $getShists=DrsPanel::getBookingShifts($parent_id,$date,$id);
            $appointments=DrsPanel::getCurrentAppointments($parent_id,$date,$current_shifts,$getShists);
            if(!empty($appointments)){
                if(isset($appointments['shifts']) && !empty($appointments['shifts'])){
                    $current_shifts=$appointments['shifts'][0]['schedule_id'];
                    $slots = DrsPanel::getBookingShiftSlots($parent_id,$date,$current_shifts,'available');
                }
            }
            return $this->render('/attender/doctor/appointments',
                ['defaultCurrrentDay'=>strtotime($date),'appointments'=>$appointments,'slots'=>$slots,'type'=>$type,'current_shifts'=>$current_shifts,'doctor'=>$doctor]);
        }
    }

    public function actionAjaxToken(){
        $result=['status'=>false,'msg'=>'Invalid Request.'];
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post=Yii::$app->request->post();
            $current_shifts=$post['shift_id'];
            $doctor_id=$post['doctorid'];
            $date=(isset($post['date']) && !empty($post['date']))?$post['date']:date('Y-m-d');
            $slots = DrsPanel::getBookingShiftSlots($doctor_id,$date,$current_shifts,'available');              echo $this->renderAjax('_slots',['slots'=>$slots,'doctor_id'=>$doctor_id]); exit();
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
            echo $this->renderAjax('_bookings',['bookings'=>$bookings,'doctor_id'=>$doctor_id]); exit();
        }
    }

    public function actionBookingConfirm(){
        $id=Yii::$app->user->id;
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $slot_id=explode('-',$post['slot_id']);
            $slot=UserScheduleSlots::find()->andWhere(['id'=>$slot_id[1]])->one();
            if(!empty($slot)){
                $doctor_id=$slot->user_id;
                $doctorProfile=UserProfile::find()->where(['user_id'=>$doctor_id])->one();
                if(!empty($doctorProfile)){
                    $doctor=User::findOne($doctorProfile->user_id);
                    $schedule=UserSchedule::findOne($slot->schedule_id);
                    $model= new AppointmentForm();
                    $model->doctor_id=$doctor->id;
                    $model->slot_id=$slot->id;
                    $model->schedule_id=$slot->schedule_id;
                    return $this->renderAjax('/common/_booking_confirm.php',
                        ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,   'address'=>UserAddress::findOne($schedule->address_id), 'model'=> $model, 'userType'=>'attender'
                        ]);
                }

            }


        }
        return NULL;
    }

    public function actionBookingConfirmStep2(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $id=Yii::$app->user->id;
            $slot_id=$post['slot_id'];
            $slot=UserScheduleSlots::find()->andWhere(['id'=>$slot_id])->one();
            if(!empty($slot)){
                $doctor_id=$slot->user_id;
                $doctorProfile=UserProfile::find()->where(['user_id'=>$doctor_id])->one();
                if(!empty($doctorProfile)){
                    $doctor=User::findOne($doctorProfile->user_id);
                    $schedule=UserSchedule::findOne($slot->schedule_id);
                    $model= new AppointmentForm();
                    $model->doctor_id=$doctor->id;
                    $model->slot_id=$slot->id;
                    $model->schedule_id=$slot->schedule_id;
                    $model->user_name=$post['name'];
                    $model->user_phone=$post['phone'];
                    $model->user_gender=$post['gender'];
                    return $this->renderAjax('/common/_booking_confirm_step2.php',
                        ['doctor'=>$doctor, 'slot'=>$slot, 'schedule'=>$schedule,   'address'=>UserAddress::findOne($schedule->address_id), 'model'=> $model, 'userType'=>'attender'
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

                            Yii::$app->session->setFlash('success', "Appointment booked successfully.");
                            return $this->redirect(['/attender/appointments']);
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
        return $response;
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
                            $current_affair=$this->renderAjax('current-affair',['appointments'=>$appointments,'type'=>'current-affairs']);
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
            $result['result']=$this->renderAjax('/common/_appointment_date_slider',['doctor_id'=>$user_id,'dates_range' => $dates_range,'date'=>$first,'type'=>$type,'userType'=>'attender']);
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
            echo $this->renderAjax('/attender/doctor/_appointment_shift_slots',['appointments'=>$appointments,'current_shifts'=>$current_shifts,'doctor'=>$doctor,'type'=>$type,'slots'=>$slots,'bookings'=>$bookings]);exit;
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
            echo $this->renderAjax('/common/_booking_detail',['booking'=>$booking,'doctor_id'=>$this->loginUser->id,'userType'=>'attender','booking_type' => $booking_type]); exit();
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

                Yii::$app->session->setFlash('success', "'Appointment Cancelled!'");
            }
            else{
                Yii::$app->session->setFlash('success', $appointment->getErrors());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    /****************************History***************************************/
    public function actionUserStatisticsData(){
        $date=date('Y-m-d');

        $user_id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($user_id);
        $parent_id=$getParentDetails['parent_id'];

        $doctor=User::findOne($parent_id);
        $current_selected=0;
        $checkForCurrentShift=0;
        $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
        $appointments=$shiftAll=$typeCount=[];
        $getSlots=DrsPanel::getBookingShifts($parent_id,$date,$user_id);
        if(!empty($getSlots)){
            $checkForCurrentShift=$getSlots[0]['schedule_id'];
            $current_selected = $checkForCurrentShift;
            $getAppointments=DrsPanel::appointmentHistory($parent_id,$date,$current_selected,$getSlots,$typeselected);
            $shiftAll=DrsPanel::getDoctorAllShift($parent_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
            $appointments=$getAppointments['bookings'];
            $typeCount=$getAppointments['type'];
        }
        return $this->render('/attender/history-statistics/user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'defaultCurrrentDay'=>strtotime($date),'doctor'=>$doctor,'current_selected'=>$current_selected]);
    }

    public function actionAjaxUserStatisticsData(){
        $user_id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($user_id);
        $parent_id=$getParentDetails['parent_id'];

        $doctor=User::findOne($parent_id);
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $days_plus=$post['plus'];
            $operator=$post['operator'];
            $date = date('Y-m-d',strtotime($operator.$days_plus.' days' ,$post['date']));

            $current_selected=0;
            $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
            $checkForCurrentShift=0;
            $appointments=$shiftAll=$typeCount=[];
            $getSlots=DrsPanel::getBookingShifts($parent_id,$date,$user_id);
            if(!empty($getSlots)){
                $checkForCurrentShift=$getSlots[0]['schedule_id'];
                $current_selected = $checkForCurrentShift;
                $getAppointments=DrsPanel::appointmentHistory($parent_id,$date,$current_selected,$getSlots,$typeselected);
                $shiftAll=DrsPanel::getDoctorAllShift($parent_id,$date, $checkForCurrentShift,$getSlots,$current_selected);
                $appointments=$getAppointments['bookings'];
                $typeCount=$getAppointments['type'];
            }
            return $this->renderAjax('/attender/history-statistics/_user-statistics-data',['typeCount'=>$typeCount,'typeselected'=>$typeselected,'appointments'=>$appointments,'shifts'=>$shiftAll,'date'=>strtotime($date),'doctor'=>$doctor,'current_shifts'=>$current_selected]);
        }
    }

    public function actionAjaxStatisticsData(){
        $user_id=Yii::$app->user->id;
        $getParentDetails=DrsPanel::getParentDetails($user_id);
        $parent_id=$getParentDetails['parent_id'];

        $result['status']=false;
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $date=($post['date'])?date('Y-m-d',strtotime($post['date'])):date('Y-m-d');
            if(isset($post['type'])){
                $typeselected=($post['type']=='online')?UserAppointment::BOOKING_TYPE_ONLINE:UserAppointment::BOOKING_TYPE_OFFLINE;
            }
            else{
                $typeselected=UserAppointment::BOOKING_TYPE_ONLINE;
            }
            $checkForCurrentShift=(isset($post['shift_id']))?$post['shift_id']:0;
            $appointments=$shiftAll=$typeCount=[];
            $getSlots=DrsPanel::getBookingShifts($parent_id,$date,$user_id);
            if(!empty($getSlots)){
                $getAppointments=DrsPanel::appointmentHistory($parent_id,$date,$checkForCurrentShift,$getSlots,$typeselected);
                $appointments=$getAppointments['bookings'];
                $typeCount=$getAppointments['type'];
            }
            $result['status']=true;
            $result['appointments']=$this->renderAjax('/common/_appointment-token',['appointments'=>$appointments,'typeselected'=>$typeselected,'typeCount'=>$typeCount,'userType'=>'attender']);
            $result['typeCount']=$typeCount;
            $result['typeselected']=$typeselected;
        }
        return json_encode($result);
    }

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
            if($groupid != Groups::GROUP_ATTENDER){
                return $this->goHome();
            }
            else{
                return parent::beforeAction($action);
            }
        }
        $this->redirect(array('/'));
    }
}