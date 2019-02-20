 <?php 
 use yii\helpers\Html;
 use yii\widgets\ActiveForm;
 use yii\helpers\ArrayHelper;
 use kartik\date\DatePicker;

 $baseUrl= Yii::getAlias('@frontendUrl');


?>

<div class="edu-list table-responsive">
<table class="table">
	<thead>
	<th>#</th>
	<th>College/School Name</th>
	<th>Degree/Class Name</th>
	<th>Start</th>
	<th>End</th>
	<th>Action</th>
	</thead>
	<tbody>
		<?php if(count($edu_list)>0){ 
		foreach ($edu_list as $key => $item) { ?>
		<tr>
		<td><?php echo $key+1;?></td>
		<td id="collage-name-<?php echo $item['id']?>"><?php echo $item->collage_name; ?></td>
		<td><?php echo $item->education; ?></td>
	<!-- 	<td><?php //echo date('Y',$item->start); ?></td>
		<td><?php// echo ($item['end'] > date("Y"))?'Till Now':date('Y',$item['end']); ?></td> -->

       <td><?php echo date('Y',$item['start']); ?></td>
        <td><?php echo (date('Y',$item['end']) > date("Y"))?'Till Now':date('Y',$item['end']); ?></td>
		<td>
		<a href="javascript:void(0)" ><i class="fa fa-pencil call-edit-modal" aria-hidden="true" data-id="<?php echo $item['id']?>"></i></a>
		<a href="javascript:void(0)"><i class="fa fa-trash-o call-delete-modal" data-id="<?php echo $item['id']?>" aria-hidden="true"></i></a>
		</td>
		</tr>
		<?php } } else{ ?>

		<?php }?>
	</tbody>

</table>
</div>

<div class="register-section">
  <div class="modal fade model_opacity" id="attenderEdit-modal" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title" id="myModalContact">Update Education </h4>
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

       </div>
       <div class="modal-body" id="edit-modal-form">

       </div>
     </div><!-- /.modal-content -->
   </div>
 </div>
</div>