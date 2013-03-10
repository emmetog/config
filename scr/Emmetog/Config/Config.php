<?php

namespace Emmetog\Config;

use Emmetog\Cache\CacheInterface;

class Config
{

    private $configs_loaded = array();
    private $application_root;

    /**
     * The cache object.
     * 
     * @var \Apl\Cache\CacheInterface
     */
    private $cache;

    public function __construct($application_root, CacheInterface $cache)
    {
        if (substr($application_root, strlen($application_root) - 1) != DIRECTORY_SEPARATOR)
        {
            $application_root .= DIRECTORY_SEPARATOR;
        }

        $this->application_root = $application_root;
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
     * @return \Apl\Cache\CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    public function setApplicationRoot($application_root)
    {
        $this->application_root = $application_root;
    }

    public function getApplicationRoot()
    {
        return $this->application_root;
    }

    public function getDatabaseConfig($profile)
    {
        return $this->getConfiguration('database', $profile);
    }

    protected function getConfigDirectory()
    {
        return $this->application_root . 'Config' . DIRECTORY_SEPARATOR;
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
