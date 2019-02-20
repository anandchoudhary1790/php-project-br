<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title = Yii::t('db', $model->title);
?>

<!--Banner Block start-->
<section class="banner_blk_1 media_none_blk">
    <div class="container">
        <div class="banner_content wow fadeInDown">
            <h1><?php echo Yii::t('db', $model->title); ?></h1>
        </div>
    </div>
</section>

<section class="middle_blk">
    <div class="container">
        <div class="page_row">
            <?php echo $model->body ?>
        </div>
    </div>
</section>