<?php

namespace Emmetog\Config;

use Emmetog\Cache\CacheInterface;

/**
 * The config class.
 */
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

    /**
     * A factory method to instantiate a new class.
     * 
     * In the future this could do more interesting things, like use a different
     * autoloader depending on certain circumstances, etc.
     * 
     * @param string $className The name of the class to instantiate.
     * 
     * @return $className An instance of the $className class.
     * @throws ConfigClassNotFoundException If the class could not be found.
     */
    public function getClass($className)
    {
        if (!class_exists($className, true))
        {
            throw new ConfigClassNotFoundException('The class "' . $className . '" was not found');
        }

        // We pass the $config object into the constructor if the class implements HasConfig.
        $newClass = (array_key_exists('HasConfig', class_implements($className))) ? new $className($this) : new $className();
        
        return $newClass;
    }

    /**
     * Gets a configuration from a config file.
     * 
     * A dot notation can be used to get the values of nested arrays, for example:
     * $result = $config->getConfiguration('file', 'some.nested.config');
     * 
     * with the config file containing:
     * 
     * $config['some']['nested']['config'] = 'hello world!';
     * 
     * will put the string 'hello world!' in the $result variable.
     * 
     * @param string $group The file to look in.
     * @param string $value The key of the array to get, array dot notation can be used (optional).
     * 
     * @return mixed The value of the configuration.
     * @throws ConfigFileNotFoundException If the config file was not found.
     * @throws ConfigValueNotFoundException If the config value was not found in the config file.
     */
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

    /**
     * Gets a configuration from an array.
     * 
     * Used internally to traverse nested config arrays.
     * 
     * @param string $value The key we are looking for.
     * @param array $configArray The array to search in.
     * 
     * @return mixed The value in the config.
     * @throws ConfigValueNotFoundException If no value was found in the array.
     */
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
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Gets the database configuration for a certain profile.
     * 
     * @param string $profile The database profile to get.
     * 
     * @return array The database configuration array.
     */
    public function getDatabaseConfig($profile)
    {
        return $this->getConfiguration('database', $profile);
    }

    /**
     * Gets the configuration directory; where the config files are stored.
     * 
     * @return string The full path to the configuration directory.
     */
    public function getConfigDirectory()
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
