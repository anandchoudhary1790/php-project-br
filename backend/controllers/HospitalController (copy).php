<?php

namespace backend\controllers;

use backend\models\AddAppointmentForm;
use backend\models\DailyPatientLimitForm;
use backend\models\search\UserAppointmentSearch;
use common\components\DrsPanel;
use common\models\Groups;
use common\models\MetaValues;
use common\models\UserAddress;
use common\models\UserAppointment;
use common\models\UserFeesPercent;
use common\models\UserProfile;
use common\models\UserRating;
use common\models\UserSchedule;
use common\models\UserRequest;
use Yii;
use common\models\User;
use backend\models\HospitalForm;
use backend\models\AttenderForm;
use backend\models\search\HospitalSearch;
use backend\models\search\DoctorSearch;
use backend\models\search\AttenderSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\AddScheduleForm;
use yii\web\UploadedFile;


/**
 * HospitalController implements the CRUD actions for User model.
 */
class HospitalController extends Controller{

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
        $searchModel = new HospitalSearch();
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
        $model = new HospitalForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->groupid=Groups::GROUP_HOSPITAL;
            $model->admin_user_id=Yii::$app->user->id;
            if($res = $model->signup()){
                return $this->redirect(['details', 'id' => $res->id]);
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

        $treatments = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>9])->all();
        $services=MetaValues::find()->andWhere(['status'=>1])->andWhere(['Key'=>11])->all();

        $addressProvider = UserAddress::find()->where(['user_id'=>$id])->andWhere(['is_register'=>[1,2]])->one();

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
                return $this->redirect(['details', 'id' => $id]);

            }
            elseif(isset($post['LiveStatus'])){
                $model->admin_status=$post['LiveStatus']['status'];
                if($model->save()){
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile status updated!')
                        ]);
                    return $this->redirect(['details', 'id' => $id]);
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Status not updated!')
                        ]);
                    return $this->redirect(['details', 'id' => $id]);
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
                    return $this->redirect(['details', 'id' => $id]);
                }
                else{
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', 'Rating not updated!')
                        ]);
                    return $this->redirect(['details', 'id' => $id]);
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
                return $this->redirect(['details', 'id' => $id]);

            }
            elseif(isset($post['UserAddress'])){
                $addAddress=new UserAddress();
                $addAddress->load($post);
                $addAddress->save();
                return $this->redirect(['details', 'id' => $id]);

            }
            else{
                $model->load($post);
                $userProfile->load($post);
                    $userProfile->gender=0;
                    $Userspecialities=$post['UserProfile']['speciality'];
                    $Userservices=$post['UserProfile']['services'];
                    $Usertreatments=$post['UserProfile']['treatment'];

                   
                    if(!empty($Userspecialities>0)){
                        $userProfile->speciality=implode(',',$Userspecialities);
                    }
                    if(!empty($Userservices>0)){
                        $userProfile->services=implode(',',$Userservices);
                    }
                    if(!empty($Usertreatments>0)){
                        $userProfile->treatment=implode(',',$Usertreatments);
                    }
                // pr($userProfile);

                if(isset($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['name']['avatar']) && !empty($_FILES['UserProfile']['tmp_name'])) {
                    $upload = UploadedFile::getInstance($userProfile, 'avatar');
                    $uploadDir = Yii::getAlias('@storage/web/source/hospitals/');  
                    $image_name=time().rand().'.'.$upload->extension;
                    $userProfile->avatar=$image_name;
                    $userProfile->gender=0;
                    $userProfile->avatar_path='/storage/web/source/hospitals/';
                    $userProfile->avatar_base_url =Yii::getAlias('@frontendUrl');
                }
                if($model->save() && $userProfile->save()){

                    
                    Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', 'Profile updated!')
                        ]);
                    if(!empty($upload))
                    {
                      $upload->saveAs($uploadDir .$image_name );  
                  }
                  return $this->redirect(['details', 'id' => $id]);

              }
             /* pr($model->getErrors());  
                    pr($userProfile->getErrors());die; */ 
          }
      }

        // pr($degrees);die;
      return $this->render('details', [
        'model' => $model,
        'userProfile'=>$userProfile,'degrees'=>$degrees,'specialities'=>$specialities,'addressProvider' => $addressProvider,
        'shiftType'=>$shiftType,'week_array'=>$week_array,'availibility_days'=>$availibility_days,'services' => $services,'treatments' => $treatments
        ]);
  }

  public function actionLinkedDoctors($id){
    $confirmDrSearch=['status'=>UserRequest::Request_Confirmed,'request_from'=>$id,'groupid'=>Groups::GROUP_HOSPITAL];
    $confirmDr=UserRequest::requestedUser($confirmDrSearch,'request_from');

    $model =$this->findModel($id);
    $searchModel = new DoctorSearch();
    $dataProvider = $searchModel->linkedDoctors(Yii::$app->request->queryParams,$confirmDr,Groups::GROUP_DOCTOR);

    return $this->render('linked-doctors', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
        'model'=>$model
        ]);
    }

    public function actionRequestToDoctors($id){

    $confirmDrSearch=['request_from'=>$id,'groupid'=>Groups::GROUP_HOSPITAL,'status'=>UserRequest::Request_Confirmed];
    $confirmDr=UserRequest::requestedUser($confirmDrSearch,'request_from');

    $model =$this->findModel($id);
    $searchModel = new DoctorSearch();
    $dataProvider = $searchModel->linkedDoctors(Yii::$app->request->queryParams,$confirmDr,Groups::GROUP_DOCTOR);
        if(Yii::$app->request->post()){
            $postData=Yii::$app->request->post();
            if(!empty($postData)){
                foreach ($postData['RequestForm']['id'] as $key => $value) {

                 $model=$this->findModel($value);
                 if(count($model)>0){
                    $post['groupid']=Groups::GROUP_HOSPITAL;
                    $post['request_from']=$id;
                    $post['request_to']=$value;
                    $result=UserRequest::updateStatus($post,'Add');

                }

                }
                 if($result){
                    return $this->redirect(['hospital/request-to-doctors','id' => $id]);

                }
            }

        }
        return $this->render('request-to-doctors', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
            'id' => $id
            ]);
 
    }

public function actionRequestSend(){
    if(Yii::$app->request->isAjax && Yii::$app->request->post()){
        $post=Yii::$app->request->post();
        $model=$this->findModel($post['request_from']);
        if(count($model)>0){
            $post['groupid']=$model->groupid;
            $result=UserRequest::updateStatus($post,'Add');
            if($result){
                return true;
            }
        }
    }
    return false;
}


public function actionUpdateAddressModal(){
    if(Yii::$app->request->post()){
        $post = Yii::$app->request->post();
        if(isset($post['UserAddress'])){
            $address=UserAddress::findOne($post['UserAddress']['id']);
            $address->load($post);
            $address->save();

            return $this->redirect(['details', 'id' => $post['UserAddress']['user_id']]);

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
                if(count($post['AttenderForm']['shift_id'])>0)
                $post['AttenderForm']['shift_id']=implode(',', $post['AttenderForm']['shift_id']);
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
            return $this->redirect(['doctor/index']);
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
        return $this->render('/user-attender/details', [
            'model' => $model,
            'shiftModels'=>$shiftModels,
            'userProfile'=>$userProfile,
            'hospitals'=>$addressList,
            'shifts'=>$shiftList,
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
}
