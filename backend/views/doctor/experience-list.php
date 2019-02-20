<?php 
$expForm="'experience-form'";
$js="
      $('.edit_exp_form').on('click', function () {
       $.ajax({
          url: $expForm,
          data: {user_id:$(this).attr('doctor-id'),exp_id:$(this).attr('data-id'),}
    })
      .done(function( msg ) { 

        $('#exp-upseart-body').html('');
        $('#exp-upseart-body').html(msg);
        $('#upseart_exp_modal').modal({backdrop: 'static',keyboard: false})

      });
    });

";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="edu-list">
<table class="table">
	<thead>
	<th>#</th>
	<th>Hospital Name</th>
	<th>Start</th>
	<th>End</th>
	<th>Action</th>
	</thead>
	<tbody>
		<?php if(count($edu_list)>0){ 
		foreach ($edu_list as $key => $item) { ?>
		<tr>
		<td><?php echo $key+1;?></td>
		<td><?php echo $item->hospital_name; ?></td>
		<td><?php echo $item->start; ?></td>
		<td><?php echo $item->end; ?></td>
		<td>
		<a href="javascript:void(0)" class="edit_exp_form" doctor-id="<?php echo $item->user_id; ?>" data-id="<?php echo $item->id;  ?>" id="edu_edit_form_<?php echo $item->id; ?>" title="Edit Educations"><i class="fa fa-pencil"></i></a>
		</td>
		</tr>
		<?php } } else{ ?>

		<?php }?>
	</tbody>

</table>
</div>