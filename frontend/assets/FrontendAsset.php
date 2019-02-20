<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FrontendAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

     public $css = [

        'css/bootstrap.min.css',
        'css/bootstrap-timepicker.min.css',
        'css/font-awesome.min.css',
        'css/slick.css',
        'css/hover-min.css',
        'css/hover.css',
        'css/swiper.css',
        'css/swiper.min.css',
        'css/animate.css',
        'css/easy-responsive-tabs.css',
        'css/style.css',
        'css/responsive.css',
        'css/sweetalert2.min.css',
        'source/jquery.fancybox.css'


    ];

    public $js = [
        //'js/jquery.min.js',
        'js/bootstrap.min.js',
        'js/bootstrap.bundle.min.js',
        'js/easyResponsiveTabs.js',
        'js/slick.js',
        'js/bootstrap-timepicker.min.js',
        'js/wow.js',
        'js/jquery-ui.js',
        'js/swiper.min.js',
        'js/wow.min.js',
        'js/custom.js',
        'js/drspanel.js',
        'js/sweetalert2.min.js',
        'source/jquery.fancybox.pack.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'common\assets\Html5shiv',
    ];
}
