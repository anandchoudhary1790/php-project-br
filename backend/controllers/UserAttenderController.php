<?php


namespace backend\controllers;
use Yii;
use backend\models\search\UserAttenderSearch;
use backend\models\search\AttenderSearch;
use yii\web\Controller;
use common\models\UserAttender;
use common\models\User;


/**
 * Application timeline controller
 */
class UserAttenderController extends Controller
{
    public $layout = 'common';
    /**
     * Lists all TimelineEvent models.
     * @return mixed
     */
    public function actionIndex()
    {
       echo 'tete'; die;
       $searchModel = new AttenderSearch();
       
       
    }


}
