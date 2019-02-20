<?php 
$eduForm="'education-form'";
$js="
      $('.edit_edu_form').on('click', function () {
       $.ajax({
          url: $eduForm,
          data: {user_id:$(this).attr('doctor-id'),edu_id:$(this).attr('data-id'),}
    })
      .done(function( msg ) { 

        $('#upseart-body').html('');
        $('#upseart-body').html(msg);
        $('#upseart_edu_modal').modal({backdrop: 'static',keyboard: false})

      });
    });

";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="edu-list">
<table class="table">
	<thead>
	<th>#</th>
	<th>Collage/School Name</th>
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
		<td><?php echo $item->collage_name; ?></td>
		<td><?php echo $item->education; ?></td>
		<td><?php echo $item->start; ?></td>
		<td><?php echo $item->end; ?></td>
		<td>
		<a href="javascript:void(0)" class="edit_edu_form" doctor-id="<?php echo $item->user_id; ?>" data-id="<?php echo $item->id;  ?>" id="edu_edit_form_<?php echo $item->id; ?>" title="Edit Educations"><i class="fa fa-pencil"></i></a>
		</td>
		</tr>
		<?php } } else{ ?>

		<?php }?>
	</tbody>

</table>
</div>