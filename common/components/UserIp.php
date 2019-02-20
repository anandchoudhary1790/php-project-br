<?php
namespace common\components;

use Yii;

class UserIp {

    /*
     * @Param Null
     * Function used to get system Ip
     * @Return String
     */
    public function getRealIp(){

        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip=$_SERVER['REMOTE_ADDR'];
        }

        $session    =   Yii::$app->session;
        $session->set('IP', $ip);
        return $ip;

    }

    /*
     * @Param $ip as string
     * Function used to user timezone on basis ip
     * @Return String
     */
    public function getIpTimeZone($ip){

        $response   =   [];
        $url    =   'http://ip-api.com/json/' . $ip;
        $ch     =   curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        $data = curl_exec($ch);

        curl_close($ch);

        $response   =   json_decode($data);

        $zone		=	'';
        if(!empty($response))
        {
            if($response->status == 'success'){
                $session    =   Yii::$app->session;
                $session->set('timezone', $response->timezone);
                $zone	=	$response->timezone;
            }
        } else {
            return $timezone = 'Asia/Kolkata';
        }

        if($zone !=''){
            return $zone;
        }else{
            return $timezone = 'Asia/Kolkata';
        }
    }

    /*
     * @Param $Zone as string and $day is integer
     * Function used to get array of date on basis $day
     * @Return Array
     */
    public function getSearchDays($day){

        $days	=	$utcstr	 =	array();
        for ($i = 0; $i < $day; $i++) {
            $time 	= 	date('Y-m-d');
            if (empty($days)) {
                $days[] = strtotime($time);
            } else {
                $time = date('Y-m-d', strtotime($time . '+' . $i . ' day'));;
                $days[] = strtotime($time);
            }
        }

        return $days;
    }

}
?>
