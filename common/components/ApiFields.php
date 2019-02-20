<?php
namespace common\components;

use Yii;



class ApiFields
{

    public static function sendOtpFields(){
        $array = array('groupid','mobile'); //countrycode
        return $array;
    }

    public static function doctorShiftUpsertFields(){
        $array = array('user_id','weekday','start_time','end_time','address_id','patient_limit','schedule_id','shift_id'); 
        return $array;
    }

    public static function doctorEduction(){
        return ['user_id', 'collage_name','start','end','education'];
    }

    public static function specialityTreatment(){
        return ['user_id', 'speciality','treatment'];
    }

    public static function addupdateServices(){
        return ['user_id', 'services'];
    }

    public static function addupdateAboutus(){
        return ['user_id', 'description'];
    }

    public static function doctorExperience(){
        return ['user_id', 'hospital_name','start','end'];
    }

    public static function attenderList(){
        return ['doctor_id'];
    }

    public static function shiftList(){
        return ['doctor_id'];
    }

    public static function attenderUpsert(){
        return ['email','name','phone','parent_id','created_by'];
    }
    public static function attenderUpdate(){
        return ['email','name','phone','id','created_by'];
    }

    public static function verifyotpFields(){
        $array = array('mobile','otp','groupid');
        return $array;
    }

    public static function memberUpsertFields(){
        return ['user_id', 'name', 'phone', 'gender'];
    }

    public static function favoriteUpsertFields(){
        return ['user_id', 'profile_id', 'status'];
    }

    public static function signupFields(){
        $array = array('name','email','mobile','groupid','token','device_id','device_type','dob','gender');
        return $array;
    }

    public static function signupFieldsHospital(){
        $array = array('name','email','mobile','groupid','token','device_id','device_type');
        return $array;
    }

    public static function editDoctorFields(){
        $array = array('name','email','dob','countrycode','mobile','gender','user_id','experience','degree','speciality','description'); //blood_group
        return $array;
    }

    public static function editHospitalFields(){
        $array = array('name','email','countrycode','mobile','user_id'); 
        return $array;
    }

    public static function editPatientFields(){
        $array = array('name','email','dob','countrycode','mobile','gender','user_id');
        return $array;
    }

    public static function addShiftFields(){
        $array= array('name','address','city','state','area');
        return $array;
    }


    public static function addressFields(){
        $array= array('type','name','address','city','state','country','mobile');
        return $array;
    }

    public static function updateaddressFields(){
        $array= array('address_id','type','name','address','city','state','country','mobile');
        return $array;
    }

    public static function doctorAppointmentFields(){
        $array=array('user_id','date','slot_id','schedule_id','name','mobile','gender'); //'age',
        return $array;
    }

    public static function patientAppointmentFields(){
        $array=array('doctor_id','user_id','date','slot_id','schedule_id','name','mobile','gender');
        return $array;
    }

    public static function doctorappointmentupdate(){
        $array=array('user_id','doctor_id','appointment_id','status','schedule_id');
        return $array;
    }

    public static function doctoraddarticle(){
        $array=array('user_id','title','body');
        return $array;
    }

    public static function socialFields(){
        $array = array('email','groupid');
        return $array;
    }

    public static function patientaddreminder(){
        $array=array('user_id','appointment_id','date','time');
        return $array;
    }

    public static function addreviewrating(){
        $array=array('user_id','doctor_id','rating','review');
        return $array;
    }

    public static function userRequestFields(){
        return ['request_to','request_from','groupid'];
    }

    public static function updateShiftStatus(){
        $array=array('user_id','doctor_id','date','schedule_id','booking_closed');
        return $array;
    }

    public static function updateshift(){
        $array=array('user_id','doctor_id','schedule_id','status');
        return $array;
    }

    public static function shiftbookingdays(){
        $array=array('address_id','doctor_id','start_time','end_time','next_date');
        return $array;
    }

    public static function liveStatus(){
        $array=array('doctor_id','schedule_id','appointment_date');
        return $array;
    }

     public static function deleteShift(){
        $array=array('doctor_id','schedule_id');
        return $array;
     }
    public static function deleteShiftAddress(){
        $array=array('doctor_id','address_id');
        return $array;
    }

    public static function todayTimingShift()
    {
        $array=array('start_time','end_time','patient_limit','appointment_time_duration','consultation_fees','emergency_fees','consultation_fees_discount','emergency_fees_discount','address_id');
        return $array;
    }
}