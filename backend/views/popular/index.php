<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;
use dosamigos\multiselect\MultiSelect;
use common\models\UserProfile;


/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */


$hospital_list = array();$speciality_list=$treatment_list=array();



foreach ($hospitalData as $h_key=>$hospital) {

    foreach ($popularHospital as $key => $value) {

        $dataValue = explode(',', $value['value']);

        foreach ($dataValue as  $valueData) {

            if($valueData == $hospital->user_id)
            {
                // unset($hospital->user_id);
            }

        } 
    }
    $hospital_list[$hospital->user_id] = $hospital->name;
}


foreach ($specialities as $h_key=>$speciality) {
// pr($speciality);

    foreach ($popularSpeciality as $key => $value) {

        $dataValue = explode(',', $value['value']);

        foreach ($dataValue as  $valueData) {

            if($valueData == $speciality->value) {
                // unset($speciality->value);
                // unset($speciality->label);
            }

        } 
    }
    $speciality_list[$speciality->value] = $speciality->label;
}

foreach ($treatments as $h_key=>$treatment) {

    foreach ($popularTreatment as $key => $value) {

        $dataValue = explode(',', $value['value']);

        foreach ($dataValue as  $valueData) {

            if($valueData == $treatment->value)
            {
                // unset($treatment->value);
                // unset($treatment->label);
            }

        } 
    }
    $treatment_list[$treatment->value] = $treatment->label;
}

?>

<div class="row" id="userdetails">
    <div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Popular Hospital</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
                <div class="col-sm-12">
                    <?php echo  $form->field($model, 'hospital')->widget(Select2::classname(), 
                        [
                        'data' => $hospital_list,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select a hospital ...', 'multiple' => true],
                       
                        ])->label(false); ?>
                </div>



                <div class="form-group clearfix col-sm-12">
                    <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

<div class="row" id="userdetails">
    <div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Popular Speciality</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form', 'options' => ['enctype'=> 'multipart/form-data']],['name' =>'SpecialityForm']); ?>
                <div class="col-sm-12">
                    <?php echo  $form->field($model, 'speciality')->widget(Select2::classname(), 
                        [
                        'data' => $speciality_list,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select a speciality ...', 'multiple' => true],
                       
                        ])->label(false); ?>
                </div>



                <div class="form-group clearfix col-sm-12">
                    <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>


<div class="row" id="userdetails">
    <div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Popular Treatment</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form', 'options' => ['enctype'=> 'multipart/form-data']],['name' =>'TreatmentForm']); ?>
                <div class="col-sm-12">
                    <?php echo  $form->field($model, 'treatment')->widget(Select2::classname(), 
                        [
                        'data' => $treatment_list,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select a treatments ...', 'multiple' => true],
                       
                        ])->label(false); ?>
                </div>



                <div class="form-group clearfix col-sm-12">
                    <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>

