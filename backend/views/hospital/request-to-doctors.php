<?php
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use common\grid\EnumColumn;
use common\models\UserRequest;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use common\components\DrsPanel;
use backend\models\RequestForm;




/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$model = new RequestForm();

$this->title = Yii::t('backend', 'Doctors List');
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="box">
  <div class="box-body">
    <div class="user-index">
      <?php $doctorList = DrsPanel::hospitalDoctorList($id);
      // pr($doctorList);die;
      ?>
      <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>
      <div  class="col-sm-12">
        <div class="seprator_box">
          <h4>Doctors:</h4>

          <?php echo  $form->field($model, 'id')->widget(Select2::classname(), 
            [
            'data' => $doctorList,
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select a doctor ...', 'multiple' => true],
            'pluginOptions' => [
            'allowClear' => true
            ],
            ])->label(false); ?>
          </div>
        </div>
        <div class="form-group clearfix col-sm-12">
          <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        
      </div>

    </div>
  </div>

  <div class="box">
    <div class="box-body">
      <div class="user-index">

        <?php echo GridView::widget([
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'options' => [
          'class' => 'grid-view table-responsive'
          ],
          'columns' => [
          'id',
          [
          'attribute'=>'name',
          'value'=>function($data){
            return DrsPanel::getUserName($data->id);
          }
          ],
          [
          'class' => EnumColumn::className(),
          'attribute' => 'status',
          'label'=>'Requested Status',
          'enum' => UserRequest::statusValue(),
          'filter' => UserRequest::statusValue(),
          'value'=>function($data){
            return UserRequest::statusValue($data->status);
          },
          ],
          'email:email',
          'phone',
          

          [
          'content' => function ($model, $key, $index, $column) {
            $link = Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['doctor/detail', 'id' => $model->id], ['aria-label'=>'View', 'title'=>'View']);
                            $link2 = '';//Html::a('<span class="glyphicon glyphicon-user"></span>', ['requested-hospital', 'id' => $model->id], ['aria-label'=>'Requested Hospital', 'title'=>'Requested Hospital']);
                            return $link.$link2;
                          }
                          ],
                          ],
                          ]); ?>

                        </div>
                      </div>
                    </div>

