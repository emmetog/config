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
trait HasConfig
{

    /**
     * The config object.
     *
     * @var Config
     */
    protected $config;

    /**
     * Sets the config object.
     * 
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        /**
         * If the object has an init() method then call it.  This gives the
         * object a chance to use the config object for initiation (the config
         * object is not available in the constructor).
         */
        if (method_exists($this, 'init'))
        {
            $this->init();
        }
    }

}

?>
