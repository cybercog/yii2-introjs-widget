<?php

namespace pvlg\introjs;

class IntrojsAsset extends \yii\web\AssetBundle
{

    public $sourcePath = '@vendor/bower/intro.js';
    public $js = [
        'minified/intro.min.js',
    ];
    public $css = [
        'minified/introjs.min.css',
        'themes/introjs-nassim.css',
    ];
}
