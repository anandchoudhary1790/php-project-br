<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Category;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\SignupForm */

$this->title = Yii::t('frontend', 'Contact Info');
$category = new Category();
$categories = $category->mainCategoryType();
unset($categories[3]);
?>



  <section class="inner_banner" style="background:url(<?php echo Yii::getAlias('@frontendUrl'); ?>/images/availability_banner.jpg) no-repeat top center;  background-size:cover;">
      <div class="container text-center">
        <h1>Vendor Contact Info</h1>
          
      </div>
  </section>

    <section class="posts_body_area">
          <div class="container">
           <?php $form = ActiveForm::begin(['id' => 'form-vendor-contact','options' => ['enctype' => 'multipart/form-data']]); ?>
          <div class="row">
              <div class="col-lg-8 col-lg-offset-2">
              <div class="regestration_section">
             


              <!--<div class="col-sm-12">


                        <div class="form-group">
                        <label class="col-sm-4 control-label logo_uplodes">
                            <div class="main-img-preview">
                              <img class="thumbnail img-preview" src="http://farm4.static.flickr.com/3316/3546531954_eef60a3d37.jpg" title="Preview Logo">
                            </div>
                            <div class="input-group">
                              <input id="fakeUploadLogo" class="form-control fake-shadow" placeholder="Choose File" disabled="disabled">
                              <div class="input-group-btn">
                                <div class="fileUpload btn btn-danger fake-shadow">
                                  <span><i class="glyphicon glyphicon-upload"></i> Upload Logo</span>
                                  <input id="logo-id" name="logo" type="file" class="attachment_upload">
                                </div>
                              </div>
                            </div>
                          </label>
                          </div>
              </div>-->
                  <div class="row">
                    <div class="col-sm-12">
                    <div class="vendor_heading">
                    <h4>Contact Information</h4>
                    </div>
                    </div>

                    <div class="col-sm-6">
                      <?php echo $form->field($userProfile, 'firstname')->textInput(['placeholder' => "Firstname Name"])->label(false);  ?>
                    </div>

                    <div class="col-sm-6">
                      <?php echo $form->field($userProfile, 'lastname')->textInput(['placeholder' => "Lastname Name"])->label(false);  ?>
                    </div>


                    <div class="col-sm-6">
                      <?php echo $form->field($model, 'email')->textInput(['placeholder' => "Email"])->label(false); ?>
                    </div>

                    <div class="col-sm-6">
                      <?php echo $form->field($model, 'mobile_number')->textInput(['placeholder' => "Mobile Number"])->label(false); ?>
                    </div>

                    <div class="col-sm-6">
                      <?php echo $form->field($userProfile, 'telephone_number')->textInput(['placeholder' => "Telephone Number"])->label(false); ?>
                    </div>


                  </div>

                  <div class="row">
                      <div class="col-sm-12">
                      <div class="vendor_heading">
                      <h4>Business Locations</h4>
                      </div>
                      </div>

                        <div class="col-sm-6">
                       <?= $form->field($userProfile, 'country_id')->dropDownList(['India'=>'India','USA'=>'USA'],['prompt' => ' -- Select Country --'])->label(false); ?>
                        </div>

                      <div class="col-sm-6">
                      <?= $form->field($userProfile, 'city_id')->textInput(['placeholder' => "City Name"])->label(false); ?>
                      </div>

                      <div class="col-sm-6">
                      <?= $form->field($userProfile, 'address')->textArea(['rows'=>5,'placeholder' => "address"])->label(false); ?>
                      </div>

                      <div class="col-sm-6">
                      <div class="form-group">
                       <?= $form->field($userProfile, 'zipcode')->textInput(['placeholder' => "Zipcode"])->label(false); ?>
                      </div>
                      </div>

                  </div>


                  <div class="row">
                      <div class="col-sm-12">
                      <div class="vendor_heading">
                      <h4>Business Category</h4>
                      </div>
                      </div>

                      <div class="col-sm-12">
                          <div class="row">
                          <?php if($userProfile['cat_id']>0){ ?>
                            <div class="col-sm-4"><p><?php echo $category->mainCategoryType($userProfile['cat_id']); ?></p></div>
                          <?php }else{
                               echo $form->field($userProfile, 'cat_id',['options'=>['class'=>
                                        'col-sm-11 col-xs-10']])->radioList($categories, [
                                        'item' => function ($index, $label, $name, $checked, $value) {

                                            $return = '<span>';
                                            $return .= Html::radio($name, $checked, ['value' => $value, 'autocomplete' => 'off', 'id' => 'gender_' . $label]);
                                            $return .= '<label for="gender_' . $label . '" >' . Yii::t('db',ucwords($label)) . '</label>';
                                            $return .= '</span>';

                                            return $return;
                                        }
                                    ])->label(false); } ?>

   
                          </div>

                      </div>
                </div>


              <?php if($planImage){ ?>
                <div class="row">
                  <div class="col-sm-12">
                      <div class="photo_galleryarea">
                            <h4>Photo Gallery</h4>

                                  <div class="photo_innerarea">
                                      <div class="photo_innertoparea">
                                      <p><?php echo $planImage; ?> photos are required (venues, attires, menus, etc.)</p>
                                      <p><strong>Showcase your work by adding high-quality photos of your business and the wedding services or products you offer.</strong> </p>
                                      </div>

                                      <div class="file_btn">
                                     <?= $form->field($vendorBusinessImages, 'business_image_name[]')->fileInput(['multiple' => true])->label(false); ?>
                                      <span>Select Images</span>

                                      </div>
                                  
                              </div>

                      </div>

                      </div>

                </div>
                <?php } ?>
                
                
                </div>


    </div>

    <div class="row">

                <div class="col-lg-12">
                          
                                
                                 <?= $form->field($userProfile, 'description')->widget(
                                        \yii\imperavi\Widget::className(),
                                        [
                                           'plugins' => ['fullscreen', 'fontcolor', 'video'],
                                            'options'=>[
                                                'minHeight'=>400,
                                                'maxHeight'=>400,
                                                'buttonSource'=>true,
                                              
                                            ]
                                        ]
                                    )->label(false);  ?>
                                
                            </div>
                </div>

                <div class="row">
                 <div class="col-sm-12">
                 <div class="col-sm-4">
                 </div>
                 <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                        
                  </div>
                    <?php ActiveForm::end(); ?>
                </div>
    </div>
    </section>
