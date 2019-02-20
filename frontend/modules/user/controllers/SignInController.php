<?php

namespace frontend\modules\user\controllers;

use Yii;
use yii\authclient\AuthAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\widgets\ActiveForm;
use common\models\User;
use common\models\Groups;
use common\components\DrsPanel;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\LoginFormType;
use frontend\modules\user\models\SignupForm;




/**
 * Class SignInController
 * @package frontend\modules\user\controllers
 * @author Eugene Terentev <eugene@terentev.net>
 */
class SignInController extends \yii\web\Controller
{

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
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'signup','vendor-signup','login', 'check-otp','request-password-reset', 'reset-password', 'oauth', 'activation','ajax-signup','login-ajax','otp-verify','ajax-unique',
                        ],
                        'allow' => true,
                        'roles' => ['?']
                    ],
                    [
                        'actions' => [
                            'signup','vendor-signup','login','check-otp', 'request-password-reset', 'reset-password', 'oauth', 'activation','ajax-signup','login-ajax','otp-verify','ajax-unique',
                        ],
                        'allow' => false,
                        'roles' => ['@'],
                        'denyCallback' => function () {
                            return Yii::$app->controller->redirect(['/user/default/index']);
                        }
                    ],
                    [
                        'actions' => ['logout','vendor-contact','request-event'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ]
            ]
        ];
    }

    public function actionLogin(){
        $error=true;
        $model = new LoginFormType();
        $model->scenario = 'login';
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {

            if($userModel=$model->OtpScreen()) {
                $userModel->otp=1234;
                $userModel->save();
                return $this->redirect(['/login-otp/'.$userModel->access_token]);
            }else{
                $error=false;
                $model->addError('type','Mobile Number Does not exists');
            }
        }
        if($model->getErrors() && $error){
            $errors=$model->getErrors();
            $model->addError('type',$errors[$model->type][0] );
        }
        return $this->render('login', [
            'model' => $model
        ]);
    }

    public function actionLoginAjax(){
        $model = new LoginForm();
        $result=['status'=>false];
        if(Yii::$app->request->post() && Yii::$app->request->isAjax) {
            $model->load(Yii::$app->request->post());
            if($model->getUser()){
                $user=$model->getUser();
                $user->otp='1234';
                $user->mobile_verified=0;
                if($user->save()){
                    $data=$this->renderAjax('otp-verify',['user'=>$model->getUser()]);
                    $result=['status'=>true,'error'=>false,'data'=>$data];
                }
                else{
                    $data=['identity'=>'Validation Errors'];
                    $result=['status'=>true,'error'=>true,'data'=>$data];
                }
            }else{
                if($model->getErrors()){
                    $data=$model->getErrors();
                }else{
                    $data=['identity'=>'Mobile number not register with '.Groups::allgroups($model->groupid)];
                }

                $result=['status'=>true,'error'=>true,'data'=>$data];
            }
        }
        return json_encode($result);
    }

    public function actionAjaxSignup()
    {
        $result=['status'=>true,'error'=>true];
        $model = new SignupForm();
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            $signup= Yii::$app->request->post();
            $groupid=$signup['SignupForm']['groupid'];
            if($groupid != Groups::GROUP_HOSPITAL){
                $model->scenario = 'user';
                $user_type=($groupid== Groups::GROUP_PATIENT)?'patient':'doctor';
                $titleList= DrsPanel::prefixingList($user_type);
            }
            $model->load($signup);
            $signup=$signup['SignupForm'];
            $model->username=$signup['email'];
            $user=$model->signup(Yii::$app->request->post());
            if (!$model->getErrors()) {
                $data=$this->renderAjax('otp-verify',['user'=>$user]);
                $result=['status'=>true,'error'=>false,'data'=>$data];
            }
            else{
                $getErrors=DrsPanel::validationErrorMessage($model->getErrors());
                echo "<pre>"; print_r($getErrors);die;
            }
        }
       return json_encode($result);
    }

    public function actionAjaxUnique(){
    $result=['email'=>false,'phone'=>false];
    if(Yii::$app->request->isAjax && Yii::$app->request->post()) {
        $post=Yii::$app->request->post('SignupForm');
        $result=User::ajaxUnique($post);
    }

    return json_encode($result);
}

    public function actionOtpVerify(){
        $model = new LoginForm();
        $model->scenario = 'otp';
         if (Yii::$app->request->isAjax) {
                $model->load($_POST);
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
        }
        if (Yii::$app->request->post()){
            $model->load(Yii::$app->request->post());
            if($model->getUser()){
                if($model->login()){
                    $groupid=Yii::$app->user->identity->userProfile->groupid;
                    if($groupid == Groups::GROUP_DOCTOR){
                        if(Yii::$app->user->identity->admin_status == User::STATUS_ADMIN_LIVE_APPROVED || Yii::$app->user->identity->admin_status == User::STATUS_ADMIN_APPROVED){
                            return $this->redirect(['doctor/appointments']);
                        } else{
                            return $this->redirect(['doctor/edit-profile']);
                        }
                    }
                    elseif($groupid == Groups::GROUP_HOSPITAL) {
                        if (Yii::$app->user->identity->admin_status == User::STATUS_ADMIN_LIVE_APPROVED || Yii::$app->user->identity->admin_status == User::STATUS_ADMIN_APPROVED) {
                            return $this->redirect(['hospital/appointments']);
                        } else {
                        return $this->redirect(['hospital/edit-profile']);
                        }
                    }
                    elseif($groupid == Groups::GROUP_ATTENDER){ return $this->redirect(['attender/appointments']);}
                    elseif($groupid == Groups::GROUP_PATIENT){
                        //return $this->goHome();
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                    else{ return $this->goHome();}

                    Yii::$app->getSession()->setFlash('success','Your logined successfully.');
                  return $this->goHome();
                }else{
                    Yii::$app->getSession()->setFlash('error','Invalid OTP Please Try Again.');
                }
            }else{
                Yii::$app->getSession()->setFlash('error','Invalid OTP Please Try Again.');
            }

        }
        return $this->goHome();
    }

    public function actionCheckOtp($token=null){
       
        $model = new LoginFormType('otp');
        if (Yii::$app->request->isAjax) { 
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) ) {
 
            if($model->loginBy($token)){ 
                if($model->getErrors()){
                    $errors=$model->getErrors(); 
                    $model->addError('type',$errors[$model->type][0] );
                } else{
                    return $this->goHome();
                }
            }
        }
        if($model->getErrors()){
                    $errors=$model->getErrors(); 
                    $model->addError('type',$errors[$model->type][0] );
        } 

        $users=User::checkLoginType($token);
        return $this->render('otp', [
            'model' => $model,
            'users'=>$users,
        ]);
    }

    /**
     * @return Response
     */
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return string|Response
     */
    public function actionSignup(){
        $model = new SignupForm();
        $titleList= [];
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        
        if (Yii::$app->request->post()) {
            $signup= Yii::$app->request->post();
            $groupid=$signup['SignupForm']['groupid'];
            if($groupid!=5){
                $model->scenario = 'user';
                $user_type=($groupid==3)?'patient':'doctor';
                $titleList= DrsPanel::prefixingList($user_type);
            }
            $model->load($signup);
            $signup=$signup['SignupForm'];
            $username=explode("@", $signup['email']);
            $model->username=$username[0];
            $user = $model->signup(Yii::$app->request->post());

            if ($user) {
                if ($model->shouldBeActivated()) {
                    Yii::$app->getSession()->setFlash('alert', [
                        'body' => Yii::t(
                            'frontend',
                            'Your account has been successfully created. Check your email for further instructions.'
                        ),
                        'options' => ['class' => 'alert-success']
                    ]);
                } 
                return $this->redirect(['/login-otp/'.$user->access_token]);
            }
        }
        return $this->render('signup', [
            'model' => $model,
            'titleList' => $titleList,
        ]);
    }

    /**
     * @param $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionActivation($token){
        $token = UserToken::find()
            ->byType(UserToken::TYPE_ACTIVATION)
            ->byToken($token)
            ->notExpired()
            ->one();

        if (!$token) {
            throw new BadRequestHttpException;
        }

        $user = $token->user;
        $user->updateAttributes([
            'status' => User::STATUS_ACTIVE
        ]);
        $token->delete();
        Yii::$app->getUser()->login($user);
        Yii::$app->getSession()->setFlash('alert', [
            'body' => Yii::t('frontend', 'Your account has been successfully activated.'),
            'options' => ['class' => 'alert-success']
        ]);

        return $this->goHome();
    }
}
