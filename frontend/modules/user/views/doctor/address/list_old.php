<?php
$js="
     $('#get_addaddress_model').on('click', function () {
        $('#addaddress').modal({backdrop: 'static',keyboard: false,show: true})
    }); 

    function updateAddress(id){
    $.ajax({
        url: 'address-update',
        dataType:   'html',
        method:     'POST',
        data: { id: id},
        success: function(response){
            $('#updateaddress').empty();
            $('#updateaddress').append(response);
            $('#updateaddress').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
        }
    });

    
}
";
$this->registerJs($js,\yii\web\VIEW::POS_END); 
?>
<div class="col-md-4">
    <div class="nav-tabs-custom">
        <div class="panel-heading">
            <h3 class="panel-title">Hospitals/Clinics
                <div class="text-right">
                    <a href="javascript:void(0)" title="Add More Addresses" id="get_addaddress_model">Add More</a>
                </div>
            </h3>
        </div>
        <div class="panel-body">
            <?php
            echo \yii\widgets\ListView::widget( [
                'layout' => "<div class='list-item' id='listitems'>{items}</div>",
                'dataProvider' => $addressProvider,
                'itemView' => '_address',
                ]);

                ?>

            </div>
        </div>
    </div>


<div class="register-section">
<div id="addaddress" class="modal"  role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
			 <h4 class="modal-title" id="myModalContact">Add Address To Doctor's Profile</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
               
            </div>
            <div class="modal-body">
                    <?php $address=new \common\models\UserAddress();
                    $address->user_id=$userProfile->user_id;
                    ?>
                    <?= $this->render('_addaddress', [
                        'model' => $address
                    ]) ?>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>
</div>
