<?php
use common\components\DrsPanel;
$listUrl="'education-list'";
$eduForm="'education-form'";
$explistUrl="'experience-list'";
$expForm="'experience-form'";
$js="
     $('#get_exp_modal').on('click', function () {
       $.ajax({
          url: $explistUrl,
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
          url: $expForm,
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
          url: $listUrl,
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
          url: $eduForm,
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

                <div class="editTopBtn">
                        <a href="javascript:void(0)" title="Edit Status" data-target="#editlivestatus" data-toggle="modal">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>

            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
    <div class="col-md-4 col-sm-8 col-xs-12 hide">
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

                <div class="editTopBtn">
                        <a href="javascript:void(0)" title="Edit Status" data-target="#editrating" data-toggle="modal">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix visible-sm-block"></div>

    <div class="col-md-4 col-sm-8 col-xs-12 hide">
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
                <div class="editTopBtn">
                        <a href="javascript:void(0)" title="Edit Status" data-target="#editfeecomm" data-toggle="modal">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
            </div><!-- /.info-box-content -->
        </div><!-- /.info-box -->
    </div><!-- /.col -->
</div>

<div class="row" style="margin-bottom:10px; ">
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-danger" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/appointments?id='.$userProfile->user_id]); ?>';">Appointments History</button>
    </div>

      <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" id="get_edu_modal" class="btn btn-block btn-deafult">Education</button>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" id="get_exp_modal" class="btn btn-block btn-success">Experience</button>
    </div>
     <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/attender-list?id='.$userProfile->user_id]); ?>';">My Attenders</button>
    </div>

    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/requested-hospital?id='.$userProfile->user_id]); ?>';">Requested Hospital</button>
    </div>
    <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/hospitals?id='.$userProfile->user_id]);?>'">Hospital/Clinics</button>
    </div>
   
</div>

<!-- Second Row -->
<div class="row" style="margin-bottom:10px; ">
   
     <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-danger" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/my-shifts?id='.$userProfile->user_id]); ?>';">My Shifts </button>
    </div>
     <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/add-shift?id='.$userProfile->user_id]); ?>';">Add Shifts</button>
    </div>
     <div class="col-md-2 col-sm-6 col-xs-12">
        <button type="button" class="btn btn-block btn-success" onclick="location.href='<?php echo yii\helpers\Url::to(['doctor/day-shifts?id='.$userProfile->user_id]); ?>';">Today Timing</button>
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