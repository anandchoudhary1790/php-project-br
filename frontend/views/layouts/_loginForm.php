<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Groups;
use frontend\modules\user\models\LoginForm;
$model = new LoginForm();
?>

<div class="login-section ">
    <div class="modal fade model_opacity" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="myModalLabel">Login <span>Enter your credentials to login.</span></h3>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <?php $form = ActiveForm::begin(['id'=>'login-form','action'=>'','method'=>"post",'class'=>'form-horizontal',]); ?>

                    <?php $groups = Groups::allgroups(); ?>

                    <div  class="col-md-12">
                        <?php
                        echo $form->field($model, 'groupid',
                            ['options' => ['class' => 'col-sm-12 reg_on_change reg_group_channge']])->radioList($groups,
                            ['item' => function ($index, $label, $name, $checked, $value) {
                                $return = '<span class="span_col_4">';
                                $return .= Html::radio($name, $checked, ['value' => $value,
                                    'autocomplete' => 'off', 'id' => 'group_' . $label,'class'=>'groupid_change']);
                                $return .= '<label for="group_' . $label . '" >' . ucwords($label) . '</label>';
                                $return .= '</span>';
                                return $return;
                            }])->label(false);

                        ?>
                    </div>

                    <?php

                    /*echo $form->field($model, 'groupid')->dropDownList($groups,['prompt' => 'Login with','id'=>'login_user_type'])->label(false);*/ ?>
                    <?php echo $form->field($model, 'identity')->textInput(["pattern"=>"^\d{10}$",'class'=>'form-control ten_number','placeholder'=>'Enter Phone Number', 'maxlength'=>"10"])->label(false); ?>

                    <?php echo Html::Button(Yii::t('frontend', 'Next'), ['id'=>"login-submit-btn",'class' => 'login-sumbit', 'name' => 'login-button']) ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>