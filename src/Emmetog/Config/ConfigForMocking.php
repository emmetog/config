<?php

namespace Emmetog\Config;

class ConfigForMocking extends \Emmetog\Config\Config
{

    /**
     * An array of the loaded (mocked) config values.
     * 
     * @var array
     */
    private $loadedConfigs = array();

    public function getDatabaseConfig($profile)
    {
        return $this->getConfiguration('database', 'test', true);
    }

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

    public function setConfiguration($group, $value)
    {
        $this->loadedConfigs[$group] = $value;
    }

}

class ConfigForMockingException extends \Exception
{
    
}

class ConfigForMockingConfigurationGroupNotMockedException extends ConfigForMockingException
{
    
}

class ConfigForMockingConfigurationValueNotMockedException extends ConfigForMockingException
{
    
}

?>
