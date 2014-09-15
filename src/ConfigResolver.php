<?php

namespace Vibius\ConfigResolver;
use Exception, FileSystem;
use \Vibius\Container\Container as Container;

class ConfigResolver extends \Vibius\Container\Container{

    use \Vibius\Container\Methods;

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
        $this->path = $this->configPath;

        $this->container = Container::open('Config', true);
    }    

    /**
     * @param string $name Name of the config to be added.
     * @param string $src Path to the config.
     */
    public function add($name, $src = ''){
        $config = $this->path.$name.'.php';
        if( !empty($src) ){
            $config = $src.$name.'.php';
        }
        if( !FileSystem::has($config) || (FileSystem::getVisibility($config) !== 'public')){
            throw new Exception("Config file does not exist or is not readable ($config)");
        }

        $this->configList[$name] = require vibius_BASEPATH.$config;

        $config = require vibius_BASEPATH.$config;
        $c_{$name} = Container::open("Config.$name", true, true);
        foreach ($config as $key => $value) {
            $c_{$name}->add($key, $value);
        }

        $this->container->add("Config.$name" ,$c_{$name});

    }

    public function get($config){
        if( !$this->container->exists("Config.$config") ){
            throw new Exception("Config does not exist in config list ($config)");
        }
        return $this->container->get("Config.$config");

    }

}