<?php
namespace backend\controllers;

use common\components\keyStorage\FormModel;
use common\components\DrsPanel;
use Yii;
use common\model\Cities;
use common\models\Groups;
use common\models\User;
use common\model\States;
use common\models\FileImport;
use common\models\FileExport;
use backend\models\DoctorForm;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->layout = Yii::$app->user->isGuest || !Yii::$app->user->can('loginToBackend') ? 'base' : 'common';
        return parent::beforeAction($action);
    }

    public function actionSettings()
    {
        $model = new FormModel([
            'keys' => [
                'frontend.maintenance' => [
                    'label' => Yii::t('backend', 'Frontend maintenance mode'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'disabled' => Yii::t('backend', 'Disabled'),
                        'enabled' => Yii::t('backend', 'Enabled')
                    ]
                ],
                'backend.theme-skin' => [
                    'label' => Yii::t('backend', 'Backend theme'),
                    'type' => FormModel::TYPE_DROPDOWN,
                    'items' => [
                        'skin-black' => 'skin-black',
                        'skin-blue' => 'skin-blue',
                        'skin-green' => 'skin-green',
                        'skin-purple' => 'skin-purple',
                        'skin-red' => 'skin-red',
                        'skin-yellow' => 'skin-yellow'
                    ]
                ],
                'backend.layout-fixed' => [
                    'label' => Yii::t('backend', 'Fixed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-boxed' => [
                    'label' => Yii::t('backend', 'Boxed backend layout'),
                    'type' => FormModel::TYPE_CHECKBOX
                ],
                'backend.layout-collapsed-sidebar' => [
                    'label' => Yii::t('backend', 'Backend sidebar collapsed'),
                    'type' => FormModel::TYPE_CHECKBOX
                ]
            ]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('alert', [
                'body' => Yii::t('backend', 'Settings was successfully saved'),
                'options' => ['class' => 'alert alert-success']
            ]);
            return $this->refresh();
        }

        return $this->render('settings', ['model' => $model]);
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

     public function actionShiftList(){
        $result='<option value="">Select Shift Time</option>';
        if(Yii::$app->request->post()){
            $post=Yii::$app->request->post();
            $rst=Drspanel::shiftList(['user_id'=>$post['doctor_id'],'address_id'=>$post['address_id']]);
            if(count($rst)>0){
                foreach ($rst as $key => $item) {
                    $result=$result.'<option value="'.$item->id.'">'.date('h:i a',$item->start_time).' - '.date('h:i a',$item->end_time).'</option>';
                }
            }
        }
         return $result;
    }

    public function actionImportModel(){
       if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post= Yii::$app->request->post();
            $model= new FileImport(DrsPanel::tableName($post['model']));
            return $this->renderAjax('/common-view/'.$post['type'], ['model' => $model,]);
        }
        return false;
    }

    public function actionExportModel(){
        if(Yii::$app->request->post() && Yii::$app->request->isAjax){
            $post= Yii::$app->request->post();
            $model= new FileExport(DrsPanel::tableName($post['model']));
            return $this->renderAjax('/common-view/'.$post['type'], ['model' => $model,]);
        }
        return false;
    }

    public function actionExportModelData(){
        if(Yii::$app->request->post()){
            $post= Yii::$app->request->post();
           // if($post['FileExport']['type']=='Data'){
             $models=User::find()->andWhere(['groupid'=>Groups::GROUP_DOCTOR])->all();   
         
               \moonland\phpexcel\Excel::export([
         
                'models' => $models,
                'fileName' => 'doctor'.rand(),
                    'columns' => [
                        'username:text:name',
                        'email:text:email',
                        'phone:text:phone',
                        'userProfile.dob',
                        [
                                'attribute' => 'userProfile.gender',
                                'header' => 'gender',
                                'format' => 'text',
                                'value' => function($model) {
                                    return  ($model['userProfile']['gender']==1)?'Male':'Female';
                                },
                        ],
                        
                    ],

            ]);

        }
        
    }

    public function actionImportModelData(){
        if(Yii::$app->request->post() && !empty($_FILES)){
            $post= Yii::$app->request->post();
            $fileName=$_FILES['FileImport']['tmp_name'] ;
            $records = \moonland\phpexcel\Excel::widget([
                'mode' => 'import', 
                'fileName' => $fileName, 
                'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
                'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
                'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
            ]);
           
           if(count($records)>0 && isset($records['file']) && count($records['file'])>0){
            $notInsertData=$error=[]; 
                foreach ($records['file'] as $key => $record) { 
                    $model = new DoctorForm();
                    $record['phone']=(string)$record['phone'];
                    if(isset($record['gender'])){
                        if($record['gender']=='Male'){ 
                            $record['gender']=1; 
                        } else if($record['gender']=='Female'){
                            $record['gender']=2;
                        }else{
                            $record['gender']=0;
                        }
                    }
                    else { 
                        $record['gender']=0;
                    }
                    if(isset($record['dob']) && !empty($record['dob'])){
                    	$record['dob']=$record['dob'];
                    }else{
                    	$record['dob']='1970-01-01';
                    }

                    if ($model->load(['DoctorForm'=>$record])) {
                        $model->groupid=Groups::GROUP_DOCTOR;
                        $model->countrycode='91';
                        $model->admin_user_id=Yii::$app->user->id;
                        if($model->signup()){
                            $InsertData[]=$record;
                        }else{
                            $notInsertData[]=$record;
                            $error[]=$model->getErrors();
                        }
                    }
                }
                if(empty($notInsertData) ){
                    $result= ['status'=>1,'msg'=>'Data Inserted'];
                }else{
                    $result= ['status'=>2,'msg'=>'Not Inserted Data' ,'data'=>$notInsertData,'error'=>$error];
                }
           }
            
        }else{
            $result= ['status'=>3,'msg'=>'File does not support.'];
        }
        if($result['status']==1){
        Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-success'],
                        'body'=>Yii::t('backend', $result['msg'])
                    ]);
        }else{
            Yii::$app->session->setFlash('alert', [
                        'options'=>['class'=>'alert-danger'],
                        'body'=>Yii::t('backend', $result['msg'])
                    ]);
        }
        return $this->redirect(['/doctor/index']);
        
    }

    /*
    /*
 \moonland\phpexcel\Excel::export([
         
                'models' => $models,
                'fileName' => 'doctor'.rand(),
                    'columns' => [
                        'username:text:name',
                        [
                                'attribute' => 'content',
                                'header' => 'Content Post',
                                'format' => 'text',
                                'value' => function($model) {
                                    return  $model->username;
                                },
                        ],
                        'created_at:datetime',
                        [
                                'attribute' => 'updated_at',
                                'format' => 'date',
                        ],
                    ],
                    'headers' => [
                        'created_at' => 'Date Created Content',
                    ],
            ]);
    */

}


