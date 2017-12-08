<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', //Para forzar el tema ,por defecto el tema es darkness.
        'css/site.css',
        '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
        '//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css',
        'codecanyon/css/slick_cfs.css',
        'js/plugins/jquery.nanoscroller/nanoscroller.css',
        'js/plugins/tooltipster/css/tooltipster.css',
        'js/plugins/tooltipster/css/themes/tooltipster-light.css',
        'js/plugins/icheck/skins/flat/red.css',
        '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css',
        'js/plugins/jAlert/jAlert-v4.css',
    ];
    public $js = [
        'js/plugins/tooltipster/js/jquery.tooltipster.min.js',
        'js/plugins/moment-with-locales.min.js',
        'js/plugins/jquery.nanoscroller/jquery.nanoscroller.min.js',
        'js/plugins/waterfall/libs/handlebars/handlebars.js',
        '//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.0/jquery.scrollTo.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js',
        '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/es.js',
        '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js',
        'js/plugins/jAlert/jAlert-v4.min.js',
        'js/plugins/js.cookie.js',
        'js/plugins/BrowserDetect.js',
        'js/plugins/icheck/icheck.min.js',
        'js/config.js',
        'js/helpers.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        //'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\jui\JuiAsset',
    ];
}
