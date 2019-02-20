<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\assets;

use yii\web\AssetBundle;

class BackendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/style.css',
        'css/bootstrap-timepicker.min.css',
        'css/custom.css',
        'source/jquery.fancybox.css'


    ];
    public $js = [
       // 'js/bootstrap.min.js',
        'js/bootstrap-timepicker.min.js',
        'js/app.js',
        'js/custom.js',
        'source/jquery.fancybox.pack.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'common\assets\AdminLte',
        'common\assets\Html5shiv'
    ];
}
