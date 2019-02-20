 <?php
 use common\components\DrsPanel;
 $base_url= Yii::getAlias('@frontendUrl'); 
 $aboutusData = DrsPanel::getAboutUs($user_id);
 if(!empty($aboutusData))
 { ?>
 	<div class="_aboutus">
	 	<h4> About Marvel Multispeciality Hospital </h4>
	 	<p><?php echo $aboutusData['description'] ?></p>
	 	<p> <strong> Vision </strong> </p>
	 	<p><?php echo $aboutusData['vision'] ?></p>
	 	<p> <strong> Mission </strong>
	 	<p><?php echo $aboutusData['mission'] ?></p>
	 	<p> <strong> Timing </strong>
	 	<p><?php echo $aboutusData['timing'] ?></p>
 	</div>
<?php 
} 
?>