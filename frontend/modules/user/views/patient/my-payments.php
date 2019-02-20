<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\date\DatePicker;
use common\components\DrsPanel;
use common\models\PatientMemberFiles;
$this->title = Yii::t('frontend','DrsPanel :: My Payments');

?>
<div class="inner-banner"> </div>
<section class="mid-content-part">
    <div class="signup-part">
        <div class="container">
            <div class="row">
                <div class="col-md-9">
                <div class="today-appoimentpart">
                <h3 class="text-left mb-3"> My Payments </h3>
                </div>
                No any payments history
                </div>
                <?php echo $this->render('@frontend/views/layouts/rightside'); ?>
            </div>
        </div>
    </div>
</section>
