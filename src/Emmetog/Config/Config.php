<?php

namespace Emmetog\Config;

use Emmetog\Cache\CacheInterface;

class Config
{

    /**
     * An array of the loaded config files.
     * 
     * @var array
     */
    private $configs_loaded = array();
    
    /**
     * The full path to the directory where the config files are to be loaded from.
     * 
     * @var string
     */
    private $config_directory;

    /**
     * The cache object.
     * 
     * @var \Emmetog\Cache\CacheInterface
     */
    private $cache;

    /**
     * Creates a new Config object.
     * 
     * @param string $config_directory The full path to the directory where the config files are to be loaded from.
     * @param \Emmetog\Cache\CacheInterface $cache
     */
    public function __construct($config_directory, CacheInterface $cache)
    {
        if (substr($config_directory, strlen($config_directory) - 1) != DIRECTORY_SEPARATOR)
        {
            $config_directory .= DIRECTORY_SEPARATOR;
        }

        $this->config_directory = $config_directory;
        
        $this->cache = $cache;
    }

    public function getClass($className)
    {
        if (!class_exists($className, true))
        {
            throw new ConfigClassNotFoundException('The class "' . $className . '" was not found');
        }

        return new $className($this);
    }

    public function getConfiguration($group, $value = '')
    {

        if (!isset($this->configs_loaded[$group]))
        {
            // The file is not loaded yet, try to load it.
            $configFile = $this->getConfigDirectory() . $group . '.config.php';
            if (!file_exists($configFile))
            {
                throw new ConfigFileNotFoundException('Config file not found: ' . $configFile);
            }
            $config = array();
            require $configFile;
            $this->configs_loaded[$group] = $config;
        }

        $box = $this->configs_loaded[$group];

        try
        {
            $box = $this->getConfigurationFromArray($value, $box);
        }
        catch (ConfigValueNotFoundException $e)
        {
            throw new ConfigValueNotFoundException('The value "' . $value . '" was not found in the config file "' . $group . '"');
        }

        return $box;
    }

    protected function getConfigurationFromArray($value, $configArray)
    {
        $originalValue = $value;
        // First split the $value by dots.
        $value = explode('.', $value);

        if (count($value) <= 1 && empty($value[0]))
        {
            return $configArray;
        }

        foreach ($value as $k => $v)
        {
            if (is_array($configArray) && isset($configArray[$v]))
            {
                $configArray = $configArray[$v];
                continue;
            }
            throw new ConfigValueNotFoundException();
        }

        return $configArray;
    }

    /**
     * Gets the cache object.
     * 
     * @return \Emmetog\Cache\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    public function getDatabaseConfig($profile)
    {
        return $this->getConfiguration('database', $profile);
    }

    protected function getConfigDirectory()
    {
        return $this->config_directory;
    }

}

class ConfigException extends \Exception
{
    
}

class ConfigClassNotFoundException extends ConfigException
{
    
}

class ConfigFileNotFoundException extends ConfigException
{
    
}

class ConfigValueNotFoundException extends ConfigException
{
    
}

class ConfigControllerNotFoundException extends ConfigException
{
    
}

class ConfigModelNotFoundException extends ConfigException
{
    
}

?>
