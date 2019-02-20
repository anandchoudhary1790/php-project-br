<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

\frontend\assets\FrontendAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?php echo Yii::$app->language ?>">
    <head>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">
     <meta charset="<?php echo Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="msvalidate.01" content="56DD5902E146F62A33E3208B63579408" />
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="shortcut icon" type="image/x-icon" href="<?php echo Yii::getAlias('@frontendUrl'); ?>/favicon.ico">
        <title><?php echo Html::encode($this->title) ?></title>

        <?php $this->head() ?>
        <?php echo Html::csrfMetaTags() ?>
    </head>
    <body  <?php if(Yii::$app->user->id){
                            $login_user_data=Yii::$app->user->identity;
                            if(isset($login_user_data->groupid) && $login_user_data->groupid=='3'){ ?> class="patient_body" <?php  } }?> <?php if(Yii::$app->user->id){
                            $login_user_data=Yii::$app->user->identity;
                            if(isset($login_user_data->groupid) && $login_user_data->groupid=='4'){ ?> class="doctor_body" <?php  } }?>>
    <!-- class="scrollbar" id="style-2" -->
    <?php $this->beginBody() ?>
    <div id="main-js-preloader" style="display: none;"></div>
        <?= $this->render('header.php') ?>
        <?php echo $content ?>
        <?= $this->render('footer.php') ?>
        <?= $this->render('modal.php') ?>
        <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>


