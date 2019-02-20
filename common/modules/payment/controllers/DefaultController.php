<?php
namespace common\modules\payment\controllers;

use Yii;
use yii\web\Controller;

class DefaultController extends Controller
{    
    
    public function actionIndex()
    {		
	   //$this->trigger('payment.success');        
        return $this->render('index');
               
    }
}
