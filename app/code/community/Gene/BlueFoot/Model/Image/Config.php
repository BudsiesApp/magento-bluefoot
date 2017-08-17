<?php

/**
 * Class Gene_BlueFoot_Model_Image_Config
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Image_Config extends Varien_Object
{
    const CONFIG_IMAGE_PROFILES_PATH = 'image_profiles';

    protected $_defaults = array(
        'adaptive_resize' => 'center',
        'keep_frame' => false,
        'keep_aspect_ratio' => true,
        'keep_transparency' => true,
        'constrain_only' => false,
    );

    protected $_configs = null;


    public function getImageConfig($identifier, $additionalParams = array())
    {
        $imageConfig = $this->_getConfig($identifier);

        if(is_array($additionalParams) && count($additionalParams)){
            $imageConfig = $additionalParams + $imageConfig;
        }

        return $imageConfig;
    }

    protected function _getConfig($identifier)
    {
        if(is_null($this->_configs)){
            $configs = array();
            $configHelper = Mage::helper('gene_bluefoot/config');
            $imageConfigs = $configHelper->getFrontendConfig(self::CONFIG_IMAGE_PROFILES_PATH);

            if(isset($imageConfigs->_default)){
                $this->_defaults = array_merge($this->_defaults, $imageConfigs->_default->asArray());
            }

            foreach($imageConfigs->children() as $imageConfig){

                if($imageConfig->getName() && $imageConfig->getName() != '_default'){
                    $conf = array_merge($this->_defaults, $imageConfig->asArray());
                    $configs[$imageConfig->getName()] = $conf;
                }

            }

            if(!isset($configs['default'])){
                $configs['default'] = $this->_defaults;
            }

            $this->_configs = $configs;
        }

        if(isset($this->_configs[$identifier])){
            return $this->_configs[$identifier];
        }

        return array();
    }
}