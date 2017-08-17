<?php

/**
 * Class Gene_BlueFoot_Helper_Data
 *
 * @author Markius Wallmanius <markus@gene.co.uk>
 */ 
class Gene_BlueFoot_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_entityType;

    /**
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if(is_null($this->_entityType)){
            $this->_entityType = Mage::getSingleton('eav/config')->getEntityType('gene_bluefoot_entity');
        }

        return $this->_entityType;
    }

    /**
     * @return Gene_BlueFoot_Model_Stage_Data
     */
    public function getStageDataModel()
    {
        return Mage::getModel('gene_bluefoot/stage_data');
    }

    /**
     * @return array
     */
    public function getStageData()
    {
        return $this->getStageDataModel()->getAllDataAsArray();

    }

    /**
     * Function to get the url for CMS images.
     * @param bool $image
     * @return bool|string
     */
    public function getImageUrl($image = false)
    {
        if ($image) {
            return Mage::helper('gene_bluefoot/config')->getUploadUrl() . $image;
        }
        return false;
    }

    /**
     * Manipulate image data string to format ready for file_put_contents
     * @param $imageData
     * @return mixed|string
     */
    public function prepareImageData($imageData)
    {
        $imgData = str_replace('data:image/png;base64,', '', $imageData);
        $imgData = base64_decode($imgData);
        return $imgData;
    }

    /**
     * Get the url for the template preview image save location
     * @param $templateName
     * @return string
     */
    public function getTemplatePreviewImageDirectoryName()
    {
        return Mage::getBaseDir('media') . '/gene-bluefoot/template-thumbs';
    }

    /**
     * Get the url for the front end
     * @param $templateName
     * @return string
     */
    public function getTemplatePreviewImageUrl($templateName)
    {
        return Mage::getBaseUrl('media') . 'gene-bluefoot/template-thumbs/'.$templateName.'.png';
    }


}