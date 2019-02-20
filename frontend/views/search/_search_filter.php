<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\models\UserProfile;
use common\models\MetaValues;
use common\components\DrsPanel;
$specialities = MetaValues::find()->orderBy('id asc')
            ->where(['key'=>5])->all();
$treatments = MetaValues::find()->orderBy('id asc')
            ->where(['key'=>9])->all();
$this->title = Yii::t('frontend','DrsPanel::Doctor List');

$genderlist = DrsPanel::getGenderList();
$ratinglist = DrsPanel::getRatingArray();
$model = new UserProfile();
$js = "function myFunction(inputid,ulid) {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById(inputid);
    filter = input.value.toUpperCase();
    ul = document.getElementById(ulid);
    li = ul.getElementsByTagName('li');
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName('label')[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = '';
        } else {
            li[i].style.display = 'none';
        }
    }
}";
$this->registerJs($js,\yii\web\VIEW::POS_END);

// echo '<pre>';
// print_r($selected_filter);

?>
  <div class="search-boxicon search-part appointment_part patient_profile">
 
  <button data-toggle="modal" data-target="#myfilter" class="filter-btn filter_btn_left"><i class="fa fa-filter"></i> Filter</button>
  <div id="myfilter" class="modal fade model_opacity filter-popup" role="dialog">
    <div class="modal-dialog"> 
      <div class="modal-content">
       <?php $form = ActiveForm::begin(['action' => ['search/search-filter'],'options' => ['method' => 'post']]); ?>
        <div class="modal-header">
          <h3>Filter</h3>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body clearfix">
            <div class="filter-part">
              <div class="resp-tabs-container hor_1">
                <div id="ChildVerticalTab_1">
                  <ul class="resp-tabs-list ver_1">
                    <li>Speciality</li>
                    <li>Treatments</li>
                    <li>Gender</li>
                    <li>Rating</li>
                  </ul>
                  <div class="resp-tabs-container ver_1">
                 
                    <div>
                      <div class="search-part">
                        <div class="row">
                          <div class="col-sm-12">
                            <div class="search-inputbar">
                              <input type="text" class="form-control" placeholder="Search Speciality." onkeyup="myFunction('speciality_input','filter_speciality')" id="speciality_input"  >
                              <div class="search-icon"> <i class="fa fa-search"></i> </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <ul class="filter-menu" id="filter_speciality">
                      <?php 
                      if(!empty($specialities))
                      {
                          foreach ($specialities as $h_key=>$speciality) { 
                           
                            ?>
                            <li>
                              <div class="form-check form-check-inline">
                                <input type="checkbox" class="form-check-input" value="<?php echo $speciality->value ?>" id="materialInline_<?php echo $h_key?>" name="UserProfile[speciality]">
                                <label class="form-check-label" for="materialInline_<?php echo $h_key?>"><?php echo $speciality->label ?></label>
                            </div>
                            </li>
                            <?php
                          }
                      }
                      ?>
                      </ul>
                    </div>
                    <div>
                    <div class="search-part">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="search-inputbar">
                            <input type="text" class="form-control" placeholder="Search Treatments." onkeyup="myFunction('treatment_input','filter_treatment')" id="treatment_input">
                            <div class="search-icon"> <i class="fa fa-search"></i> </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <ul class="filter-menu" id="filter_treatment">
                      <?php 
                       if(!empty($treatments))
                       {
                        foreach ($treatments as $h_key=>$treatment) { 
                          ?>
                          <li>
                            <div class="form-check form-check-inline">
                              <input type="checkbox" class="form-check-input" value="<?php echo $treatment->value ?>" id="treatment_<?php echo $h_key?>" name="UserProfile[treatment][]">
                              <label class="form-check-label" for="treatment_<?php echo $h_key?>"><?php echo $treatment->label ?></label>
                            </div>
                          </li>
                          <?php
                        }
                      }
                      ?>
                    </ul>
                    </div>
                    <div>
                      <ul class="filter-menu no-scroll">
                     <?php 
                      if(!empty($genderlist))
                      {
                          foreach ($genderlist as $h_key=>$gender) { 
                            ?>
                            <li>
                              <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="<?php echo $h_key ?>" id="gender_<?php echo $h_key?>" name="UserProfile[gender]">
                                <label class="form-check-label" for="gender_<?php echo $h_key?>"><?php echo $gender ?></label>
                              </div>
                            </li>
                            <?php
                          }
                      }
                      ?>
                    </ul>
                    </div>
                    <div>
                      <ul class="filter-menu no-scroll">
                      <?php 
                      if(!empty($ratinglist))
                      {
                          foreach ($ratinglist as $h_key=>$rating) { 
                            ?>
                            <li>
                              <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="<?php echo $h_key ?>" id="rating_<?php echo $h_key?>" name="UserProfile[rating]">
                                <label class="form-check-label" for="rating_<?php echo $h_key?>"><?php echo $rating ?></label>
                              </div>
                            </li>
                            <?php
                          }
                      }
                      ?>
                    </ul>
                    </div>
                  </div>

                </div>
              </div>
            </div>
        </div> 
        <div class="modal-footer">
            <div class="pull-left">
            <button type="reset" class="btn filter-btn">Reset</button>
          </div>
          <div class="pull-right">
            <button type="submit" class="btn filter-btn">Apply</button>
          </div>
        </div>
          <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
  </div>


<div id="form_subcat_div" class="col-sm-12">
    <div class="seprator_box">
        
    </div>
</div>
