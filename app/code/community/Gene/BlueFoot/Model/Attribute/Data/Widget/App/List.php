<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_App_List
 *
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_App_List extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{

    /**
     * Return app collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract | bool
     */
    public function getAppEntityCollection()
    {
        $collection = false;

        if ($collectionData = Mage::helper('core')->jsonDecode($this->getEntity()->getData($this->getAttribute()->getData('attribute_code')))) {

            $appId = (isset($collectionData['app_id'])) ? $collectionData['app_id'] : 0 ;
            $termIds = (isset($collectionData['term_ids'])) ? $collectionData['term_ids'] : array() ;
            $pageSize = ($this->getEntity()->getAppEntityCount()) ? $this->getEntity()->getAppEntityCount() : 3 ;

            if ($appId != 0) {
                $collection = Mage::getModel('gene_bluefoot/app')->load($appId)->getAllEntities()->addAttributeToSelect('*');
                // Filter by term Ids (0 is all)
                if (is_array($termIds) && !in_array('0', $termIds)) {
                    //$collection->filterByTerms($termIds);
                }
                $collection->setPageSize($pageSize);
            }
        }

        return $collection;
    }

    /**
     * Return an array of basic entity data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {

        $return = array();


        // Load products for the category
        $collection = $this->getAppEntityCollection();
        if(!$collection) {
            return $return;
        }

        foreach($collection as $appEntity) {
            $return['entities'][] = array(
                'title' => $appEntity->getTitle(),
                'excerpt' => $appEntity->getExcerpt(),
                'image' => $this->_getEntityImage($appEntity),
                'date' => $this->_getPublishedDate($appEntity)

            );
        }

        return $return;
    }

    /**
     * Return the url of the entity image
     *
     * @return string
     */
    protected function _getEntityImage($appEntity)
    {

        try{
            $imgSrc = (string) Mage::helper('gene_bluefoot/image')->init($appEntity->getFeaturedImage())->useConfig('thumbnail');
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
    protected function _getPublishedDate($appEntity)
    {
        if ($date = $appEntity->getPublishedDate()) {
            return Mage::helper('gene_bluefoot')->__('Published %s', Mage::helper('gene_bluefoot/date')->getFriendlyDateTime(strtotime($date)));
        }
        return '';
    }



}