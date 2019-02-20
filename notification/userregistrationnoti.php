<?php
namespace common\components\notification;

use yii\base\Behavior;
use common\models\User;

/**
 * Class LocalFlysystemProvider
 * @author Eugene Terentev <eugene@terentev.net>
 */
class userregistrationnoti implements Behavior
{
    
    public function events(){
		
		return [
		
		  User::EVENT_AFTER_SIGNUP => 'notification'
		
		 ];
		
		
		}
		
	public function notification($e){
	
	   file_put_contents('te09045.txt',print_r($e,true))	;
		
	 }	
		
    
}
