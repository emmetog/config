<?php

namespace Emmetog\Config;

use Emmetog\Config\Config;
use \Exception;

/**
 * A config class used in unit tests when we want to be able to simulate different
 * configuration setups.
 */
class ConfigForMocking extends Config
{

    /**
     * An array of the loaded (mocked) config values.
     * 
     * @var array
     */
    private $loadedConfigs = array();

    /**
     * Gets the database configuration for a certain profile.
     * 
     * @see Emmetog\Config\Config::getDatabaseConfig()
     * @param string $profile The database profile to get.
     * 
     * @return array The database configuration array.
     */
    public function getDatabaseConfig($profile)
    {
	return $this->getConfiguration('database', 'test', true);
    }

    /**
     * Gets a configuration from a config file.
     * 
     * @see Emmetog\Config\Config::getConfiguration()
     * 
     * @param string $group The file to look in.
     * @param string $value The key of the array to get, array dot notation can be used (optional).
     * @param boolean $useRealConfigValue Whether or not to look in the real config file (default: false).
     * 
     * @return mixed The value of the configuration.
     * @throws ConfigForMockingConfigurationGroupNotMockedException If the config file was not mocked.
     * @throws ConfigForMockingConfigurationValueNotMockedException If the config value was not mocked.
     */
    public function getConfiguration($group, $value = '', $useRealConfigValue = false)
    {
	if ($useRealConfigValue)
	{
	    return parent::getConfiguration($group, $value);
	}

	if (!array_key_exists($group, $this->loadedConfigs))
	{
	    throw new ConfigForMockingConfigurationGroupNotMockedException('The configuration group ' . $group . ' was not mocked');
	}

	try
	{
	    return $this->getConfigurationFromArray($value, $this->loadedConfigs[$group]);
	}
	catch (ConfigValueNotFoundException $e)
	{
	    throw new ConfigForMockingConfigurationValueNotMockedException('The configuration value "' . $value . '" in the group "' . $group . '" was not mocked');
	}
    }

    /**
     * Sets a configuration.
     * 
     * Used in unit tests to simulate a configuration setting.
     * 
     * @param string $group The configuration file.
     * @param array $value The configuration array to set.
     */
    public function setConfiguration($group, array $value)
    {
	$this->loadedConfigs[$group] = $value;
    }

}

class ConfigForMockingException extends Exception
{
    
}

class ConfigForMockingConfigurationGroupNotMockedException extends ConfigForMockingException
{
    
}

class ConfigForMockingConfigurationValueNotMockedException extends ConfigForMockingException
{
    
}

?>
