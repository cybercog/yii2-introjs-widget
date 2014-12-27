<?php

namespace pvlg\introjs;

use Yii;
use yii\helpers\Json;
use yii\base\InvalidConfigException;

class Introjs extends \yii\base\Widget
{

    public $configFile = '@app/config/introjs.php';
    public $config;
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
    }

    public function run()
    {
        $js = '';
        $isStarted = false;
        
        foreach ($this->config['intros'] as $introName => $introConfig) {
            if (call_user_func($introConfig['condition'])) {
                if (isset($this->config['introjsOptions'])) {
                    $introjsOptions = array_merge($this->config['introjsOptions'], $introConfig['introjsOptions']);
                } else {
                    $introjsOptions = $introConfig['introjsOptions'];
                }

                $introjsOptions = Json::encode($introjsOptions, JSON_PRETTY_PRINT);

                $js.= "window.intro['$introName'] = introJs();\n";
                $js.= "window.intro['$introName'].setOptions($introjsOptions);\n";
                if (!isset($introConfig['startOnLoad']) || $introConfig['startOnLoad']) {
                    if ($isStarted) {
                        throw new InvalidConfigException("Double start introJs: {$introName}");
                    }
                    $js.= "window.intro['$introName'].start();\n";
                    $isStarted = true;
                }
            }
        }

        if ($js) {
            $view = $this->getView();

            IntrojsAsset::register($view);
            if ($this->cssFile) {
                $view->registerCssFile($this->cssFile);
            }

            $js = "window.intro = [];\n" . $js;

            $view->registerJs($js);
        }
    }
}
