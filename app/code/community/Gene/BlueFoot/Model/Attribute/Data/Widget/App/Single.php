<?php
/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_App_Single
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_App_Single extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{

    /**
     * Return an entity from the given field
     *
     * @return mixed
     */
    public function getAppEntity()
    {
        return Mage::getModel('gene_bluefoot/entity')->load($this->getEntity()->getData($this->getAttribute()->getData('attribute_code')));
    }

    /**
     * Return an array of basic entity data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {

        $appEntity = $this->getAppEntity();

        return array(
            'title' => $appEntity->getTitle(),
            'image' => $this->_getEntityImage(),
            'excerpt' => $appEntity->getExcerpt(),
            'date' => $this->_getPublishedDate()
        );
    }


    /**
     * Return the url of the entity image
     *
     * @return string
     */
    protected function _getEntityImage()
    {

        try{
            $imgSrc = (string) Mage::helper('gene_bluefoot/image')->init($this->getAppEntity()->getFeaturedImage())->useConfig('thumbnail');
        }
        catch(Exception $e) {
            $imgSrc = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg',array('_area'=>'frontend'));
        }

        return $imgSrc;
    }

    /**
     * Return a friendly published date
     * @return string
     */
    protected function _getPublishedDate()
    {
        if ($date = $this->getAppEntity()->getPublishedDate()) {
            return Mage::helper('gene_bluefoot')->__('Published %s', Mage::helper('gene_bluefoot/date')->getFriendlyDateTime(strtotime($date)));
        }
        return '';
    }

}