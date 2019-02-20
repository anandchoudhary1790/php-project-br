<?php

namespace backend\controllers;

use common\models\Groups;
use common\models\UserProfile;
use Yii;
use common\components\DrsPanel;
use common\models\User;
use backend\models\PatientForm;
use backend\models\search\PatientSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/**
 * PatientController implements the CRUD actions for User model.
 */
class PatientController extends Controller
{
    public function behaviors()
    {
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
    public function actionIndex()
    {
        $searchModel = new PatientSearch();
        $logined=Yii::$app->user->identity;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$logined);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PatientForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->groupid=Groups::GROUP_PATIENT;
            $model->admin_user_id=Yii::$app->user->id;
           
            if($res = $model->signup($model) ){
                if(empty($model->getErrors()))
                return $this->redirect(['update', 'id' => $res->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'roles' => ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')
        ]);
    }

    /**
     * Updates an existing User model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model =$this->findModel($id);
        $userProfile=UserProfile::findOne(['user_id'=>$id]);

        if (Yii::$app->request->post()) {
            $post=Yii::$app->request->post();

            $model->load($post);
            $userProfile->load($post);

            if($model->save() && $userProfile->save()){
                Yii::$app->session->setFlash('alert', [
                    'options'=>['class'=>'alert-success'],
                    'body'=>Yii::t('backend', 'Profile updated!')
                ]);
                return $this->redirect(['update', 'id' => $id]);
            }
        }

        return $this->render('update', [
            'userProfile' => $userProfile,'model'=>$model
        ]);
    }

    public function actionMyDoctors($id)
    {
        $doctors = DrsPanel::patientDoctorList($id);
        return $this->render('my-doctors', [
            'doctors' => $doctors]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->authManager->revokeAll($id);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
