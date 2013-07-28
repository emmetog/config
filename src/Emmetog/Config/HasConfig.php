<?php

namespace Emmetog\Config;

use Emmetog\Config\Config;

/**
 * An trait for objects which have an instance of a config.
 * 
 * If an object wants to create new objects it must have a config object,
 * or if it wants to use settings from the config files.
 *
 * @author emmet
 */
trait HasConfig {
    
    /**
     * The config object.
     *
     * @var Config
     */
    private $config;
    
    /**
     * Sets the config object.
     * 
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
    
}

?>
