<?php

namespace pvlg\introjs;

use Yii;
use yii\helpers\Json;

class Introjs extends \yii\base\Widget
{

    public $configFile = '@app/config/introjs.php';
    public $config;
    public $introConfig;
    public $cssFile;

    public function init()
    {
        parent::init();

        $this->configFile = Yii::getAlias($this->configFile);

        if (!is_file($this->configFile)) {
            throw new InvalidConfigException("The config introjs does not exist: {$this->configPath}");
        } else {
            $this->configFile = realpath($this->configFile);
        }

        $this->config = require($this->configFile);

        $view = $this->getView();
        IntrojsAsset::register($view);
        if ($this->cssFile) {
            $view->registerCssFile($this->cssFile);
        }
    }

    public function run()
    {
        foreach ($this->config as $introName => $introConfig) {
            if (call_user_func($introConfig['condition'])) {
                $this->introConfig = $introConfig;
                break;
            }
        }

        if (!$this->introConfig) {
            return;
        }

        $introConfig = Json::encode($this->introConfig['introjsOptions'], JSON_PRETTY_PRINT);
        $js = <<<EOD
jQuery(function () {
    window.intro = introJs();
    window.intro.setOptions($introConfig);
    window.intro.start();
});
EOD;

        $view = $this->getView();
        $view->registerJs($js);
    }
}
