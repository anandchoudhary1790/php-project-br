<?php

namespace backend\controllers;

use backend\models\search\MetaValuesSearch;
use common\models\MetaKeys;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\MetaValues;

class AddressTypeController extends Controller
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
     * Lists all MetaValue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $metakey=MetaKeys::findOne(['key'=>'address_type']);
        if(!empty($metakey)){
            $searchModel = new MetaValuesSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$metakey->id);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Creates a new MetaValue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate(){
        $model = new MetaValues();
        $metakey=MetaKeys::findOne(['key'=>'address_type']);
        if(!empty($metakey)){
            $model->key=$metakey->id;
            if ($model->load(Yii::$app->request->post())){
                $model->label=$model->value;
                if($model->save()) {
                    return $this->redirect(['index']);
                }
                else{
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
        else{
            throw new NotFoundHttpException('The requested page does not exist.');
        }


    }

    /**
     * Updates an existing MetaValue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id){
        $model = MetaValues::findOne($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

}
