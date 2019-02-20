<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$types=array('User'=>'User','Admin'=>'Admin');
?>
<?php $form = ActiveForm::begin([
    'id' => 'update-rating',
    'fieldConfig'=>['template' =>"{label}{input}\n {error}"],
    'options' => [
        'enctype' => 'multipart/form-data',
    ],
]); ?>
    <div class="col-sm-12">
        <div class="form-group clearfix">
            <label class="control-label">Show Rating</label>
            <select name="AdminRating[type]" class="form-control" id="admin_rating_type">
                <?php foreach($types as $type){ ?>
                    <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                <?php }?>
            </select>
        </div>
    </div>

    <div class="col-sm-12" id="admin_rating_number" style="display: none;">
        <div class="form-group clearfix field-adminrating-rating">
            <label class="control-label">Rating</label>
            <input id="adminrating-rating" class="form-control" type="text" name="AdminRating[rating]"/>
            <p class="help-block help-block-error"></p>
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-md-12 text-right">
            <input type="submit" class="show-profile" value="<?php echo Yii::t('db','Update'); ?>">
        </div>
    </div>
<?php ActiveForm::end(); ?>