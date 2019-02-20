<?php
namespace common\behaviors;
use yii\base\Behavior;
use yii\web\User;

class NotificationBehavior extends Behavior
{
    
    public $attribute = null;

    public function events()
    {
        return [
            User::EVENT_AFTER_SIGNUP => 'afterSignin'
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterSignin($event)
    {
        
        
        
    }
}
