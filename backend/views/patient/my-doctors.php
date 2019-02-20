<?php

use common\grid\EnumColumn;
use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'My Doctor');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="box">
    <div class="box-body">
        <div class="user-index">

<div class="edu-list">
<table class="table">
	<thead>
	<th>#</th>
	<th>Doctro Name</th>
	<th>Doctor Fees</th>
	<th>Doctor Address</th>
	</thead>
	<tbody>
		<?php if(count($doctors)>0){ 
		foreach ($doctors as $key => $item) { ?>
		<tr>
		<td><?php echo $key+1;?></td>
		<td><?php echo $item->doctor_name; ?></td>
		<td><?php echo $item->doctor_address; ?></td>
		<td><?php echo $item->doctor_fees; ?></td>
		</tr>
		<?php } } else{ ?>

		<?php }?>
	</tbody>

</table>
</div>
</div>
</div>
</div>