<?php


namespace backend\controllers;
use Yii;
use backend\models\search\UserDirectorySearch;
use yii\web\Controller;
use common\models\UserDirectory;
use common\models\User;

/**
 * Application timeline controller
 */
class UserDirectoryController extends Controller
{
    public $layout = 'common';
    /**
     * Lists all TimelineEvent models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $allModels=User::find()->all();
        \moonland\phpexcel\Excel::export([
    'models' => $allModels,
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
      /* \moonland\phpexcel\Excel::widget([
    'models' => $allModels,
    'mode' => 'export', //default value as 'export'
   'columns' => ['username','email','phone'], //without header working, because the header will be get label from attribute label. 
    'headers' => ['username' => 'Dr Name','email' => 'email', 'phone' => 'phone'], 
]);
        /*
        $searchModel = new UserDirectorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort = [
            'defaultOrder'=>['created_at'=>SORT_DESC]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); */
    }


}
