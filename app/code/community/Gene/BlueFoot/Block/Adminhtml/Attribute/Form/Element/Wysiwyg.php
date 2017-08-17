<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Wysiwyg
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Attribute_Form_Element_Wysiwyg extends Varien_Data_Form_Element_Editor
{

    /**
     * Check whether Wysiwyg is enabled or not
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getIsWysiwygEnabled();
    }

    public function getConfig($key = null)
    {
        if ( !($this->_getData('config') instanceof Varien_Object) ) {
            $config['add_variables']         = false;
            $config['add_widgets']           = false;
            $config['add_directives']        = true;
            $config['use_container']         = true;
            $config['container_class']       = 'hor-scroll';
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig($config);
            $this->setConfig($config);
        }
        return parent::getConfig($key);
    }

    /**
     * Check whether wysiwyg enabled or not
     *
     * @return boolean
     */
    public function getIsWysiwygEnabled()
    {
        if($this->getEntityAttribute()){
            return $this->getEntityAttribute()->getIsWysiwygEnabled();
        }

        return false;
    }
}

