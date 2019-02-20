<?php

namespace backend\controllers;

use common\components\DrsPanel;
use common\models\MetaKeys;
use Yii;
use common\models\MetaValues;
use backend\models\search\MetaValuesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * MetaValuesController implements the CRUD actions for MetaValues model.
 */
class MetaValuesController extends Controller
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
        $searchModel = new MetaValuesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
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

        if ($model->load(Yii::$app->request->post())) {
            if($model->key == 9 || $model->key == 5){
                $model->slug=DrsPanel::metavalues_slugify($model->label);
            }
            if($model->save()){
                return $this->redirect(Yii::$app->request->referrer);
            }
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
    public function actionUpdate($id){
        $model = $this->findModel($id);
        $metakeys=MetaKeys::find()->select(['id','label'])->asArray()->all();

        if ($model->load(Yii::$app->request->post())) {
            if($model->key == 9 || $model->key == 5){
                $model->slug=DrsPanel::metavalues_slugify($model->label);
            }
            if($_FILES) {
                $model->base_path=Yii::getAlias('@frontendUrl');
                $model->file_path='/storage/web/source/'.strtolower(MetaValues::getKeyName($model->key)).'/';
                $image = UploadedFile::getInstance($model, 'image');
                if($image){
                    $uploadDir = Yii::getAlias('@storage/web/source/'.strtolower(MetaValues::getKeyName($model->key)).'/');
                    $image_name=time().rand().'.'.$image->extension;
                    $image->saveAs($uploadDir .$image_name );
                    $model->image=$image_name;
                }
                else{
                    $model->image='default_treatment.jpg';
                }

                $icon = UploadedFile::getInstance($model, 'icon');
                if($icon){
                    $uploadDir = Yii::getAlias('@storage/web/source/'.strtolower(MetaValues::getKeyName($model->key)).'/');
                    $image_name=time().rand().'_icon.'.$icon->extension;
                    $icon->saveAs($uploadDir .$image_name );
                    $model->icon=$image_name;
                }
                else{
                    $model->icon='default_treatment_icon.png';
                }
            }

            if($model->save()){
                return $this->redirect(Yii::$app->request->referrer);
            }

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
    public function actionDelete($id){
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
    protected function findModel($id){
        if (($model = MetaValues::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Lists all MetaValues models.
     * @return mixed
     */
    public function actionTreatment(){
        $searchModel = new MetaValuesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$treatment=9);

        return $this->render('treatment/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTreatmentCreate(){
        $model = new MetaValues();
        $metakeys=MetaValues::find()->where(['key'=>5])->select(['id','label'])->orderBy('id asc')->asArray()->all();

         if (Yii::$app->request->isPost) {
            $post=Yii::$app->request->post();
            $post['MetaValues']['parent_key']=$post['MetaValues']['key'];
            $post['MetaValues']['key']=9;
            $model->load($post);
            $model->save();
            return $this->redirect(['treatment']);
        }

        return $this->render('treatment/create', [
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
    public function actionTreatmentUpdate($id){
        $model = $this->findModel($id);
        $metakeys=MetaValues::find()->where(['key'=>5])->select(['id','label'])->orderBy('id asc')->asArray()->all();

        if (Yii::$app->request->isPost) {
            $post=Yii::$app->request->post();
            $post['MetaValues']['key']=9;
            $post['MetaValues']['parent_key']=$post['MetaValues']['key'];
            $model->load($post);
            $model->save();
            return $this->redirect(['treatment']);
        }

        return $this->render('treatment/update', [
            'model' => $model,'metakeys'=>$metakeys
        ]);
    }

      public function actionFeaturedUpdate() {
        if(Yii::$app->request->isPost && Yii::$app->request->isAjax){
            $id=Yii::$app->request->post('id');
            $is_featured=Yii::$app->request->post('is_featured');
            $model = MetaValues::find()->andWhere(['key'=>9,'id'=>$id])->one();
            $model->popular=($is_featured)?0:1;
            $model->save();
            return $this->renderAjax('featured', ['is_featured' => $is_featured]);
        }
       return false; 
    }


    
}
