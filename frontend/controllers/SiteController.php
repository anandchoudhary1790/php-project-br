<?php
namespace frontend\controllers;

use Yii;
use frontend\models\ContactForm;
use yii\web\Controller;
use common\components\DrsPanel;
use common\models\Groups;
use common\models\UserProfile;
use common\models\MetaValues;
use yii\bootstrap\ActiveForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions(){
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
        $drsdata = DrsPanel::homeScreenData();
        return $this->render('index',['drsdata'=>$drsdata]);
    } 

    public function actionGallery(){
        return $this->render('gallery');
    }

    public function actionContact(){
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->contact(Yii::$app->params['adminEmail'])) {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=>Yii::t('frontend', 'Thank you for contacting us. We will respond to you as soon as possible.'),
                    'options'=>['class'=>'alert-success']
                    ]);
                return $this->refresh();
            } else {
                Yii::$app->getSession()->setFlash('alert', [
                    'body'=>\Yii::t('frontend', 'There was an error sending email.'),
                    'options'=>['class'=>'alert-danger']
                    ]);
            }
        }

        return $this->render('contact', [
            'model' => $model
            ]);
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

    public function actionAttenderList(){
        $result='<option value="">Select Attender</option>';
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $rst=Drspanel::attenderList(['parent_id'=>$post['doctor_id'],'address_id'=>$post['address_id']]);
            if(count($rst)>0){
                foreach ($rst as $key => $item) {
                    $result=$result.'<option value="'.$item->id.'">'.$item['userProfile']['name'].'</option>';
                }
            }
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action){
        if (!\Yii::$app->user->isGuest) {
            $groupid=Yii::$app->user->identity->userProfile->groupid;
            if($groupid == Groups::GROUP_DOCTOR){ return $this->redirect(['doctor/appointments']); }
            elseif($groupid == Groups::GROUP_HOSPITAL){ return $this->redirect(['hospital/appointments']);}
            elseif($groupid == Groups::GROUP_ATTENDER){ return $this->redirect(['attender/appointments']);}
            else{
            }
        }
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);

    }



}
