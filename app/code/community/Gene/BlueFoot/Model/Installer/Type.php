<?php

/**
 * Class Gene_BlueFoot_Model_Installer_Type
 *
 * @author Mark Wallman <mark@gene.co.uk>
 *
 */
class Gene_BlueFoot_Model_Installer_Type extends Gene_BlueFoot_Model_Installer_App
{
    public function validateContentType($identifier, $typeDataRaw)
    {
        if($this->contentTypeExists($identifier)){
            $this->_validationErrors[$identifier][] = 'Content type with this identifier already exists';
        }

        $attributeData = (isset($attributeData['attributes']) && is_array($typeDataRaw['attribute_data'])) ? $typeDataRaw['attribute_data'] : false;
        $attributes = (isset($attributeData['attributes']) && is_array($attributeData['attributes'])) ? $attributeData['attributes'] : false;

        if(!$attributes){
            $this->_validationErrors[$identifier][] = 'There are no attributes associated with this content type in the installer';
        }

    }

}