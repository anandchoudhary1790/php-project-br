<?php

namespace backend\controllers;

use common\models\MetaKeys;
use Yii;
use common\models\MetaValues;
use backend\models\search\MetaValuesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MetaValuesController implements the CRUD actions for MetaValues model.
 */
class SettingsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all MetaValues models.
     * @return mixed
     */
    public function actionIndex()
    {
        $key=array();
        $metakey=MetaKeys::findOne(['key'=>'customer_care']);
        if(!empty($metakey)){
            $key[]=$metakey->id;
        }

        $metakey=MetaKeys::findOne(['key'=>'social_links']);
        if(!empty($metakey)){
            $key[]=$metakey->id;
        }

        $metakey=MetaKeys::findOne(['key'=>'slider_limit']);
        if(!empty($metakey)){
            $key[]=$metakey->id;
        }

        if(!empty($key)){
            $searchModel = new MetaValuesSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$key);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');




    }

    /**
     * Displays a single MetaValues model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MetaValues model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MetaValues();
        $metakeys=MetaKeys::find()->select(['id','label'])->asArray()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,'metakeys'=>$metakeys
        ]);
    }

    /**
     * Updates an existing MetaValues model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $metakeys=MetaKeys::find()->select(['id','label'])->asArray()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,'metakeys'=>$metakeys
        ]);
    }

    /**
     * Deletes an existing MetaValues model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MetaValues model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MetaValues the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MetaValues::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
