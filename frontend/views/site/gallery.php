<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = 'Gallery';
$this->params['breadcrumbs'][] = $this->title;
$js="
	$('.fancybox').fancybox();
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="site-contact">
    <h1><?php echo Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
			<a class="fancybox" rel="gallery1" href="http://farm2.staticflickr.com/1669/23976340262_a5ca3859f6_b.jpg" title="Twilight Memories (doraartem)">
			<img src="http://farm2.staticflickr.com/1669/23976340262_a5ca3859f6_m.jpg" alt="" />
			</a>
			<a class="fancybox" rel="gallery1" href="http://farm2.staticflickr.com/1459/23610702803_83655c7c56_b.jpg" title="Electrical Power Lines and Pylons disappear over t.. (pautliubomir)">
			<img src="http://farm2.staticflickr.com/1459/23610702803_83655c7c56_m.jpg" alt="" />
			</a>
			<a class="fancybox" rel="gallery1" href="http://farm2.staticflickr.com/1617/24108587812_6c9825d0da_b.jpg" title="Morning Godafoss (Brads5)">
			<img src="http://farm2.staticflickr.com/1617/24108587812_6c9825d0da_m.jpg" alt="" />
			</a>
			<a class="fancybox" rel="gallery1" href="http://farm4.staticflickr.com/3691/10185053775_701272da37_b.jpg" title="Vertical - Special Edition! (cedarsphoto)">
			<img src="http://farm4.staticflickr.com/3691/10185053775_701272da37_m.jpg" alt="" />
			</a>
        </div>
    </div>
</div>


<style type="text/css">
		.fancybox-custom .fancybox-skin {
			box-shadow: 0 0 50px #222;
		}
</style>