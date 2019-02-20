<?php
namespace frontend\modules\user\controllers;

use common\components\DrsImageUpload;
use common\models\MetaValues;
use common\models\UserReminder;
use common\models\UserScheduleSlots;
use Yii;
use yii\authclient\AuthAction;
use yii\db\Query;
use yii\filters\AccessControl;
use common\components\DrsPanel;
use yii\base\Exception;
use common\models\Groups;
use common\models\UserFavorites;
use common\models\User;
use common\models\UserProfile;
use common\models\UserAppointment;
use common\models\UserAddress;
use common\models\PatientMemberFiles;
use common\models\PatientMembers;
use frontend\modules\user\models\PatientMemberForm;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;


/**
 * Class PatientController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class PatientController extends \yii\web\Controller
{

    private $loginUser;
    /**
     * @return array
     */
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
            return $this->loginUser['groupid']==Groups::GROUP_PATIENT;
        }
        ],
        ]
        ]
        ];
    }

    public function actionProfile(){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $genderlist=DrsPanel::getGenderList();
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $heightFI = array();
            $heightFI['feet'] = $post['UserProfile']['height'];
            $heightFI['inch'] = $post['UserProfile']['inch'];

            if($post['UserProfile'] || $post['User']){
                $old_image=$userProfile->avatar;
                $userModel->load($post);
                $userProfile->load($post);
                $userProfile->avatar=$old_image;
                $userProfile->avatar=$userProfile->avatar;
                $userProfile->height= json_encode($heightFI);

                if($userModel->groupUniqueNumber(['phone'=>$post['User']['phone'],'groupid'=>$userModel->groupid,'id'=>$userModel->id])){
                    $userModel->addError('phone', 'This phone number already exists.');
                }
                $upload = UploadedFile::getInstance($userProfile, 'avatar');
                if($userModel->save() && $userProfile->save()){
                    if(isset($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                        $imageUpload=DrsImageUpload::updateProfileImageWeb('patients',$id,$upload);
                    }
                    Yii::$app->session->setFlash('success', "Profile Updated.");
                    return $this->redirect(['/patient/profile']);
                }
            }
        }
        if($userProfile->height){
            $height=json_decode($userProfile->height);
            $userProfile->height=$height->feet;
            $userProfile->inch=$height->inch;
        }
        return $this->render('/patient/profile',
            ['userModel'=>$userModel,'userProfile'=>$userProfile,'genderList'=>$genderlist]);
    }

    public function actionMyDoctors(){
        $id=Yii::$app->user->id;
        return $this->render('/patient/my-doctors',
            ['doctors'=> DrsPanel::patientMyDoctorsList($id),
            'id' => $id]
            );
    }

    public function actionRecords(){
        $id=Yii::$app->user->id;
        return $this->render('/patient/records',
            ['doctors'=> DrsPanel::patientDoctorList($id),
            'id' => $id]
            );
    }

    public function actionPatientAppointments($id){
        $member  = PatientMembers::find()->where(['id'=> $id])->one();

        if(!empty($member)){
            $appList= new Query();
            $appList=UserAppointment::find();
            $appList->where(['user_id'=>$member->user_id]);
            $appList->andWhere(['user_name'=>$member->name,'user_phone'=>$member->phone]);
            $appList->all();
            $command = $appList->createCommand();
            $appointments = $command->queryAll();

            return $this->render('/patient/patient-appointments',
                ['appointments'=> $appointments]
            );
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionPatientRecordFiles($slug = NULL){
        $id=Yii::$app->user->id;
        $userModel=$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);
        $UserAddress=UserAddress::findOne(['user_id'=> $id]);
        $PatientRecordFilesSlug  = PatientMembers::find()->andWhere(['slug'=> $slug])->one();
        if(!empty($PatientRecordFilesSlug)){
            $member_id=$PatientRecordFilesSlug->id;
            $records  = PatientMemberFiles::find()->andWhere(['user_id'=> $id])->andWhere(['member_id' => $PatientRecordFilesSlug])->all();
            return $this->render('/patient/patient-record-files',['records'=>$records ,'id'=> $id,'member_id'=>$member_id]);
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddUpdateRecord(){
        if(Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            if(isset($post['PatientMemberFiles'])){

                if(isset($_FILES['PatientMemberFiles'])) {
                    $PatientRecordImages=new PatientMemberFiles();
                    $member_id=$post['PatientMemberFiles']['member_id'];
                    $member  = PatientMembers::find()->where(['id'=> $member_id])->one();
                    $uploads = UploadedFile::getInstances($PatientRecordImages, 'image');
                    foreach ($uploads as $key => $file) {
                        //echo "<pre>"; print_r($file); die;
                            $uploadDir = Yii::getAlias('@storage/web/source/records/');
                            $file_name = $file->name;
                            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                            $image_name=time().rand().'.'.$extension;
                            if($file->saveAs($uploadDir .$image_name )){
                                $imgModelPatient= new PatientMemberFiles();
                                $imgModelPatient->member_id=$member_id;
                                $imgModelPatient->user_id=$member->user_id;
                                $imgModelPatient->image=$image_name;
                                $imgModelPatient->image_base_url=Yii::getAlias('@storageUrl');
                                $imgModelPatient->image_path='/source/records/';
                                $imgModelPatient->image_type=$extension;
                                $imgModelPatient->image_name=$post['PatientMemberFiles']['image_name'];
                                if($imgModelPatient->save()){

                                }
                                else{
                                }
                            }
                            else{
                            }


                    }
                }
                return $this->redirect(['patient-record-files','slug'=>$member->slug]);

            }
            else{
                $member_id=$post['member_id'];
                $type=$post['type'];
                $member  = PatientMembers::find()->where(['id'=> $member_id])->one();
                if($type == 'add'){
                    $recordModel=new PatientMemberFiles();
                    $recordModel->user_id=$member->user_id;
                    $recordModel->member_id=$member_id;
                    echo $this->renderAjax('/patient/_addupdaterecord',['recordModel'=> $recordModel]); exit;
                }
                else{

                }
            }
        }
    }

    public function actionDeleteRecord(){
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $record_id = $post['record_id'];
            $member_id=$post['member_id'];
            $member  = PatientMembers::find()->where(['id'=> $member_id])->one();
            $reminder = PatientMemberFiles::find()->where(['id' => $record_id])->one();
            $reminder->delete();
            Yii::$app->session->setFlash('success', "Record Deleted!");
            return $this->redirect(['patient-record-files','slug'=>$member->slug]);
        }
    }

    public function actionPatientRecordUpdate(){
        if(Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $pid = isset($post['PatientMembers']['id'])?$post['PatientMembers']['id']:'0';

            if(isset($post['PatientMembers'])) {
                $patientrecordData=PatientMembers::findOne($pid);

                $patientrecordData->load($post);

                if($patientrecordData->save())
                {
                    Yii::$app->session->setFlash('success', "Patient Record Updated!");
                }
                return $this->redirect(['/patient/records']);
            }

            if(isset($_FILES['PatientMembersFiles'])) {
                $PatientRecordImages=PatientMembers::findOne($pid);
                $uploads = UploadedFile::getInstances($PatientRecordImages, 'image');
                if(!empty($uploads))
                {
                    $uploadDir = Yii::getAlias('@storage/web/source/hospitals/');
                    foreach ($uploads as $key => $file) {
                        $image_name=time().rand(1,9999).'_'.$key.'.'.$file->extension;
                        if($file->saveAs($uploadDir .$image_name )){
                            $imgModelPatient= new PatientMemberFiles();
                            $imgModelPatient->member_id=$pMember->id;
                            $imgModelPatient->user_id=Yii::$app->user->id;
                            $imgModelPatient->image=$image_name;
                            $imgModelPatient->image_base_url=Yii::getAlias('@storageUrl');
                            $imgModelPatient->image_path='/source/members/';

                            $imgModelPatient->save();
                        }
                    }
                }
            }
            else
            {
                $id=$post['id'];
                $patientrecordDataRow=PatientMembers::findOne($id);
                $patientrecordFilesDataRow=PatientMemberFiles::find()->where(['member_id'=>$id])->one();
                if(empty($patientrecordFilesDataRow)){
                    $patientrecordFilesDataRow = new PatientMemberFiles();
                    $patientrecordFilesDataRow->member_id=$id;
                }
                $genderlist=DrsPanel::getGenderList();
                echo $this->renderAjax('/patient/_editrecord',['model'=> $patientrecordDataRow,'patientmemberData' => $patientrecordFilesDataRow,'genderList' => $genderlist]); exit;
            }
        }
    }

    public function actionAppointments($type =''){
        $id=Yii::$app->user->id;
        if($type == 'upcoming'){
            $appointments=DrsPanel::patientAppoitmentList($id,array(UserAppointment::STATUS_AVAILABLE,UserAppointment::STATUS_PENDING,UserAppointment::STATUS_SKIP,UserAppointment::STATUS_ACTIVE));
        }
        elseif($type == 'past'){
            $appointments=DrsPanel::patientAppoitmentList($id,array(UserAppointment::STATUS_COMPLETED,UserAppointment::STATUS_CANCELLED));
        }
        else{
            $type='all';
            $appointments=DrsPanel::patientAppoitmentList($id,[]);
        }
        return $this->render('/patient/appointments',['type'=>$type,'appointments'=>$appointments]);
    }

    public function actionAppointmentDetails($id){
        $appointment_details  = UserAppointment::find()->andWhere(['id'=> $id])->one();
        if(!empty($appointment_details)) {
            $appointment_doctorData = UserProfile::find()->andWhere(['user_id' => $appointment_details->doctor_id])->one();
            $appointment_hospitalData = UserAddress::find()->andWhere(['id' => $appointment_details->doctor_address_id])->one();
        }
        else{
             $appointment_doctorData = array();
             $appointment_hospitalData = array();
        }
        return $this->render('/patient/_appointment_details',['appointments'=>$appointment_details,'appointment_doctorData' => $appointment_doctorData,'appointment_hospitalData' => $appointment_hospitalData]);
    }

    public function actionAjaxCheckReminder(){
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $user_id = $this->loginUser->id;
            if(isset($post['UserReminder'])){
                $appointment_id = $post['UserReminder']['appointment_id'];
                $reminder = UserReminder::find()->where(['user_id' => $user_id, 'appointment_id' => $appointment_id])->one();
                if (empty($reminder)) {
                    $reminder = new UserReminder();
                }
                $reminder->user_id = $user_id;
                $reminder->appointment_id = $appointment_id;
                $reminder->reminder_date=$post['UserReminder']['reminder_date'];
                $reminder->reminder_time=$post['UserReminder']['reminder_time'];
                $reminder->reminder_datetime=(int) strtotime($post['UserReminder']['reminder_date'].' '.$post['UserReminder']['reminder_time']);;
                $reminder->status='pending';
                if($reminder->save()){
                    return $this->redirect(Yii::$app->request->referrer);
                }
                else{
                    return $this->redirect(Yii::$app->request->referrer);
                }

            }
            else{
                $lists=UserReminder::find()->where(['user_id'=>$user_id])->orderBy('id desc')->one();
                $appointment_id = $post['appointment_id'];

                $reminder = UserReminder::find()->where(['user_id' => $user_id, 'appointment_id' => $appointment_id])->one();
                if (empty($reminder)) {
                    $reminder = new UserReminder();
                    $reminder->user_id = $user_id;
                    $reminder->appointment_id = $appointment_id;
                    $type='Add';
                }
                else{
                    $type='Update';

                }
                $appointment=UserAppointment::findOne($appointment_id);
                $appointment_detail=  DrsPanel::patientgetappointmentarray($appointment);
                echo $this->renderAjax('_addupdatereminder', ['reminder' => $reminder,'type'=>$type,'doctorData' => $appointment_detail]);
                exit;
            }
        }

    }

    public function actionAjaxCheckReminderDelete(){
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            /*  echo '<pre>';
              print_r($post);die;*/
            if(isset($post['appointment_id'])){
                $appointment_id = $post['appointment_id'];
                $reminder = UserReminder::find()->where(['appointment_id' => $appointment_id])->one();
                $reminder->delete();
            }
        }
        Yii::$app->session->setFlash('success', "Reminder Deleted!");
        return $this->redirect(['/patient/reminder']);
    }

    public function actionAjaxCheckReminderList(){
        if(Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $user_id = $this->loginUser->id;
            if(isset($post['UserReminder'])){
                $appointment_id = $post['UserReminder']['appointment_id'];
                $reminder = UserReminder::find()->where(['user_id' => $user_id, 'appointment_id' => $appointment_id])->one();
                if (empty($reminder)) {
                    $reminder = new UserReminder();
                }
                $reminder->user_id = $user_id;
                $reminder->appointment_id = $appointment_id;
                $reminder->reminder_date=$post['UserReminder']['reminder_date'];
                $reminder->reminder_time=$post['UserReminder']['reminder_time'];
                $reminder->reminder_datetime=(int) strtotime($post['UserReminder']['reminder_date'].' '.$post['UserReminder']['reminder_time']);;
                $reminder->status='pending';
                if($reminder->save()){
                    Yii::$app->session->setFlash('success', "Reminder Updated.");
                    return $this->redirect(Yii::$app->request->referrer);
                }
                else{
                    Yii::$app->session->setFlash('error', "Please try again!");
                    return $this->redirect(Yii::$app->request->referrer);
                }

            }
            else{
                $lists=UserReminder::find()->where(['user_id'=>$user_id])->orderBy('id desc')->one();
                $appointment_id = $post['appointment_id'];

                $reminder = UserReminder::find()->where(['user_id' => $user_id, 'appointment_id' => $appointment_id])->one();
                if (empty($reminder)) {
                    $reminder = new UserReminder();
                    $reminder->user_id = $user_id;
                    $reminder->appointment_id = $appointment_id;
                    $type='Add';
                }
                else{
                    $type='Update';

                }
                $appointment=UserAppointment::findOne($appointment_id);
                $appointment_detail=  DrsPanel::patientgetappointmentarray($appointment);
                echo $this->renderAjax('_updatereminder', ['reminder' => $reminder,'type'=>$type,'doctorData' => $appointment_detail]);
                exit;
            }
        }

    }

    public function actionReminder(){
        $user_id=Yii::$app->user->id;
        $reminders=DrsPanel::getPatientReminders($user_id);
        return $this->render('reminder',['reminders' => $reminders]);
    }

    public function actionAjaxCancelAppointment(){
        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();
            $appointment_id=$post['appointment_id'];
            $appointment = UserAppointment::find()->where(['id' => $appointment_id])->one();
            $appointment->status=UserAppointment::STATUS_CANCELLED;
            // echo '<pre>';
            // print_r($appointment);
            // die;
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

    public function actionCustomerCare(){
        $customer = MetaValues::find()->orderBy('id asc')->where(['key'=>8])->all();
        return $this->render('/patient/customer-care', ['customer' => $customer]);
    }

    public function actionFavorite(){
        if(Yii::$app->request->isAjax && Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $data['user_id']=$this->loginUser->id;
            $data['profile_id']=$post['profile_id'];
            $data['status']=UserFavorites::STATUS_FAVORITE;
            return DrsPanel::userFavoriteUpsert($data);

        }
        return NULL;
    }

    public function actionMyPayments(){
        $id=Yii::$app->user->id;
        return $this->render('/patient/my-payments');
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

    /**
     * @inheritdoc
     */
    public function beforeAction($action){
        if (!Yii::$app->user->isGuest) {
            $groupid=Yii::$app->user->identity->userProfile->groupid;
            if($groupid != Groups::GROUP_PATIENT){
                return $this->goHome();
            }
            else{
                return parent::beforeAction($action);
            }
        }
        $this->redirect(array('/'));
    }
}