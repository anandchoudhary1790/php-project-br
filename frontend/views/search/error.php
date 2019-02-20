<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
$baseUrl= Yii::getAlias('@frontendUrl');
?>

                <div class="col-md-12">
                    <h1 class="display-6 lg_pb_30 text-center">Not Found (#404)
</h1>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                             <h2 class="display-6  text-center"><?php echo nl2br(Html::encode('Page Not Found')) ?> </h2>
                         </div>
                     </div>
                     <div class="col-md-12 text-center">
                        <p>
                            The above error occurred while the Web server was processing your request.
                        </p>
                        <p>
                            Please contact us if you think this is a server error. Thank you.
                        </p>
                    </div>
                    

                    <div class="clearfix"></div>
                    <div class="col-md-3 text-center offset-4">
                        <div class="button_bottom_c text-right"> <a href="<?php echo $baseUrl?>" class="view_pro_appoint new_bookbtn"> Back to home </a> </div>
                    </div>
                </div>
            </div>
       