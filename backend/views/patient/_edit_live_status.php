<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$status=\common\components\DrsPanel::getAllProfileStatus();
?>
<?php $form = ActiveForm::begin([
    'id' => 'update-status',
    'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]); ?>

    <div class="col-sm-12">

        <div class="form-group clearfix">

            <div class="col-sm-8">
                <label class="control-label">Status</label>
                <select name="LiveStatus[status]" class="form-control">
                    <?php foreach($status as $value=>$status){ ?>
                        <option value="<?php echo $value; ?>"><?php echo $status; ?></option>
                    <?php }?>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-md-12 text-right">
            <input type="submit" class="show-profile" value="<?php echo Yii::t('db','Update'); ?>">
        </div>
    </div>
<?php ActiveForm::end(); ?>