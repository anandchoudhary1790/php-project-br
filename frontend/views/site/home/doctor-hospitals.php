<?php $baseUrl= Yii::getAlias('@frontendUrl'); ?>
<section class="online-appointment">
    <div class="container text-center">
        <h1 class="wow bounceInDown">DRS THE <span>ONLINE</span> APPOINTMENT!</h1>
        <p class="wow bounceInDown">Usu habeo equidem sanctus no. Suas summo id sed, erat erant oporteat cu pri. In eum omnes molestie. <br>Sed ad debet scaevola omnes molestie. Sed ad debet scaevola...</p>
        <div class="row">
            <div class="col-sm-6">
                <a href="<?php echo $baseUrl?>/specialization?type=doctor" class="wow bounceInLeft">
                    <div class="click-box blur-bg">
                        <i><img src="<?php echo $baseUrl ?>/images/icon1.png"></i>
                        <br>
                        <p>Doctors</p>
                    </div>
                </a>
            </div>
            <div class="col-sm-6">
                <a href="<?php echo $baseUrl?>/specialization?type=hospital" class="wow bounceInRight">
                    <div class="click-box green-bg">
                        <i><img src="<?php echo $baseUrl ?>/images/icon2.png"></i>
                        <br>
                        <p>Hospitals</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>