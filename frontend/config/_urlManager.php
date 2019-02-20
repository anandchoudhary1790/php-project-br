<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=> [
        // Pages
        ['pattern'=>'/', 'route'=>'site/index'],
        ['pattern'=>'gallery', 'route'=>'site/gallery'],
        ['pattern'=>'contact', 'route'=>'site/contact'],
        ['pattern'=>'page/<slug>', 'route'=>'page/view'],

        // Articles
        ['pattern'=>'article/index', 'route'=>'article/index'],
        ['pattern'=>'article/attachment-download', 'route'=>'article/attachment-download'],
        ['pattern'=>'article/<slug>', 'route'=>'article/view'],

        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article', 'only' => ['index', 'view', 'options']],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/user', 'only' => ['index', 'view', 'options']],

        ['pattern'=>'login', 'route'=>'user/sign-in/login'],
        ['pattern'=>'login-ajax', 'route'=>'user/sign-in/login-ajax'],
        ['pattern'=>'ajax-unique', 'route'=>'user/sign-in/ajax-unique'],
        ['pattern'=>'otp-verify', 'route'=>'user/sign-in/otp-verify'],
        ['pattern'=>'login-otp', 'route'=>'user/sign-in/check-otp'],
        ['pattern'=>'login-otp/<token>', 'route'=>'user/sign-in/check-otp'],
        ['pattern'=>'signup', 'route'=>'user/sign-in/signup'],
        ['pattern'=>'ajax-signup', 'route'=>'user/sign-in/ajax-signup'],
        ['pattern'=>'logout', 'route'=>'user/sign-in/logout'],



        // Doctor
        ['pattern'=>'doctor/edit-profile', 'route'=>'user/doctor/edit-profile'],
        ['pattern'=>'doctor/profile', 'route'=>'user/doctor/profile'],

        ['pattern'=>'doctor/ajax-patient-list', 'route'=>'user/doctor/ajax-patient-list'],
        ['pattern'=>'doctor/attenders',     'route'=>'user/doctor/attenders-list'],
        ['pattern'=>'doctor/attender-show', 'route'=>'user/doctor/attender-details'],
        ['pattern'=>'doctor/attender-update', 'route'=>'user/doctor/attender-update'],
        ['pattern'=>'doctor/attender-delete', 'route'=>'user/doctor/attender-delete'],
        ['pattern'=>'doctor/ajax-treatment-list', 'route'=>'user/doctor/ajax-treatment-list'],
        ['pattern'=>'doctor/my-patients', 'route'=>'user/doctor/my-patients'],
        ['pattern'=>'doctor/customer-care', 'route'=>'user/doctor/customer-care'],

        ['pattern'=>'doctor/accept-hospital-request', 'route'=>'user/doctor/accept-hospital-request'],
        ['pattern'=>'doctor/update-status', 'route'=>'user/doctor/update-status'],
        ['pattern'=>'doctor/ajax-city-list', 'route'=>'user/doctor/ajax-city-list'],
        ['pattern'=>'doctor/city-list', 'route'=>'user/doctor/city-list'],
        ['pattern'=>'doctor/add-more-shift', 'route'=>'user/doctor/add-more-shift'],

        ['pattern'=>'doctor/appointments', 'route'=>'user/doctor/appointments'],
        ['pattern'=>'doctor/appointments/<type>', 'route'=>'user/doctor/appointments'],
        ['pattern'=>'doctor/ajax-token', 'route'=>'user/doctor/ajax-token'],
        ['pattern'=>'doctor/ajax-current-appointment', 'route'=>'user/doctor/ajax-current-appointment'],
        ['pattern'=>'doctor/ajax-appointment', 'route'=>'user/doctor/ajax-appointment'],

        ['pattern'=>'doctor/get-next-slots', 'route'=>'user/doctor/get-next-slots'],
        ['pattern'=>'doctor/get-date-shifts', 'route'=>'user/doctor/get-date-shifts'],

        ['pattern'=>'doctor/appointment-payment-confirm', 'route'=>'user/doctor/appointment-payment-confirm'],
        ['pattern'=>'doctor/current-appointment-shift-update', 'route'=>'user/doctor/current-appointment-shift-update'],
        ['pattern'=>'doctor/booking-confirm', 'route'=>'user/doctor/booking-confirm'],
        ['pattern'=>'doctor/booking-confirm-step2', 'route'=>'user/doctor/booking-confirm-step2'],
        ['pattern'=>'doctor/appointment-booked', 'route'=>'user/doctor/appointment-booked'],
        ['pattern'=>'doctor/get-appointment-detail', 'route'=>'user/doctor/get-appointment-detail'],
        ['pattern'=>'doctor/ajax-cancel-appointment', 'route'=>'user/doctor/ajax-cancel-appointment'],


        ['pattern'=>'doctor/my-shifts', 'route'=>'user/doctor/my-shifts'],
        ['pattern'=>'doctor/add-shift', 'route'=>'user/doctor/add-shift'],
        ['pattern'=>'doctor/add-shift/<id>', 'route'=>'user/doctor/add-shift'],
        ['pattern'=>'doctor/edit-shift/<id>', 'route'=>'user/doctor/edit-shift'],
        ['pattern'=>'doctor/delete-shift-with-address', 'route'=>'user/doctor/delete-shift-with-address'],

        ['pattern'=>'doctor/day-shifts', 'route'=>'user/doctor/day-shifts'],
        ['pattern'=>'doctor/update-shift-status', 'route'=>'user/doctor/update-shift-status'],
        ['pattern'=>'doctor/get-shift-details', 'route'=>'user/doctor/get-shift-details'],
        ['pattern'=>'doctor/shift-update', 'route'=>'user/doctor/shift-update'],
        ['pattern'=>'doctor/ajax-address-list', 'route'=>'user/doctor/ajax-address-list'],


        ['pattern'=>'doctor/shifts', 'route'=>'user/doctor/shifts'],
        ['pattern'=>'doctor/update-shift', 'route'=>'user/doctor/update-shift'],
        ['pattern'=>'doctor/ajax-shift-details', 'route'=>'user/doctor/ajax-shift-details'],

        ['pattern'=>'doctor/ajax-tab-content', 'route'=>'user/doctor/ajax-tab-content'],
        ['pattern'=>'doctor/appointment-status-update', 'route'=>'user/doctor/appointment-status-update'],

        ['pattern'=>'doctor/experiences', 'route'=>'user/doctor/experiences'],
        ['pattern'=>'doctor/experience-show', 'route'=>'user/doctor/experience-details'],
        ['pattern'=>'doctor/experience-update', 'route'=>'user/doctor/experience-update'],
        ['pattern'=>'doctor/experience-delete', 'route'=>'user/doctor/experience-delete'],

        ['pattern'=>'doctor/educations', 'route'=>'user/doctor/educations'],
        ['pattern'=>'doctor/education-show', 'route'=>'user/doctor/education-details'],
        ['pattern'=>'doctor/education-update', 'route'=>'user/doctor/education-update'],
        ['pattern'=>'doctor/education-delete', 'route'=>'user/doctor/education-delete'],


        ['pattern'=>'doctor/patient-history','route'=>'user/doctor/patient-history'],
        ['pattern'=>'doctor/ajax-history-content', 'route'=>'user/doctor/ajax-history-content'],
        ['pattern'=>'doctor/ajax-history-appointment','route'=>'user/doctor/ajax-history-appointment'],

        ['pattern'=>'doctor/user-statistics-data', 'route'=>'user/doctor/user-statistics-data'],
        ['pattern'=>'doctor/ajax-user-statistics-data', 'route'=>'user/doctor/ajax-user-statistics-data'],
        ['pattern'=>'doctor/ajax-statistics-data', 'route'=>'user/doctor/ajax-statistics-data'],

         ['pattern'=>'doctor/services', 'route'=>'user/doctor/services'],
        ['pattern'=>'doctor/services-list', 'route'=>'user/doctor/services-details'],
        ['pattern'=>'doctor/services-update', 'route'=>'user/doctor/services-update'],
        ['pattern'=>'doctor/services-delete', 'route'=>'user/doctor/services-delete'],


        //Patient
        ['pattern'=>'patient/my-doctors', 'route'=>'user/patient/my-doctors'],
        ['pattern'=>'patient/my-payments', 'route'=>'user/patient/my-payments'],
        ['pattern'=>'patient/records', 'route'=>'user/patient/records'],
        ['pattern'=>'patient/profile', 'route'=>'user/patient/profile'],
        ['pattern'=>'patient/appointments', 'route'=>'user/patient/appointments'],
        ['pattern'=>'patient/appointments/<type>', 'route'=>'user/patient/appointments'], 
        ['pattern'=>'patient/appointment-details/<id>', 'route'=>'user/patient/appointment-details'],
        ['pattern'=>'patient/ajax-tab-content', 'route'=>'user/patient/ajax-tab-content'],
        ['pattern'=>'patient/favorite', 'route'=>'user/patients/favorite'],
        ['pattern'=>'patient/record-list', 'route'=>'user/patients/record-list'],
        ['pattern'=>'patient/patient-record-files/<slug>', 'route'=>'user/patient/patient-record-files'],
        ['pattern'=>'patient/customer-care', 'route'=>'user/patient/customer-care'],
        ['pattern'=>'patient/patient-appointments/<id>', 'route'=>'user/patient/patient-appointments'],
        ['pattern'=>'patient/add-update-record', 'route'=>'user/patient/add-update-record'],
        ['pattern'=>'patient/delete-record', 'route'=>'user/patient/delete-record'],
        ['pattern'=>'patient/ajax-cancel-appointment', 'route'=>'user/patient/ajax-cancel-appointment'],


        ['pattern'=>'hospital/edit-profile',     'route'=>'user/hospital/edit-profile'],
        ['pattern'=>'hospital/profile', 'route'=>'user/hospital/profile'],
        ['pattern'=>'hospital/ajax-treatment-list', 'route'=>'user/hospital/ajax-treatment-list'],
        ['pattern'=>'hospital/my-patients', 'route'=>'user/hospital/my-patients'],
        ['pattern'=>'hospital/my-doctors', 'route'=>'user/hospital/my-doctors'],
        ['pattern'=>'hospital/get-search-list', 'route'=>'user/hospital/get-search-list'],
        ['pattern'=>'hospital/get-detailurl', 'route'=>'user/hospital/get-detailurl'],
        
        ['pattern'=>'hospital/find-doctors', 'route'=>'user/hospital/find-doctors'],
        ['pattern'=>'hospital/aboutus', 'route'=>'user/hospital/aboutus'],

        ['pattern'=>'hospital/services', 'route'=>'user/hospital/services'],
        ['pattern'=>'hospital/services-list', 'route'=>'user/hospital/services-details'],
        ['pattern'=>'hospital/services-update', 'route'=>'user/hospital/services-update'],
        ['pattern'=>'hospital/services-delete', 'route'=>'user/hospital/services-delete'],

        ['pattern'=>'hospital/customer-care', 'route'=>'user/hospital/customer-care'],

        ['pattern'=>'hospital/speciality', 'route'=>'user/hospital/speciality'],

        ['pattern'=>'hospital/attenders',     'route'=>'user/hospital/attenders-list'],
        ['pattern'=>'hospital/attender-show', 'route'=>'user/hospital/attender-details'],
        ['pattern'=>'hospital/attender-update', 'route'=>'user/hospital/attender-update'],
        ['pattern'=>'hospital/attender-delete', 'route'=>'user/hospital/attender-delete'],

        ['pattern'=>'hospital/update-status', 'route'=>'user/hospital/update-status'],
        ['pattern'=>'hospital/city-list', 'route'=>'user/hospital/city-list'],

        // hospitals Appointment
        ['pattern'=>'hospital/appointments', 'route'=>'user/hospital/appointments'],
        ['pattern'=>'hospital/appointments/<slug>', 'route'=>'user/hospital/appointments'],
        ['pattern'=>'hospital/ajax-token', 'route'=>'user/hospital/ajax-token'],
        ['pattern'=>'hospital/ajax-appointment', 'route'=>'user/hospital/ajax-appointment'],

        ['pattern'=>'hospital/shifts', 'route'=>'user/hospital/shifts'],
        ['pattern'=>'hospital/ajax-shift-details', 'route'=>'user/hospital/ajax-shift-details'],
        ['pattern'=>'hospital/ajax-tab-content', 'route'=>'user/hospital/ajax-tab-content'],
        ['pattern'=>'hospital/appointment-status-update', 'route'=>'user/hospital/appointment-status-update'],
        ['pattern'=>'hospital/add-appointment', 'route'=>'user/hospital/doctor-add-appointment'],
        ['pattern'=>'hospital/booking-confirm', 'route'=>'user/hospital/booking-confirm'],
        ['pattern'=>'hospital/appointment-booked', 'route'=>'user/hospital/appointment-booked'],

        ['pattern'=>'hospital/get-next-slots', 'route'=>'user/hospital/get-next-slots'],
        ['pattern'=>'hospital/get-date-shifts', 'route'=>'user/hospital/get-date-shifts'],

        ['pattern'=>'hospital/patient-history', 'route'=>'user/hospital/patient-history'],
        ['pattern'=>'hospital/patient-history/<slug>', 'route'=>'user/hospital/patient-history'],
        ['pattern'=>'hospital/ajax-history-content', 'route'=>'user/hospital/ajax-history-content'],
        ['pattern'=>'hospital/ajax-history-appointment','route'=>'user/hospital/ajax-history-appointment'],

        ['pattern'=>'hospital/user-statistics-data', 'route'=>'user/hospital/user-statistics-data'],
        ['pattern'=>'hospital/user-statistics-data/<slug>', 'route'=>'user/hospital/user-statistics-data'],
        ['pattern'=>'hospital/ajax-user-statistics-data', 'route'=>'user/hospital/ajax-user-statistics-data'],
        ['pattern'=>'hospital/ajax-statistics-data', 'route'=>'user/hospital/ajax-statistics-data'],

        // Attender Moduel
        ['pattern'=>'attender/edit-profile',     'route'=>'user/attender/edit-profile'],
        ['pattern'=>'attender/ajax-treatment-list', 'route'=>'user/attender/ajax-treatment-list'],
        ['pattern'=>'attender/my-patients',     'route'=>'user/attender/my-patients'],
        ['pattern'=>'attender/customer-care', 'route'=>'user/attender/customer-care'],

        ['pattern'=>'attender/appointments', 'route'=>'user/attender/appointments'],
        ['pattern'=>'attender/appointments/<slug>', 'route'=>'user/attender/appointments'],
        ['pattern'=>'attender/ajax-token', 'route'=>'user/attender/ajax-token'],
        ['pattern'=>'attender/ajax-appointment', 'route'=>'user/attender/ajax-appointment'],

        ['pattern'=>'attender/get-next-slots', 'route'=>'user/attender/get-next-slots'],
        ['pattern'=>'attender/get-date-shifts', 'route'=>'user/attender/get-date-shifts'],

        ['pattern'=>'attender/my-shifts', 'route'=>'user/attender/my-shifts'],
        ['pattern'=>'attender/day-shifts', 'route'=>'user/attender/day-shifts'],
        ['pattern'=>'attender/update-shift-status', 'route'=>'user/attender/update-shift-status'],
        ['pattern'=>'attender/get-shift-details', 'route'=>'user/attender/get-shift-details'],
        ['pattern'=>'attender/shift-update', 'route'=>'user/attender/shift-update'],
        ['pattern'=>'attender/ajax-address-list', 'route'=>'user/attender/ajax-address-list'],

        ['pattern'=>'attender/appointment-payment-confirm', 'route'=>'user/attender/appointment-payment-confirm'],
        ['pattern'=>'attender/current-appointment-shift-update', 'route'=>'user/attender/current-appointment-shift-update'],
        ['pattern'=>'attender/booking-confirm', 'route'=>'user/attender/booking-confirm'],
        ['pattern'=>'attender/booking-confirm-step2', 'route'=>'user/attender/booking-confirm-step2'],
        ['pattern'=>'attender/appointment-booked', 'route'=>'user/attender/appointment-booked'],
        ['pattern'=>'attender/get-appointment-detail', 'route'=>'user/attender/get-appointment-detail'],
        ['pattern'=>'attender/ajax-cancel-appointment', 'route'=>'user/attender/ajax-cancel-appointment'],

        ['pattern'=>'attender/shifts', 'route'=>'user/attender/shifts'],
        ['pattern'=>'attender/ajax-shift-details', 'route'=>'user/attender/ajax-shift-details'],
        ['pattern'=>'attender/ajax-tab-content', 'route'=>'user/attender/ajax-tab-content'],
        ['pattern'=>'attender/appointment-status-update', 'route'=>'user/attender/appointment-status-update'],
        ['pattern'=>'attender/add-appointment', 'route'=>'user/attender/doctor-add-appointment'],

        ['pattern'=>'attender/user-statistics-data', 'route'=>'user/attender/user-statistics-data'],
        ['pattern'=>'attender/user-statistics-data/<slug>', 'route'=>'user/attender/user-statistics-data'],
        ['pattern'=>'attender/ajax-user-statistics-data', 'route'=>'user/attender/ajax-user-statistics-data'],
        ['pattern'=>'attender/ajax-statistics-data', 'route'=>'user/attender/ajax-statistics-data'],


        //Common
        ['pattern'=>'doctors', 'route'=>'common/doctors'],
        ['pattern'=>'speciality', 'route'=>'common/speciality'],
        ['pattern'=>'find-doctors/<slug>', 'route'=>'common/find-doctors'],
        
        ['pattern'=>'doctor/profile/<slug>', 'route'=>'common/doctor-profile'],
        ['pattern'=>'<speciality>/doctors', 'route'=>'common/speciality-doctors'],
        ['pattern'=>'doctor-shifts', 'route'=>'common/doctor-shifts'],
        ['pattern'=>'ajax-unique-group-number', 'route'=>'common/ajax-unique-group-number'],
        ['pattern'=>'patients/hospitals/<id>', 'route'=>'common/hospitals'],
        ['pattern'=>'hospital/profile/<slug>', 'route'=>'common/hospital-profile'],
        ['pattern'=>'patient/patient-record-update','route'=>'user/patient/patient-record-update'],

        ['pattern'=>'patient/reminder','route'=>'user/patient/reminder'],
        ['pattern'=>'patient/reminder-update','route'=>'user/patient/reminder-update'],
        ['pattern'=>'patient/ajax-check-reminder','route'=>'user/patient/ajax-check-reminder'], 
        ['pattern'=>'patient/ajax-check-reminder-list','route'=>'user/patient/ajax-check-reminder-list'],
         ['pattern'=>'patient/ajax-check-reminder-delete','route'=>'user/patient/ajax-check-reminder-delete'],

        ['pattern'=>'patient/get-next-slots', 'route'=>'user/patient/get-next-slots'],

        ['pattern'=>'patient/get-date-shifts', 'route'=>'user/patient/get-date-shifts'],

        
        // Appointment

        ['pattern'=>'appointment-time/<slug>', 'route'=>'search/appointment-time'],

        ['pattern'=>'appointment-confirm/<doctor>/<slot_id>', 'route'=>'common/appointment-confirm'],


        // Site
        ['pattern'=>'hospital','route'=>'search/hospital'],
        ['pattern'=>'hospital/<slug>','route'=>'search/hospital','defaults' => ['slug' => null]],
        ['pattern'=>'doctor','route'=>'search/doctor'],
        ['pattern'=>'doctor/<slug>', 'route'=>'search/doctor','defaults' => ['slug' => null]],
        ['pattern'=>'specialization','route'=>'search/specialization'],
        ['pattern'=>'specialization/<slug>', 'route'=>'search/specialization','defaults' => ['slug' => null]],
        ['pattern'=>'treatment','route'=>'search/treatment'],
        ['pattern'=>'treatment/<slug>', 'route'=>'search/treatment','defaults' => ['slug' => null]],

        ['pattern'=>'favorite', 'route'=>'search/favorite'],

        ['pattern'=>'get-shift-booking-days', 'route'=>'search/get-shift-booking-days'],

        



    ]
];
