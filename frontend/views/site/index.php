<?php
use common\components\DrsPanel;

// $this->title = Yii::$app->name;

$this->title = 'Drspanel :: HomePage';




/*Top Slider*/
echo $this->render('home/slider', ['sliders' => $drsdata['slider_images']]);

/*second Row*/
echo $this->render('home/doctor-hospitals');

/*Popular Speciality/Treatments & Hospitals*/
echo $this->render('home/categories',['categories' => $drsdata['speciality'],'treatements' => $drsdata['treatment'],'hospitals' => $drsdata['hospitals']]);

echo $this->render('home/booked-appointment');
echo $this->render('home/mobile-app');
echo $this->render('home/contact');

