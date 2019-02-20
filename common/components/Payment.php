<?php
namespace common\components;
use common\models\Transaction;
use common\models\UserAppointment;
use common\models\UserAppointmentTemp;
use common\models\UserScheduleSlots;
use Yii;
use common\models\User;

require_once('../../paytm/lib/config_paytm.php');
require_once('../../paytm/lib/encdec_paytm.php');

class Payment {

    public static function walletRechargePaytm($user_id,$appointment_id,$amount) {
       // Payment::includePaytmFiles();
        $amount=Payment::format_number($amount);
        $patient_detail=Payment::get_user_details_by_id($user_id);

        $checkSum = "";
        $paramList = array();

        $ORDER_ID = $user_id."_".$appointment_id."_patient_wallet_".time();
        $CUST_ID = "P_".$user_id;
        $TXN_AMOUNT = 10;

        // Create an array having all required parameters for creating checksum.
        $paramList["MID"] = PAYTM_MERCHANT_MID;
        $paramList["ORDER_ID"] = $ORDER_ID;
        $paramList["CUST_ID"] = $CUST_ID;
        $paramList["INDUSTRY_TYPE_ID"] = INDUSTRY_TYPE_ID;
        $paramList["CHANNEL_ID"] = CHANNEL_ID;
        $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
        $paramList["WEBSITE"] = PAYTM_MERCHANT_WEBSITE;
        $paramList["CALLBACK_URL"] = 'http://205.147.102.6/g/sites/drspanel/api-drspanel/paytm-wallet-callback?appointment_id='.$appointment_id;
        if(!empty($patient_detail['email'])){
            $paramList["EMAIL"] =$patient_detail['email'];

        }
        if(!empty($patient_detail['phone'])){
            $paramList["MOBILE_NO"] =$patient_detail['phone'];

        }
        $checkSum = getChecksumFromArray($paramList,PAYTM_MERCHANT_KEY);
        $paramList["CHECKSUMHASH"] = $checkSum;

        $html='<html><head>
        <title>DRSPANEL</title></head><body>
        <center><h1>Please do not refresh this page...</h1></center>';
        $html.='<form method="post" action="'.PAYTM_TXN_URL.'" name="f1">
        <table border="1">
            <tbody>';

        foreach($paramList as $name => $value) {
            $html.='<input type="hidden" name="' . $name .'" value="' . $value . '">';
        }

        $html.='</tbody></table><script type="text/javascript"> document.f1.submit(); </script></form></body></html>';
        return $html;
    }

    /*public static function includePaytmFiles() {
        $filesArr=get_required_files();
        $searchString=PAYTM_FILES_FIRST;
        if (!in_array($searchString, $filesArr)) {
            require PAYTM_FILES_FIRST;
        }
        $searchString1=PAYTM_FILES_SECOND;
        if (!in_array($searchString1, $filesArr)) {
            require PAYTM_FILES_SECOND;
        }
    }*/

    public static function format_number($number){
        return str_replace(',', '', number_format($number, 2));
    }

    public static function get_user_details_by_id($id) {
        $user=User::findOne($id);
        $output = array();
        if (!empty($user)) {
            $output['id'] = $user->id;
            $output['name'] = $user->userProfile->name;
            $output['email'] = $user->email;
            $output['phone'] = $user->phone;
            $output['country_mobile_code'] = $user->countrycode;
        }
        return $output;
    }

