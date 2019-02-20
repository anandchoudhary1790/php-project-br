<?php
use yii\helpers\Html;
use common\components\DrsPanel;
use common\models\UserAddress;


$week_array=DrsPanel::getWeekArray();

$baseUrl= Yii::getAlias('@frontendUrl'); 

?>

<div class="col-sm-12 table-responsive">
  <?php 
  foreach($week_array as $key=>$week) {  $scheduleslist= DrsPanel::weekSchedules($user_id,$week); ?>



  <div class="mainaccoridan">
    <div class="panel-group" id="accordion">
      <div class="panel panel-default">
        <div class="panel-heading">

          <h5 data-toggle="collapse" data-parent="#accordion<?php echo $week;  ?>" href="#collapse<?php echo $week;  ?>" class="panel-title expand">
           <div class="right-arrow pull-right">+</div>
           <p> <?php echo $week; ?></p>
         </h5>
       </div>
       <div id="collapse<?php echo $week;  ?>" class="panel-collapse collapse">
        <div class="panel-body sift_days">
            <?php foreach ($scheduleslist as $key => $value) {
                $address=UserAddress::findOne($value['address_id']);
                ?>
                <div class="morning-parttiming">
                    <div class="main-todbox">
                        <div class="pull-left">
                            <div class="moon-cionimg"><img src="<?php echo $baseUrl?>/images/doctor-clock-icon.png" alt="image"><span> <?php echo date('h:i a',$value['start_time']); ?>   -  <?php echo date('h:i a',$value['end_time']); ?>  </span> </div>
                        </div>

                    </div>
                    <div class="main-todbox no-pd">
                        <div class="pull-left">
                            <div class="moon-cionimg "><img src="<?php echo $baseUrl?>/images/doctor-profile-icon3.png" alt="image"> <span> <strong class="hospota_add"> <?php echo isset($address->address)?$address->address:''; ?></strong></span> </div>
                        </div>
                        <div class="pull-right icon-border">
                            <?php
                            if(isset($value['is_edit'])==1)
                            {
                                $link = Html::a('<span class="glyphicon glyphicon-pencil "></span>',null, ['aria-label'=>'View', 'title'=>'View','class'=>'get-shift-data','id'=>$value['id']]);

                                $del = Html::a('<span class="glyphicon glyphicon-trash "></span>',null, ['aria-label'=>'View', 'title'=>'View','class'=>'confirm-dailog','data-id'=>$value['id']]);
                                echo  $link.$del;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="main-todbox no-pd">
                        <div class="pull-left">
                            <div class="moon-cionimg">
                                <p> <?= Drspanel::getAddressLine($address); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>



        </div>
      </div>
    </div>

  </div> 
</div>


<?php  } ?>
<?php /*
    <table class="table no-margin">
        <thead>
        <tr>
            <th>Day</th>
            <th>Shift Total</th>
        </tr>
        </thead>
        <tbody>
            <?php
            $week_array=DrsPanel::getWeekArray();
            foreach($week_array as $week){ 
             ?>
                <tr>
                    <td><?php echo $week; ?></td>
                    <td>
                        <?php echo $totla_shift=UserSchedule::dayShiftTotal($user_id,$week); ?>
                    </td>
                    <td>
                    <?php if($totla_shift){ ?>
                        <a href="<?php echo Yii::getAlias('@backendUrl').'/doctor/update-shift-time?id='.$user_id.'&day='.$week;?>" ><i class="fa fa-pencil"></i></a>
                        <?php } else { ?>
                        <a href="<?php echo Yii::getAlias('@backendUrl').'/doctor/add-shift?id='.$user_id.'&day='.$week;?>" title="Add Shift" id="add-new-shift"><i class="fa fa-plus"></i></a>
                        <?php } ?>
                    </td>
                </tr>  
            <?php }
            ?>
        </tbody>
      </table>   */ ?>
    </div><!-- /.table-responsive -->

