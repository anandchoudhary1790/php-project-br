<?php
$customer=\common\components\DrsPanel::getMetaData('customer_care');
$this->title = Yii::t('frontend', 'DrsPanel :: Customer Care');

$base_url= Yii::getAlias('@frontendUrl'); ?>
<div class="inner-banner"> </div>
<section class="mid-content-part customer_care">
    <div class="signup-part customer_care">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="allover-reminderpart">
                                <div class="success-reminderpart">
                                    <div class="reminderic-success1"> <img src="<?php echo  $base_url?>/images/mail-icon.png"> </div>
                                    <div class="reminder-ctpart">
                                        <h4> <?php echo $customer[0]['value']?> </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="allover-reminderpart">
                                <div class="success-reminderpart">
                                    <div class="reminderic-success1"> <img src="<?php echo  $base_url?>/images/mail-call.png"> </div>
                                    <div class="reminder-ctpart">
                                        <h4>91+  <?php echo $customer[1]['value']?> </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>