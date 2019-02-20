<?php
namespace common\components;

use common\models\Transaction;
use common\models\TransactionLog;
use common\models\UserAppointment;
use common\models\UserAppointmentLogs;
use common\models\UserAppointmentTemp;
use Yii;
use yii\db\Query;
use yii\helpers\Url;


class Logs
{
    public static function addAppointment($appointment_id,$data){
        $appointment= UserAppointmentTemp::findOne($appointment_id);
        $appointmentlog=new UserAppointment();
        $appointmentlog->booking_id=$appointment->booking_id;
        $appointmentlog->booking_type=$appointment->booking_type;
        $appointmentlog->type=$appointment->type;
        $appointmentlog->token=$appointment->token;
        $appointmentlog->user_id=$appointment->user_id;
        $appointmentlog->user_name=$appointment->user_name;
        $appointmentlog->user_age=$appointment->user_age;
        $appointmentlog->user_phone=$appointment->user_phone;
        $appointmentlog->user_address=$appointment->user_address;
        $appointmentlog->user_gender=$appointment->user_gender;
        $appointmentlog->doctor_id=$appointment->doctor_id;
        $appointmentlog->doctor_name=$appointment->doctor_name;
        $appointmentlog->doctor_address=$appointment->doctor_address;
        $appointmentlog->doctor_address_id=$appointment->doctor_address_id;
        $appointmentlog->doctor_phone=$appointment->doctor_phone;
        $appointmentlog->doctor_fees=$appointment->doctor_fees;
        $appointmentlog->date=$appointment->date;
        $appointmentlog->weekday=$appointment->weekday;
        $appointmentlog->start_time=$appointment->start_time;
        $appointmentlog->end_time=$appointment->end_time;
        $appointmentlog->shift_name=$appointment->shift_name;
        $appointmentlog->schedule_id=$appointment->schedule_id;
        $appointmentlog->slot_id=$appointment->slot_id;
        $appointmentlog->book_for=$appointment->book_for;
        $appointmentlog->payment_type=$appointment->payment_type;
        $appointmentlog->service_charge=$appointment->service_charge;
        $appointmentlog->status=$appointment->status;
        $appointmentlog->payment_status=$appointment->payment_status;
        $appointmentlog->is_deleted=$appointment->is_deleted;
        $appointmentlog->deleted_by=$appointment->deleted_by;
        $appointmentlog->created_by=$appointment->created_by;
        $appointmentlog->created_by_id=$appointment->created_by_id;
        if($appointmentlog->save()){
            $addLog=Logs::appointmentLog($appointmentlog->id,'Appointment added by patient');
            $transaction=Transaction::find()->where(['temp_appointment_id'=>$appointment_id])->one();
            $transaction->appointment_id=$appointmentlog->id;
            $status=$data['STATUS'];
            if($status=="TXN_SUCCESS"){
                $transaction->status='completed';
            }
            else{
                $transaction->status='pending';
            }
            $transaction->paytm_response=\GuzzleHttp\json_encode($data);
            if($transaction->save()){
                $addLog=Logs::transactionLog($transaction->id,'Transaction updated');
            }
        }

        return true;
    }

    public static function addTransactionRow($appointment_id,$temp_id,$type = 'pay',$txn_type ='booking',$service_charge = 0){
        if($appointment_id > 0){
            $appointment=UserAppointment::findOne($appointment_id);
        }
        else{
            $appointment=UserAppointmentTemp::findOne($temp_id);
        }
        $transaction=new Transaction();
        $transaction->type=$type;
        $transaction->txn_type=$txn_type;
        $transaction->user_id=$appointment->user_id;
        $transaction->appointment_id=$appointment_id;
        $transaction->temp_appointment_id=$temp_id;
        $transaction->payment_type=$appointment->payment_type;
        $transaction->base_price=$appointment->doctor_fees;
        $transaction->cancellation_charge=0;
        $transaction->txn_amount=$appointment->service_charge;
        $transaction->originate_date=date('Y-m-d H:i:s');
        $transaction->txn_date=date('Y-m-d');
        $transaction->paytm_response = NULL;
        if($appointment->payment_type == UserAppointment::PAYMENT_TYPE_PAYTM){
            $transaction->status='pending';
        }
        else{
            $transaction->status='completed';
        }
        if($transaction->save()){
            $addLog=Logs::transactionLog($transaction->id,'Transaction row added');
        }
        return true;
    }

