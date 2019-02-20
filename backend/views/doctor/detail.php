<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use backend\models\AddScheduleForm;
use common\components\DrsPanel;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $roles yii\rbac\Role[] */

$this->title = Yii::t('backend', '{modelClass}: ', ['modelClass' => 'Doctor']) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Doctors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=>$model->email];

$degree_list = array();$speciality_list=$treatment_list=$services_list=array();
foreach ($degrees as $d_key=>$degree) {
    $degree_list[$degree->value] = $degree->label;
}
$degree_list['Other']='Other';


foreach ($specialities as $speciality) {
    $speciality_list[$speciality->value] = $speciality->value;
}

foreach ($treatment as $obj) {
    $treatment_list[$obj->value] = $obj->label;
}

foreach ($services as $obj) {
    $services_list[$obj->value] = $obj->label;
}

if($userProfile->other_degree){
    $this->registerJs(" $('#other_degree').show();",\yii\web\VIEW::POS_END); 
}else{
    $this->registerJs(" $('#other_degree').hide();",\yii\web\VIEW::POS_END); 
}

if($userProfile->treatment){
    $this->registerJs(" $('#treatment_list_update').show();",\yii\web\VIEW::POS_END); 
}
$this->registerJs("
    $('#degree_Other').on('click', function () {
        if($(this).prop('checked') == true){
            $('#other_degree').show();
        }else{
            $('#other_degree').hide();
        } 
    });
    $('#specialities').on('change', function () {
     $.ajax({
      method: 'POST',
      url: 'ajax-treatment-list',
      data: { id: $('#specialities').val(),'user_id':$model->id}
  })
  .done(function( msg ) { 
    if(msg){
        $('#treatment_list_update').show();
        $('#treatment_list_update').html('');
        $('#treatment_list_update').html(msg);
    }
});
});

",\yii\web\VIEW::POS_END); 


?>

<?= $this->render('_update_top', ['userProfile' => $userProfile]); ?>

<div class="row" id="userdetails">
    <div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">Personal Information</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
                <div class="col-sm-12">
                    <?php echo $form->field($userProfile, 'name') ?>
                </div>
                <div class="col-sm-12">
                    <?php echo $form->field($model, 'email') ?>
                </div>

                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-3 hide">
                            <?php //echo $form->field($model, 'countrycode')->dropDownList(\common\components\DrsPanel::getCountryCode(91)) ?>
                        </div>
                        <div class="col-sm-12">
                            <?php echo $form->field($model, 'phone') ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= $form->field($userProfile, 'dob')->textInput()->widget(
                            DatePicker::className(), [
                            'convertFormat' => true,
                            'options' => ['placeholder' => 'Date of Birth*'],
                            'layout'=>'{input}{picker}',
                            'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-MM-dd',
                            'endDate' => date('Y-m-d'),
                            'todayHighlight' => true
                            ],]); ?>
                        </div>
                    </div>


                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-1"><label>Gender</label></div>

                                        <?php
                                        echo $form->field($userProfile, 'gender', ['options' => ['class' =>
                                            'col-sm-11']])->radioList(['1' => 'Male', '2' => 'Female','3' => 'Other'], [
                                            'item' => function ($index, $label, $name, $checked, $value) {

                                                $return = '<span>';
                                                $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
                                                $return .= '<label for="gender_' . $label . '" >' . ucwords($label) . '</label>';
                                                $return .= '</span>';

                                                return $return;
                                            }
                                            ])->label(false)
                                            ?>
                                        </div>

                                    </div>
                                </div>
                        <?php /*
                        <div class="col-sm-6">
                            <?php echo $form->field($userProfile, 'blood_group')->dropDownList(\common\components\DrsPanel::getBloodGroups()) ?>
                        </div> */ ?>
                    </div>
                </div>

                <div  class="col-sm-12">
                    <?php echo $form->field($userProfile, 'description')->widget(
                        \yii\imperavi\Widget::className(),
                        [
                        'plugins' => ['fullscreen', 'fontcolor', 'video'],
                        'options'=>[
                        'minHeight'=>250,
                        'maxHeight'=>250,
                        'buttonSource'=>true,
                        'imageUpload'=>Yii::$app->urlManager->createUrl(['/file-storage/upload-imperavi'])
                        ]
                        ]
                        ) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($userProfile, 'avatar')->fileInput([
                            'options' => ['accept' => 'image/*'],
                'maxFileSize' => 5000000, // 5 MiB

                ]);   ?>
                <?php if($userProfile->avatar){  ?>
                <div class="edit-image" style="margin-left: 200px;margin-top: -70px;">
                    <img  src="<?php echo Yii::getAlias('@storageUrl/source/doctors/').$userProfile->avatar; ?>" width="75" height="75"/>
                </div>
                <?php } ?>
            </div>

            <div class="form-group clearfix col-sm-12">
                <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="nav-tabs-custom">
        <div class="panel-heading">
            <h3 class="panel-title">Professional Information</h3>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>
            <div  class="col-sm-12">
                <div class="seprator_box">
                    <h4>Degree:</h4>
                    <?php $userProfile->degree = explode(',',$userProfile->degree); 
                    ?>
                    <?php echo  $form->field($userProfile, 'degree')->widget(Select2::classname(), 
                        [
                        'data' => $degree_list,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select a degree ...', 'multiple' => true],
                        'pluginOptions' => [
                        'allowClear' => true
                        ],
                        ])->label(false); ?>

                    </div>
                    <div id="other_degree" class="chkbox_blk col-sm-12">
                      <input id="other_degree_text" class="form-control" value="<?php echo $userProfile->other_degree; ?>" name="other_degree" placeholder="other degree"/>  
                  </div>
              </div>

              <div  class="col-sm-12">
                <div class="seprator_box">
                    <h4>Speciality:</h4>
                    <?= $form->field($userProfile, 'speciality')->dropDownList($speciality_list,['id'=>'specialities','prompt'=>'Select Speciality'])->label(false);  ?>
                    
                    <div id="treatment_list_update">
                        <?php echo $this->render('ajax-treatment-list',['form'=>$form,'userProfile'=>$userProfile,'treatment_list'=>$treatment_list]) ?>
                    </div>
                </div>
            </div>  
            <div  class="col-sm-12">
                <div class="seprator_box">
                    <h4>Services:</h4>
                    <?= $form->field($userProfile, 'services')->dropDownList($services_list
                        ,['id'=>'services','prompt'=>'Select Services
                    '])->label(false);  ?>
                </div>
            </div>

            <div  class="col-sm-12">
                <?php echo $form->field($userProfile, 'experience') ?>
            </div>


            <div class="form-group clearfix col-sm-12">
                <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

</div>





<div class="modal fade" id="editrating" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalContact">Update Rating</h4>
            </div>
            <div class="modal-body">
                <?= $this->render('_edit_rating', [
                    'userProfile' => $userProfile
                    ]) ?>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div>



    <div class="modal fade" id="editfeecomm" tabindex="-1" role="dialog" aria-labelledby="feecomm" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalContact">Fees Percentage</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('_edit_fee_percent', [
                        'userProfile' => $userProfile
                        ]) ?>
                    </div>
                </div><!-- /.modal-content -->
            </div>
        </div>

        <div class="modal fade" id="updateaddress" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
        </div>

        <div class="modal fade" id="editlivestatus" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalContact">Update Profile/Live Status</h4>
                    </div>
                    <div class="modal-body">
                        <?= $this->render('_edit_live_status', [
                            'userProfile' => $userProfile,'user'=>$model
                            ]) ?>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div> 