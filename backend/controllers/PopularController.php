<?php
namespace backend\controllers;

use Yii;
use common\models\MetaKeys;
use common\models\MetaValues;
use common\models\PopularMeta;
use backend\models\search\MetaValuesSearch;
use backend\models\PopularForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\DrsPanel;


/**
 * PopularController implements the CRUD actions for Page model.
 */
class PopularController extends Controller
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

    /**
     * Lists all Page models.
     * @return mixed
     */
    public function actionIndex()
    {

        $specialities = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>5])->all();

        $treatments = MetaValues::find()->orderBy('id asc')
        ->where(['key'=>9])->all();

        $popularMetaHospital = PopularMeta::find()->where(['key' => 'hospital'])->all();
        $popularMetaSpeciality = PopularMeta::find()->where(['key' => 'speciality'])->all();
        $popularMetaTreatment = PopularMeta::find()->where(['key' => 'treatment'])->all();

        $hospital_data = DrsPanel::getallhospital();

        $model = new PopularForm();

        $speciality_model = PopularMeta::find()->where(['key' => 'speciality'])->one();
        if(!empty($speciality_model)){
            $model->speciality = explode(',', $speciality_model->value);
        }
        $hospital_model = PopularMeta::find()->where(['key' => 'hospital'])->one();
        if(!empty($hospital_model)){
            $model->hospital = explode(',', $hospital_model->value);
        }

            $treatment_model = PopularMeta::find()->where(['key' => 'treatment'])->one();
        if(!empty($treatment_model)){
            $model->treatment = explode(',', $treatment_model->value);
        }


        if (Yii::$app->request->post()) {
            $post = Yii::$app->request->post();
            $postData = $post['PopularForm'];
            if(isset($postData['hospital'])) {
                $hospital_model = PopularMeta::find()->where(['key' => 'hospital'])->one();
                if(!empty($hospital_model))
                {
                    $hospital_model->delete();
                }
                if(!empty($postData['hospital'])){
                    $hospital_model = new PopularMeta();
                    $hospital = implode(',', $postData['hospital']);
                    $hospital_model->value = $hospital;
                    $hospital_model->key = 'hospital';
                    $hospital_model->save();
                }

            }
            if(isset($postData['speciality'])) {
                $speciality_model = PopularMeta::find()->where(['key' => 'speciality'])->one();
                    if(!empty($speciality_model))
                    {
                        $speciality_model->delete();
                    }
                    if(!empty($postData['speciality'])){
                        $speciality_model = new PopularMeta();
                        $speciality = implode(',', $postData['speciality']);
                        $speciality_model->value = $speciality;
                        $speciality_model->key = 'speciality';
                        $speciality_model->save();
                    }
            }
            if(isset($postData['treatment'])) {
                $treatment_model = PopularMeta::find()->where(['key' => 'treatment'])->one();
                if(!empty($treatment_model))
                {
                    $treatment_model->delete();
                }
                if(!empty($postData['treatment']))
                {
                    $treatment_model = new PopularMeta();
                    $treatment = implode(',', $postData['treatment']);
                    $treatment_model->value = $treatment;
                    $treatment_model->key = 'treatment';
                    $treatment_model->save();
                }               
            }
   
        Yii::$app->session->setFlash('alert', [
            'options'=>['class'=>'alert-success'],
            'body'=>Yii::t('backend', 'Popular hospital added!')
            ]);
        return $this->redirect(['index']);
        } 

        else {
            return $this->render('index',['specialities' => $specialities, 'treatments' => $treatments ,'hospitalData' => $hospital_data,'model' => $model,'popularHospital' => $popularMetaHospital,'popularSpeciality' => $popularMetaSpeciality, 'popularTreatment' => $popularMetaTreatment]);
        }
  
    }

     public function actionPopular()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('popular', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Page model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Page();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Page model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Page model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Page model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Page the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Page::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
