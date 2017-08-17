<?php

/**
 * Class Gene_BlueFoot_Helper_Config
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Config extends Mage_Core_Helper_Abstract
{
    //const CONFIG_PATH = 'global/gene_cms';
    const CONFIG_BLOCK_PATH = 'content_blocks';
    const CONFIG_CONTENT_APP_PATH = 'content_apps';
    const CONFIG_INSTALLER_PATH = 'installer';
    const CONFIG_FRONTEND_PATH = 'frontend';

    protected $_configInstance;

    /**
     * Return the upload directory
     *
     * @return string
     */
    public function getUploadDir()
    {
        return Mage::getBaseDir('media') . DS . 'gene-bluefoot';
    }

    /**
     * Return the upload URL for uploaded content
     *
     * @param $removeHttp
     *
     * @return string
     */
    public function getUploadUrl($removeHttp = false)
    {
        $uploadUrl = Mage::getBaseUrl('media') . 'gene-bluefoot';
        if($removeHttp) {
            $uploadUrl = str_replace('http:', '', $uploadUrl);
        }
        return $uploadUrl;
    }

    /**
     * @return Gene_BlueFoot_Model_Config
     */
    public function getConfigInstance()
    {
        if(is_null($this->_configInstance)){
            $this->_configInstance = Mage::getSingleton('gene_bluefoot/config');
        }
        return $this->_configInstance;
    }

    /**
     * @param null $configNode
     * @return Varien_Simplexml_Element
     */
    public function getConfig($configNode = null)
    {
        $cmsConfig = $this->getConfigInstance()->getNode($configNode);
        return  $cmsConfig;
    }

    /**
     * @param null $configNode
     * @return Varien_Simplexml_Element
     */
    public function getBlockConfig($configNode = null)
    {
        $configPath = self::CONFIG_BLOCK_PATH;
        if($configNode){
            $configPath .= '/' . $configNode;
        }

        return $this->getConfig($configPath);
    }

    /**
     * @param null $configNode
     * @return Varien_Simplexml_Element
     */
    public function getContentAppConfig($configNode = null)
    {
        $configPath = self::CONFIG_CONTENT_APP_PATH;
        if($configNode){
            $configPath .= '/' . $configNode;
        }

        return $this->getConfig($configPath);
    }

    /**
     * @param null $configNode
     * @return Varien_Simplexml_Element
     */
    public function getInstallerConfig($configNode = null)
    {
        $configPath = self::CONFIG_INSTALLER_PATH;
        if($configNode){
            $configPath .= '/' . $configNode;
        }

        return $this->getConfig($configPath);
    }

    /**
     * @param null $configNode
     * @return Varien_Simplexml_Element
     */
    public function getFrontendConfig($configNode = null)
    {
        $configPath = self::CONFIG_FRONTEND_PATH;
        if($configNode){
            $configPath .= '/' . $configNode;
        }

        return $this->getConfig($configPath);
    }

}