    public static function paytm_wallet_callback($data,$request) {
        if(!empty($data) && isset($data['STATUS'])){
            $status=$data['STATUS'];
            $data=Payment::paytm_status_api($data);
            $appointment_id=$request['appointment_id'];
            $appointment=UserAppointmentTemp::find()->where(['id'=>$appointment_id])->one();
            if($status=="TXN_SUCCESS"){
                $appointment->payment_status=UserAppointment::PAYMENT_COMPLETED;
                if($appointment->save()){
                    $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
                    $appointment_id=$transaction->appointment_id;
                    if($appointment_id > 0){
                        $transaction->status='completed';
                        $transaction->paytm_response=\GuzzleHttp\json_encode($data);
                        if($transaction->save()){
                            $addLog=Logs::transactionLog($transaction->id,'Transaction updated');
                        }
                    }
                    else{
                        $appointment_log=Logs::addAppointment($request['appointment_id'],$data);
                    }

                }
                else{

                }
            }
            elseif($status == "TXN_FAILURE"){
                $appointment->payment_status=UserAppointment::PAYMENT_PENDING;
                if($appointment->save()){

                    $schedule_id=$appointment->schedule_id;
                    $slot_id=$appointment->slot_id;
                    $slot=UserScheduleSlots::find()->where(['id'=>$slot_id,'schedule_id'=>$schedule_id])->one();
                    $slot->status='available';
                    $slot->save();

                    $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
                    $transaction->status='failed';
                    $transaction->paytm_response=\GuzzleHttp\json_encode($data);
                    if($transaction->save()){
                        $addLog=Logs::transactionLog($transaction->id,'Transaction failed');
                    }
                }
            }
            else{
                $appointment->payment_status=UserAppointment::PAYMENT_PENDING;
                if($appointment->save()){
                    $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
                    $appointment_id=$transaction->appointment_id;
                    if($appointment_id > 0){
                        $transaction->status='completed';
                        $transaction->paytm_response=\GuzzleHttp\json_encode($data);
                        if($transaction->save()){
                            $addLog=Logs::transactionLog($transaction->id,'Transaction pending');
                        }
                    }
                    else{
                        $appointment_log=Logs::addAppointment($request['appointment_id'],$data);
                    }
                }
                else{

                }
            }
        }
        else{
            $appointment_id=$request['appointment_id'];
            $appointment=UserAppointmentTemp::find()->where(['id'=>$appointment_id])->one();
            $appointment->payment_status=UserAppointment::PAYMENT_PENDING;
            if($appointment->save()){
                $schedule_id=$appointment->schedule_id;
                $slot_id=$appointment->slot_id;
                $slot=UserScheduleSlots::find()->where(['id'=>$slot_id,'schedule_id'=>$schedule_id])->one();
                $slot->status='available';
                $slot->save();

                $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
                $transaction->status='failed';
                $transaction->paytm_response=\GuzzleHttp\json_encode($data);
                if($transaction->save()){
                    $addLog=Logs::transactionLog($transaction->id,'Transaction failed');
                }
            }
        }
        return $data;

    }

    public static function paytm_status_api($data){

        //Payment::includePaytmFiles();

        header("Pragma: no-cache");
        header("Cache-Control: no-cache");
        header("Expires: 0");


        $ORDER_ID = $data['ORDERID'];
        $requestParamList = array();
        $responseParamList = array();

        $requestParamList = array("MID" => PAYTM_MERCHANT_MID , "ORDERID" => $ORDER_ID);

        $checkSum = getChecksumFromArray($requestParamList,PAYTM_MERCHANT_KEY);
        //$checkSum = $data['CHECKSUMHASH'];
        $requestParamList['CHECKSUMHASH'] = urlencode($checkSum);

        $data_string = "JsonData=".json_encode($requestParamList);
        //echo $data_string;

        $ch = curl_init();                    // initiate curl
        $url = PAYTM_STATUS_QUERY_URL; //Paytm server where you want to post data

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, true);  // tell curl you want to post something
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string); // define what you want to post
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return the output in string format
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch); // execute
        $info = curl_getinfo($ch);

        //file_put_contents('paytm_txn_parm.txt', print_r($data_string, true),FILE_APPEND);
        //file_put_contents('paytm_txn_parm.txt', print_r($output, true),FILE_APPEND);

        $data = json_decode($output, true);

        return $data;

    }
}
?>
