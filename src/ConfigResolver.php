<?php

namespace Vibius\ConfigResolver;
use Exception;

class ConfigResolver{

    /**
     * @var string Holds path to config folder.
     */
    public $configPath = 'app/';

    /**
     * @var array List of all configs, in form of array.
     */
    public $configList = [];

    function __construct($configPath = false){
        if( $configPath ){
            $this->configPath = $configPath;
        }
        $this->path = vibius_BASEPATH.$this->configPath;
    }    

    /**
     * @param string $name Name of the config to be added.
     * @param string $src Path to the config.
     */
    public function addConfig($name, $src = ''){
        $config = $this->path.$name.'.php';
        if( !empty($src) ){
            $config = $src.$name.'.php';
        }
        if( !file_exists($config) || !is_readable($config)){
            throw new Exception("Config file does not exist or is not readable ($config)");
        }

        $this->configList[$name] = require $config;
    }

    public function getParameter($param, $config){
        if( !isset($this->configList[$config]) ){
            throw new Exception("Config does not exist in config list ($config)");
        }

        if( !isset($this->configList[$config][$param]) ){
            throw new Exception("Config parameter does not exist ($param)");
        }

        return $this->configList[$config][$param];

    }

}