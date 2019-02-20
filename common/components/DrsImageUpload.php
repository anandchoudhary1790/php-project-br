<?php
namespace common\components;

use common\models\Groups;
use Intervention\Image\ImageManagerStatic;
use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;
use common\models\PatientMemberFiles;
use common\models\UserAddressImages;
use common\models\UserAddress;
use common\models\UserProfile;

class DrsImageUpload{

    public static function updateProfileImageWeb($userType,$user_id,$upload){
        if(!empty($upload)){
            $userProfile = UserProfile::findOne(['user_id' => $user_id]);
            $uploadDir = Yii::getAlias('@storage/web/source/'.$userType.'/');
            $image_name=time().rand().'.'.$upload->extension;
            $userProfile->avatar=$image_name;
            $userProfile->avatar_path='/storage/web/source/'.$userType.'/';
            $userProfile->avatar_base_url =Yii::getAlias('@frontendUrl');
            $upload->saveAs($uploadDir .$image_name );
            if($userProfile->save()){
                $geturl= DrsPanel::getUserAvator($user_id);
                $img = ImageManagerStatic::make($geturl);
                $img->fit(100, 100);
                $dir=$uploadDir.'thumb/';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $img->save($uploadDir.'thumb/'.$image_name);
            }
        }
        return true;
    }

