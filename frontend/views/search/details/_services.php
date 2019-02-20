<?php
$base_url= Yii::getAlias('@frontendUrl'); 
?>
<ul class="mobile-text">
	<?php 
	if(!empty($servicesList))
	{

		$Servicedata = explode(',', $servicesList);
		foreach ($Servicedata as $list) { ?>
		<li><i><img src="<?php echo $base_url?>/images/check-icon.png"></i> <?php echo $list?></li>
		<?php } 
	}?>
</ul>
