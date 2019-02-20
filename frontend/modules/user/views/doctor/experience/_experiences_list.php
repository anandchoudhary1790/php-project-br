 <?php 
 use yii\helpers\Html;
 use yii\widgets\ActiveForm;
 use yii\helpers\ArrayHelper;
 use kartik\date\DatePicker;



 $baseUrl= Yii::getAlias('@frontendUrl'); 
 $experienceUrl="'".$baseUrl."/doctor/experience-show'";
$experienceDelete="'".$baseUrl."/doctor/experience-delete'";

$this->registerJs("
$('.call-edit-modal').on('click', function () {
    id=$(this).attr('data-id');
    $.ajax({
          method: 'POST',
          url: $experienceUrl,
          data: { id: id,}
    })
      .done(function( msg ) { 
        if(msg){
        $('#edit-modal-form').html('');
        $('#edit-modal-form').html(msg);
        $('#attenderEdit-modal').modal({backdrop: 'static',keyboard: false,show: true})
        }
      });

   
       });

$('.call-delete-modal').on('click', function () {
    id=$(this).attr('data-id');
    name= $('#hospital-name-'+id).html();
    $('#ConfirmModalHeading').html('Confirm Experience Delete');
    $('#ConfirmModalContent').html('<p>Are You sure to delete '+name+'</p>');
    $('#ConfirmModalShow').modal({backdrop: 'static',keyboard: false,show: true})
    
     .one('click', '#confirm_ok', function(e) {
             $.ajax({
                method:'POST',
                url: $experienceDelete,
                data: { id: id,}
          })
            .done(function( msg ) { 
  
              $('#ConfirmModalShow').modal('hide');
              $('#SuccessModalHeading').html('Experience Delete');
              $('#SuccessModalContent').html(msg);
              $('#success-modal').modal({show: true});


            });
            //ajax close
         }) //confirm alert box close

   
       });

", \yii\web\VIEW::POS_END); 
?>


<div class="edu-list table-responsive">
<table class="table">
  <thead>
  <th>#</th>
  <th>Hospital Name</th>
  <th>Start</th>
  <th>End</th>
  <th>Action</th>
  </thead>
  <tbody>
    <?php  if(!empty($lists))
 {
  foreach ($lists as $key => $list) { 
    ?>
    <tr>
        <td><?php echo $key+1;?></td>
        <td id="hospital-name-<?php echo $list['id']?>">
        <?php echo $list['hospital_name']; ?></td>
        <td><?php echo date('Y',$list['start']); ?></td>
        <td><?php echo (date('Y',$list['end']) > date("Y"))?'Till Now':date('Y',$list['end']); ?></td>
        <td>
            <a href="javascript:void(0)" ><i class="fa fa-pencil call-edit-modal" aria-hidden="true" data-id="<?php echo $list['id']?>"></i></a>
            <a href="javascript:void(0)"><i class="fa fa-trash-o call-delete-modal" data-id="<?php echo $list['id']?>" aria-hidden="true"></i></a>
        </td>
    </tr>
    <?php } /*die;*/ } else{ ?>

    <?php }?>
  </tbody>

</table>
</div>


<div class="register-section">
  <div class="modal fade model_opacity" id="attenderEdit-modal" tabindex="-1" role="dialog" aria-labelledby="addproduct" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
         <h4 class="modal-title" id="myModalContact">Update Experience </h4>
         <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

       </div>
       <div class="modal-body" id="edit-modal-form">

       </div>
     </div><!-- /.modal-content -->
   </div>
 </div>
</div>