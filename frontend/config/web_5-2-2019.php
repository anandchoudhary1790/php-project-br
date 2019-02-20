<?php
use \yii\web\Request;
$baseUrl = str_replace('/frontend/web', '', (new Request)->getBaseUrl());

$config = [
    'homeUrl'=>Yii::getAlias('@frontendUrl'),
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    'bootstrap' => ['maintenance'],
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
            //'shouldBeActivated' => true
        ],
        'api' => [
            'class' => 'frontend\modules\api\Module',
            'modules' => [
                'v1' => 'frontend\modules\api\v1\Module'
            ]
        ]
    ],
    'components' => [
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'github' => [
                    'class' => 'yii\authclient\clients\GitHub',
                    'clientId' => env('GITHUB_CLIENT_ID'),
                    'clientSecret' => env('GITHUB_CLIENT_SECRET')
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => env('FACEBOOK_CLIENT_ID'),
                    'clientSecret' => env('FACEBOOK_CLIENT_SECRET'),
                    'scope' => 'email,public_profile',
                    'attributeNames' => [
                        'name',
                        'email',
                        'first_name',
                        'last_name',
                    ]
                ]
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'maintenance' => [
            'class' => 'common\components\maintenance\Maintenance',
            'enabled' => function ($app) {
                return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            }
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yii\db\Command::execute'],
                    'levels' => ['info'],
                    'logFile' => '@runtime/logs/info.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yii\mail\BaseMailer::send'],
                    'logVars' => [null],
                    'logFile' => '@runtime/logs/email.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['yii\web\HttpException:404'],
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/404.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => ['frontend\controllers\ApiDrspanelController::actionGender',
                        'frontend\controllers\ApiDrspanelController::actionGroups',
                        'frontend\controllers\ApiDrspanelController::actionGetCountryCode',
                        'frontend\controllers\ApiDrspanelController::actionGetMetaData',
                        'frontend\controllers\ApiDrspanelController::actionSendOtp',
                        'frontend\controllers\ApiDrspanelController::actionVerifyOtp',
                        'frontend\controllers\ApiDrspanelController::actionSignup',
                        'frontend\controllers\ApiDrspanelController::actionGetProfile',
                        'frontend\controllers\ApiDrspanelController::actionEditDoctorProfile'
,
                        'frontend\controllers\ApiDrspanelController::actionEditPatientProfile',

                        'frontend\controllers\ApiDrspanelController::actionGetMyDoctors',
                        'frontend\controllers\ApiDrspanelController::actionCurrentAppointmentAffair',

                        'frontend\controllers\ApiDrspanelController::actionFindHospitalDoctors',

                        'frontend\controllers\ApiDrspanelController::actionAddAttender',
                        'frontend\controllers\ApiDrspanelController::actionAttenderList',
                        'frontend\controllers\ApiDrspanelController::actionAddNewAddress',
                        'frontend\controllers\ApiDrspanelController::actionUpdateAddress',
                        'frontend\controllers\ApiDrspanelController::actionGetShiftsDetail',
                        'frontend\controllers\ApiDrspanelController::actionUpsertShift',
                        'frontend\controllers\ApiDrspanelController::actionUpsertSpecialityTreatment',
                        'frontend\controllers\ApiDrspanelController::actionPaytmWalletCallback',
                        'frontend\controllers\ApiDrspanelController::actionPaytmResponse',

                        'frontend\controllers\ApiDrspanelController::actionDoctorDetail',
                        'frontend\controllers\ApiDrspanelController::actionAddShift',

                    ],
                    'levels' => ['info'],
                    'logFile' => '@runtime/logs/apilogs_'.env('LOG_FILE').'.log',
                ],

                [
                    'class' => 'yii\log\FileTarget',
                    'categories' => [
                        'frontend\controllers\ApiDrspanelController::actionFindDoctors',
                        'frontend\controllers\ApiDrspanelController::actionAppointmentShedules',
                        'frontend\controllers\ApiDrspanelController::actionDoctorShiftList',
                        'frontend\controllers\ApiDrspanelController::actionGetBookingShifts',
                        'frontend\controllers\ApiDrspanelController::actionGetBookingShiftSlots',
                        'frontend\controllers\ApiDrspanelController::actionDoctorAddAppointment'



                    ],
                    'levels' => ['info'],
                    'logFile' => '@runtime/logs/bookinglogs_'.env('LOG_FILE').'.log',
                ],


            ],
        ],
        'request' => [
            'baseUrl' => $baseUrl,
            'cookieValidationKey' => env('FRONTEND_COOKIE_VALIDATION_KEY'),
            'csrfParam' => '_frontendCSRF',

        ],
        'session' => [
            'name' => 'PHPFRONTSESSID',
            'savePath' => __DIR__ . '/../runtime',
        ],
        'user' => [
            'class'=>'yii\web\User',
            'identityClass' => 'common\models\User',
            'loginUrl'=>['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => 'common\behaviors\LoginTimestampBehavior',
            'identityCookie' => [
                'name' => '_frontendUser', // unique for frontend
            ]
            
        ]
    ]
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class'=>'yii\gii\Module',
        'generators'=>[
            'crud'=>[
                'class'=>'yii\gii\generators\crud\Generator',
                'messageCategory'=>'frontend'
            ]
        ]
    ];
}

return $config;