    public static function updateProfileImageApp($user_id,$files){
        $model = UserProfile::findOne(['user_id' => $user_id]);
        $avatar=$model->avatar_path;
        $response = $file_tmp = $file_name = array();
        $groupid=$model->groupid;
        $model->gender=($model->gender)?$model->gender:0;
        if (isset($files['image']['tmp_name']) && isset($files['image']['name'])) {
            $file_tmp = $files['image']['tmp_name'];
            $file_name = $files['image']['name'];
            $dir = Url::to('@frontend');
            if($groupid==Groups::GROUP_PATIENT){$dirname='patients';}
            else if($groupid==Groups::GROUP_DOCTOR){$dirname='doctors';}
            else if($groupid==Groups::GROUP_HOSPITAL){$dirname='hospitals';}
            else{$dirname='attenders';}
            $uploadDir = Yii::getAlias('@storage/web/source/'.$dirname.'/');
            $model->avatar_path = '/storage/web/source/'.$dirname.'/';
            $model->avatar_base_url = Yii::getAlias('@frontendUrl');
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $newimage = time() . '_'.$dirname.'.' . $extension;
            if (!move_uploaded_file($file_tmp, $uploadDir . $newimage)) {
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Unable to upload image';
            }

            $model->avatar = $newimage;

            if ($model->save()) {
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']= DrsPanel::getUserAvator($user_id);
                $response['message'] = 'Profile image updated';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response["data"]=$model->getErrors();
                $response['message'] = 'Profile image not updated';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Image Required';
        }

        return $response;
    }

    public static function updateAddressImageWeb($address_id,$upload){
        $address= UserAddress::findOne(['id'=>$address_id]);
        if(!empty($upload)) {
            $uploadDir = Yii::getAlias('@storage/web/source/hospitals/');
            $image_name=time().rand().'.'.$upload->extension;
            $address->image=$image_name;
            $address->image_path='/storage/web/source/hospitals/';
            $address->image_base_url =Yii::getAlias('@frontendUrl');
            $upload->saveAs($uploadDir .$image_name );
            $address->save();
        }
        return true;
    }

    public static function updateAddressImage($address_id,$files){
        $address= UserAddress::findOne(['id'=>$address_id]);
        $avatar=$address->image_path;
        $response = $file_tmp = $file_name = array();


        if (isset($files['image']['tmp_name']) && isset($files['image']['name'])) {
            $file_tmp = $files['image']['tmp_name'];
            $file_name = $files['image']['name'];
            $uploadDir = Yii::getAlias('@storage/web/source/user-address/');
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $newimage = time() .rand(). '_add.' . $extension;
            if (!move_uploaded_file($file_tmp, $uploadDir. $newimage)) {
                $response["status"] = 0;
                $response["error"] = true;
                $response['message'] = 'Unable to upload image';
            }
            $address->image_path = '/storage/web/source/user-address/';
            $address->image_base_url = Yii::getAlias('@frontendUrl');
            $address->image=$newimage;
            if ($address->save()) {
                $response["status"] = 1;
                $response["error"] = false;
                $response['data']= DrsPanel::getAddressAvator($address_id);
                $response['message'] = 'Address image updated';
            }
            else{
                $response["status"] = 0;
                $response["error"] = true;
                $response["data"]=$address->getErrors();
                $response['message'] = 'Address image not updated';
            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Image Required';
        }

        return $response;
    }

    public static function updateAddressImageListWeb($address_id,$uploads){
        $address= UserAddress::findOne(['id'=>$address_id]);
        if(!empty($uploads)) {
            $uploadDir = Yii::getAlias('@storage/web/source/hospitals/');
            foreach ($uploads as $key => $file) {
                $image_name=time().rand(1,9999).'_'.$key.'.'.$file->extension;
                if($file->saveAs($uploadDir .$image_name )){
                    $imgModelPatient= new UserAddressImages();
                    $imgModelPatient->address_id=$address_id;
                    $imgModelPatient->image=$image_name;
                    $imgModelPatient->image_base_url=Yii::getAlias('@storageUrl');
                    $imgModelPatient->image_path='/source/hospitals/';
                    if($imgModelPatient->save()){
                    }
                    else {
                        echo '<pre>';
                        print_r($imgModelPatient->getErrors());die;    
                    }
                }
            }
        }
        return true;
    }

    public static function updateAddressImageList($address_id,$files,$typename='images',$type='api'){
        $address= UserAddress::findOne(['id'=>$address_id]);
        $avatar=$address->image_path;
        $response = $file_tmp = $file_name = array();


        if (isset($files[$typename]['tmp_name']) && isset($files[$typename]['name'])) {
            if($type == 'web'){
                $file_tmps = $files[$typename]['tmp_name']['image'];
                $file_names = $files[$typename]['name']['image'];
            }
            else{
                $file_tmps = $files[$typename]['tmp_name'];
                $file_names = $files[$typename]['name'];
            }
            $uploadDir = Yii::getAlias('@storage/web/source/user-address/');


            foreach($file_names as $key=>$file_name){
                if(!empty($file_name)){
                    $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                    $newimage = time() .rand(). '_add.' . $extension;
                    $file_tmp=$file_tmps[$key];

                    if (!move_uploaded_file($file_tmp, $uploadDir. $newimage)) {
                        $response["status"] = 0;
                        $response["error"] = true;
                        $response['message'] = 'Unable to upload image';
                    }
                    $address_image =new UserAddressImages();
                    $address_image->address_id=$address_id;
                    $address_image->image_path = '/storage/web/source/user-address/';
                    $address_image->image_base_url = Yii::getAlias('@frontendUrl');
                    $address_image->image=$newimage;

                    if ($address_image->save()) {
                        $response[$key]["status"] = 1;
                        $response[$key]["error"] = false;
                        $response[$key]['data']= DrsPanel::getAddressAvator($address_id);
                        $response[$key]['message'] = 'Address image updated';
                    }
                    else{
                        $response[$key]["status"] = 0;
                        $response[$key]["error"] = true;
                        $response[$key]["data"]=$address_image->getErrors();
                        $response[$key]['message'] = 'Address image not updated';
                    }
                }
                else{
                    $response[$key]["status"] = 0;
                    $response[$key]["error"] = true;
                    $response[$key]["data"]='';
                    $response[$key]['message'] = 'Temp file not found';
                }       

            }
        }
        else{
            $response["status"] = 0;
            $response["error"] = true;
            $response['message'] = 'Image Required';
        }

        return $response;
    }

    public static function updateMemberImageWeb($member_id,$upload){
        if(!empty($upload)){
            $uploadDir = Yii::getAlias('@storage/web/source/records/');
            $image_name=time().rand().'.'.$upload->extension;


            
        }
        return true;
    }

    public static function memberImages($model,$record_label,$files){

        if(count($files)>0){
            $file_count=count($files['file']['tmp_name']);

            for($i=0;$i<$file_count;) {
                $photos= new PatientMemberFiles();
                $photos->member_id=$model->id;
                $photos->user_id=$model->user_id;
                $photos->image_name=$record_label;
                $file_tmp = $files['file']['tmp_name'];
                $file_name = $files['file']['name'];
                $uploadDir = Yii::getAlias('@storage/web/source/records/');
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $newimage=time().rand().'.'.$extension;
                //$newimage = time() .rand(1,9999).'_'.$i.'.'. $extension;
                
                if (move_uploaded_file($file_tmp, $uploadDir.$newimage)) {
                    $photos->image_base_url=Yii::getAlias('@storageUrl');
                    $photos->image_path='/source/records/';
                    $photos->image_type=$extension;
                    $photos->image=$newimage;
                    $photos->save();
                }
                $i++;
            }
            return true;
        }
        return false;
    }
}

