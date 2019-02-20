<?php
use common\models\Groups;
use common\components\DrsPanel;

if(!Yii::$app->user->isGuest){
    $loginuser=Yii::$app->user->id;

    $baseUrl= Yii::getAlias('@frontendUrl');
    if($request_to='doctor'){
        $groupid=Groups::GROUP_DOCTOR;
    }else{
        $groupid=Groups::GROUP_HOSPITAL;
    }
    $favorite=DrsPanel::checkFavorite($loginuser,$doctor_id);
    if(!empty($favorite)){
        $status = isset($favorite['status'])?$favorite['status']:'0';
    }
    else{
        $status = 0;
    }
    $favorite_url="'".$baseUrl."/search/favorite'";
    $js="
      $(document).on('click', '.update-status',function () {
        var status  = $(this).attr('data-value');
        
          $.ajax({
                    method:'POST',
                    url: $favorite_url,
                    data: {user_id:$loginuser,profile_id:$doctor_id,status:status}
              })
                .done(function( responce_data ) { 
                   $('#favorite_add').html(responce_data);
             })// ajax close
      });
      //close get shift
    ";
    $this->registerJs($js,\yii\web\VIEW::POS_END);
    ?>
    <span id="favorite_add">
    <?php echo $this->render('_favorite_status',['status' => $status]) ?>
    </span>
<?php } else{ ?>
    <span id="favorite_add">
         <a href="javascript:void(0);" class="modal-call" id="login-popup"><i class="fa fa-heart-o" style="font-size:20px;color:#a42127"></i>
    </a>
    </span>
<?php }?>