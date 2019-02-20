<?php
$baseUrl= Yii::getAlias('@frontendUrl');
?>

<?php if(!empty($history_count)) {
    $total_patient=$history_count['total_patient'];
    $total_cancelled=$history_count['total_cancelled'];
}
if(!empty($typeCount)) {
    $total_online=$typeCount['online'];
    $total_offline=$typeCount['offline'];
} ?>

<div class="row">
    <div class="col-sm-12">
        <div class="search-boxicon">
            <div class="search-iconmain"> <i class="fa fa-search"></i> </div>
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search patient, token ..." title="Type in a name" class="form-control">
        </div>
    </div>
</div>

 <div class="totalpatient-part">
     <ul>
        <li><p>Total Patient <span><?php echo (isset($total_patient))? '('.$total_patient.')':0 ?></span>  </p></li>
        <li><p>Online Patient <span><?php echo (isset($total_online))? '('.$total_online.')':0 ?> </span> </p></li>
        <li><p> Offline Patient <span><?php echo (isset($total_offline))? '('.$total_offline.')':0 ?> </span>  </p></li>
        <li><p> Cancelled <span><?php echo (isset($total_cancelled))? '('.$total_cancelled.')':0 ?> </span>  </p></li>
     </ul>
 </div>


<div id="appointment_shift_slots">
    <?php
    echo $this->render('/doctor/history-statistics/_history_shift_slots',['appointments'=>$appointments,'current_shifts'=>$current_selected,'doctor'=>$doctor,'shifts'=>$shifts]);
    ?>
</div>
<div class="bookappoiment-btn">
    <input value="Statement" class="bookinput" type="button">
</div>