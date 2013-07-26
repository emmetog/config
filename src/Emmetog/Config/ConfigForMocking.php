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
     * Whether or not unmocked models are allowed in the test.
     *
     * @var boolean
     */
    private $isUnmockedClassesAllowed = false;
    
    /**
     * Whether or not real configs are allowed in the test.
     *
     * @var boolean
     */
    private $areRealConfigsAllowed = false;

    /**
     * An array of all the classes that have been mocked.
     *
     * @var array
     */
    private $mockedClasses = array();

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
    public function getConfiguration($group, $value = '')
    {
	if ($this->areRealConfigsAllowed)
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

    /**
     * Overwrites the parent getClass() method.
     * 
     * Throws an error if any unmocked model is used.
     * 
     * @param type $className
     */
    public function getClass($className)
    {
	// Check if the class has been mocked.
	if (array_key_exists($className, $this->mockedClasses))
	{
	    return $this->mockedClasses[$className];
	}

	if ($this->isUnmockedClassesAllowed())
	{
	    return parent::getClass($className);
	}

	throw new ConfigForMockingUnmockedClassRequestedException(
	    'The class "' . $className . '" was not mocked'
	);
    }
    
    /**
     * Sets a class to be used when getClass() is called.
     * 
     * @param string $className The name of the class to set.
     * @param object $object The object that should be returned.
     */
    public function setClass($className, $object)
    {
	$className = (string) $className;
	
	$this->mockedClasses[$className] = $object;
    }

    /**
     * Sets whether unmocked classes (real ones) are allowed or not.
     * 
     * If unmocked classes are not allowed then the UT will raise an error
     * if $config->getClass() is used to get a class that has not been
     * mocked in the UT.
     * 
     * The default is that unmocked classes are NOT allowed, meaning that
     * all the classes except the one under test must be mocked.
     * 
     * @param boolean $allowed True if it is ok for a UT to use classes that have not been mocked.
     */
    public function setUnmockedClassesAllowed($allowed = true)
    {
	$this->isUnmockedClassesAllowed = (bool) $allowed;
    }

    /**
     * Returns a boolean depending on if unmocked classes are allowed or not.
     * 
     * If unmocked classes are not allowed then the UT will raise an error
     * if $config->getClass() is used to get a class that has not been
     * mocked in the UT.
     * 
     * The default is that unmocked classes are NOT allowed, meaning that
     * all the classes except the one under test must be mocked.
     * 
     * @return boolean
     */
    public function isUnmockedClassesAllowed()
    {
	return (boolean) $this->isUnmockedClassesAllowed;
    }

    /**
     * Sets whether real config values are allowed in the unit tests or not.
     * 
     * If real configs are not allowed then the UT will raise an error
     * if $config->getConfiguration() is used to get a config that has not been
     * mocked in the UT.
     * 
     * The default is that real configs are NOT allowed, meaning that
     * all configs must be mocked.
     * 
     * @param boolean $allowed True if it is ok for a UT to use real config values.
     */
    public function setRealConfigsAllowed($allowed = true)
    {
	$this->areRealConfigsAllowed = (bool) $allowed;
    }

    /**
     * Returns a boolean depending on if real config values are allowed or not.
     * 
     * If real config values are not allowed then the UT will raise an error
     * if $config->getConfiguration() is used to get a config that has not been
     * mocked in the UT.
     * 
     * The default is that real config values are NOT allowed, meaning that
     * all the configs must be mocked.
     * 
     * @return boolean
     */
    public function areRealConfigsAllowed()
    {
	return (boolean) $this->areRealConfigsAllowed;
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

class ConfigForMockingUnmockedClassRequestedException extends ConfigForMockingException
{
    
}

?>
