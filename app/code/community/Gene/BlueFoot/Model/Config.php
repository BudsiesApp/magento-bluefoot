<?php

/**
 * Class Gene_BlueFoot_Model_Config
 * Custom class for handling custom config files
 *
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Config extends Mage_Core_Model_Config_Base
{
    /**
     * Key name for storage of cache data
     *
     * @var string
     */
    const CACHE_KEY_NAME = 'GENECMS_CONFIG';

    /**
     * Tag name for cache type, used in mass cache cleaning
     *
     * @var string
     */
    const CACHE_TAG_NAME = 'GENECMS_CONFIG';

    /**
     * Filename that will be collected from different modules
     *
     * @var string
     */
    const MAIN_CONFIGURATION_FILE = 'bluefoot/bluefoot.xml';
    const PAGEBUILDER_CONFIGURATION_FILE = 'bluefoot/pagebuilder.xml';

    /**
     * Initial configuration file template, then merged in one file
     *
     * @var string
     */
    const CONFIGURATION_TEMPLATE = '<?xml version="1.0"?><config></config>';

    /**
     * Load Gene CMS config from any module
     * @param null $sourceData
     */
    public function __construct($sourceData = null)
    {
        $tags = array(self::CACHE_TAG_NAME);
        $useCache = Mage::app()->useCache(self::CACHE_TAG_NAME);
        $this->setCacheId(self::CACHE_KEY_NAME);
        $this->setCacheTags($tags);

        if ($useCache && ($cache = Mage::app()->loadCache(self::CACHE_KEY_NAME))) {
            parent::__construct($cache);
        } else {
            parent::__construct(self::CONFIGURATION_TEMPLATE);

            // Load the main configuration file
            Mage::getConfig()->loadModulesConfiguration(self::MAIN_CONFIGURATION_FILE, $this);

            // Load the page builder specific file
            Mage::getConfig()->loadModulesConfiguration(self::PAGEBUILDER_CONFIGURATION_FILE, $this);

            // Store the configuration in the cache if enabled
            if ($useCache) {
                $xmlString = $this->getXmlString();
                Mage::app()->saveCache($xmlString, self::CACHE_KEY_NAME, $tags);
            }
        }
    }
}