    public static function transactionLog($transaction_id,$comment){
        $transaction=Transaction::findOne($transaction_id);

        $transactionlog=new TransactionLog();
        $transactionlog->txn_id=$transaction_id;
        $transactionlog->type=$transaction->type;
        $transactionlog->txn_type=$transaction->txn_type;
        $transactionlog->user_id=$transaction->user_id;
        $transactionlog->appointment_id=$transaction->appointment_id;
        $transactionlog->temp_appointment_id=$transaction->temp_appointment_id;
        $transactionlog->payment_type=$transaction->payment_type;

        $transactionlog->base_price=$transaction->base_price;
        $transactionlog->cancellation_charge=0;
        $transactionlog->txn_amount=$transaction->txn_amount;

        $transactionlog->originate_date=$transaction->originate_date;
        $transactionlog->txn_date=$transaction->txn_date;
        $transactionlog->paytm_response=$transaction->paytm_response;
        $transactionlog->status=$transaction->status;
        $transactionlog->comment=$comment;
        $transactionlog->save();

        return true;

    }

    public static function appointmentLog($appointment_id,$comment){
        $appointment=UserAppointment::findOne($appointment_id);

        $appointmentlog=new UserAppointmentLogs();
        $appointmentlog->appointment_id=$appointment_id;
        $appointmentlog->booking_id=$appointment->booking_id;
        $appointmentlog->booking_type=$appointment->booking_type;
        $appointmentlog->type=$appointment->type;
        $appointmentlog->token=$appointment->token;
        $appointmentlog->user_id=$appointment->user_id;
        $appointmentlog->user_name=$appointment->user_name;
        $appointmentlog->user_age=$appointment->user_age;
        $appointmentlog->user_phone=$appointment->user_phone;
        $appointmentlog->user_address=$appointment->user_address;
        $appointmentlog->user_gender=$appointment->user_gender;
        $appointmentlog->doctor_id=$appointment->doctor_id;
        $appointmentlog->doctor_name=$appointment->doctor_name;
        $appointmentlog->doctor_address=$appointment->doctor_address;
        $appointmentlog->doctor_address_id=$appointment->doctor_address_id;
        $appointmentlog->doctor_phone=$appointment->doctor_phone;
        $appointmentlog->doctor_fees=$appointment->doctor_fees;
        $appointmentlog->date=$appointment->date;
        $appointmentlog->weekday=$appointment->weekday;
        $appointmentlog->start_time=$appointment->start_time;
        $appointmentlog->end_time=$appointment->end_time;
        $appointmentlog->shift_name=$appointment->shift_name;
        $appointmentlog->schedule_id=$appointment->schedule_id;
        $appointmentlog->slot_id=$appointment->slot_id;
        $appointmentlog->book_for=$appointment->book_for;
        $appointmentlog->payment_type=$appointment->payment_type;
        $appointmentlog->service_charge=$appointment->service_charge;
        $appointmentlog->status=$appointment->status;
        $appointmentlog->payment_status=$appointment->payment_status;
        $appointmentlog->is_deleted=$appointment->is_deleted;
        $appointmentlog->deleted_by=$appointment->deleted_by;
        $appointmentlog->created_by=$appointment->created_by;
        $appointmentlog->created_by_id=$appointment->created_by_id;
        $appointmentlog->comment=$comment;
        $appointmentlog->save();
        return true;

    }
}