<?php

namespace common\modules\payment;

use yii\base\

class Module extends \yii\base\Module
{
    /**
     * @var string
     */
    public $controllerNamespace = 'common\modules\payment\controllers';

    /**
     * @var bool Is users should be activated by email
     */
    public $active = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
	    parent::init();
       // custom initialization code goes here                
       // $this->on('payment.success' ,  [$this ,'paymentsuccess']);
        
    }
    
    
    
    
}
