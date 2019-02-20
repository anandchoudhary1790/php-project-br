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

$this->title = Yii::t('backend', '{modelClass}: ', ['modelClass' => 'Hospital']) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'Hospitals'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label'=>$model->email];

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
                        <div class="col-sm-3">
                            <?php echo $form->field($model, 'countrycode')->dropDownList(\common\components\DrsPanel::getCountryCode()) ?>
                        </div>
                        <div class="col-sm-9">
                            <?php echo $form->field($model, 'phone') ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <?= $form->field($userProfile, 'dob')->textInput()->widget(
                            DatePicker::className(), [
                            'convertFormat' => true,
                            'options' => ['placeholder' => 'Establishment Date*'],
                            'layout'=>'{input}{picker}',
                            'pluginOptions' => [
                            'autoclose'=>true,
                            'format' => 'yyyy-MM-dd',
                            'endDate' => date('Y-m-d'),
                            'todayHighlight' => true
                            ],])->label('Establishment Date'); ?>
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
                    <img  src="<?php echo Yii::getAlias('@storageUrl/source/hospitals/').$userProfile->avatar; ?>" width="75" height="75"/>
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
            <?php echo $this->render('multi-selection',['userProfile' => $userProfile, 'degrees' => $degrees,'specialities' => $specialities,'services' => $services,'treatments' => $treatments])?>
        </div>
    </div>
</div>
<div class="col-md-6">
        <div class="nav-tabs-custom">
            <div class="panel-heading">
                <h3 class="panel-title">My About Us</h3>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin(['id' => 'profile-form','options' => ['enctype'=> 'multipart/form-data']]); ?>
                <div class="col-sm-12">
                    <?php echo $form->field($userAboutus, 'description')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            ) ?>
                </div>
                <div class="col-sm-12">
                    <?php echo $form->field($userAboutus, 'vision')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            ) ?>
                </div>
                <div class="col-sm-12">
                      <?php echo $form->field($userAboutus, 'mission')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            ) ?>
                </div>
                <div class="col-sm-12">
                     <?php echo $form->field($userAboutus, 'timing')->widget(
                            \yii\imperavi\Widget::className(),
                            [
                            'plugins' => ['filemanager'],
                            'options'=>[
                            'minHeight'=>100,
                            'maxHeight'=>100,
                          
                            ]
                            ]
                            ) ?>
                </div>
               


                

            <div class="form-group clearfix col-sm-12">
                <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
</div>
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
                    'userProfile' => $userProfile
                    ]) ?>
                </div>
            </div><!-- /.modal-content -->
        </div>
    </div>
    <div class="modal fade" id="addaddress" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalContact">Add Address To Doctor's Profile</h4>
                </div>
                <div class="modal-body">
                    <?php $address=new \common\models\UserAddress();
                    $address->user_id=$userProfile->user_id;
                    $address->name=$userProfile->name;
                    $address->is_register=1;
                    $address->type='Hospital';
                    ?>
                    <?= $this->render('_addaddress', [
                        'model' => $address
                        ]) ?>
                    </div>
                </div><!-- /.modal-content -->
            </div>
        </div>
        <div class="modal fade" id="updateaddress" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
        </div>