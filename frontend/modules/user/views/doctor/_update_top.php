<?php
use common\components\DrsPanel;

$js="
     $('#get_exp_modal').on('click', function () {
       $.ajax({
          method:'POST',
          url: 'experience-list',
          data: {user_id:$userProfile->user_id}
    })
      .done(function( msg ) { 

        $('#exp-set-model-body').html('');
        $('#exp-set-model-body').html(msg);
        $('#exp_list_modal').modal({backdrop: 'static',keyboard: false})

      });
    }); 
    // add/update modal
     $('#get_exp_add_model').on('click', function () {
       $.ajax({
          method:'POST',
          url: 'experience-form',
          data: {user_id:$userProfile->user_id}
    })
      .done(function( msg ) { 

        $('#exp-upseart-body').html('');
        $('#exp-upseart-body').html(msg);
        $('#upseart_exp_modal').modal({backdrop: 'static',keyboard: false})

      });
    });


    $('#get_edu_modal').on('click', function () {
       $.ajax({
          method:'POST',
          url: 'education-list',
          data: {user_id:$userProfile->user_id}
    })
      .done(function( msg ) { 

        $('#set-model-body').html('');
        $('#set-model-body').html(msg);
        $('#edu_list_modal').modal({backdrop: 'static',keyboard: false})

      });
    }); 
    // add/update modal
     $('#get_add_model').on('click', function () {
       $.ajax({
          url: 'education-form',
          data: {user_id:$userProfile->user_id}
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
<div class="row">
    <div class="col-md-4 col-sm-8 col-xs-12">
        <div class="info-box">
              <span class="info-box-icon bg-aqua">
                <i class="ion ion-ios-people-outline"><p class="info-text">Live Status</p></i>
              </span>
            <div class="info-box-content">

                <span class="info-box-values">
                   <?php echo DrsPanel::getLiveStatus($userProfile->user_id);?>

                                    </span>

            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
    <div class="col-md-4 col-sm-8 col-xs-12">
        <div class="info-box">

                <span class="info-box-icon bg-red">
                <i class="ion ion-ios-gear-outline"><p class="info-text">Rating</p></i>
              </span>
            <div class="info-box-content">
                <span class="info-box-values">
                   <?php
                   $getRating=DrsPanel::getRatingStatus($userProfile->user_id);
                   echo '<p><strong>Show: </strong> ' .$getRating['type'].'</p>';
                   echo '<p><strong>Rating: </strong> ' .$getRating['rating'].'</p>';
                   ?>
                </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-4 col-sm-8 col-xs-12">
        <div class="info-box">

                <span class="info-box-icon bg-green">
                <i class="ion ion-ios-cart-outline"><p class="info-text">Fee %</p></i>
              </span>

            <div class="info-box-content">

                <span class="info-box-values">
                    <?php
                    echo '<p><strong>Booking: </strong> ' .DrsPanel::getAdminCommission($userProfile->user_id,'booking').'</p>';
                    echo '<p><strong>Cancel: </strong> ' .DrsPanel::getAdminCommission($userProfile->user_id,'cancel').'</p>';
                    echo '<p><strong>Reschedule: </strong> ' .DrsPanel::getAdminCommission($userProfile->user_id,'reschedule').'</p>';
                    ?>
                </span>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
</div>

<div class="row">
<?php /* ?>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-danger" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/appointments?id='.$userProfile->user_id]); ?>';">Appointments History</button>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/add-appointments?id='.$userProfile->user_id]); ?>';">Add Appointments</button>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-primary" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/daily-patient-limit?id='.$userProfile->user_id]); ?>';">Daily Patient Limit</button>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-warning">Holidays</button>
    </div>
    <?php */ ?>

    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" id="get_edu_modal" class="btn btn-block btn-primary">Education</button>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" id="get_exp_modal" class="btn btn-block btn-success">Experience</button>
    </div>
</div>

<div class="modal fade" id="edu_list_modal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Doctors Educations</h4>
                <button type="button" id="get_add_model" class="btn btn-success" >Add</button> 
            </div>
            <div class="modal-body" id="set-model-body">
             
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" id="upseart_edu_modal" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Doctors Educations Add/Update</h4>
            </div>
            <div class="modal-body" >
                <div id="upseart-body" >

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" id="exp_list_modal" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Doctors Experience</h4>
                <button type="button" id="get_exp_add_model" class="btn btn-success" >Add</button> 
            </div>
            <div class="modal-body" id="exp-set-model-body">
             
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" id="upseart_exp_modal" >
    <div class="modal-dialog">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="">Doctors Experience Add/Update</h4>
            </div>
            <div class="modal-body" >
                <div id="exp-upseart-body" >

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div>
</div>