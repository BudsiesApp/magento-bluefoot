<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_Category
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_Category extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{

    /**
     * Return category product collection
     * @return Mage_Eav_Model_Entity_Collection_Abstract | bool
     */
    public function getProductCollection()
    {
        $collection = false;
        if ($categoryId = $this->getEntity()->getData($this->getAttribute()->getData('attribute_code'))) {
            $collection = Mage::getModel('catalog/category')->load($categoryId)->getProductCollection()->addAttributeToSelect('*');
        }

        // Remove out of stock products if specified
        if ($collection && $this->getEntity()->getHideOutOfStock()) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }

        if( !$collection ) {
            return false;
        }

        $pageSize = ($this->getEntity()->getProductCount()) ? $this->getEntity()->getProductCount() : 4 ;

        // Set page size + only show visible products
        $collection
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setPageSize($pageSize);

        return $collection;
    }

    /**
     * Return an array of basic product data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {
        $return = array();

        // Add category name if it's present
        $categoryId = $this->getEntity()->getData($this->getAttribute()->getData('attribute_code'));
        if( $categoryId ) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $return['category'] = array("name" => $category->getName());
        }

        // Load products for the category
        $collection = $this->getProductCollection();
        if(!$collection) {
            return $return;
        }

        foreach($collection as $product) {
            $return['products'][] = array(
                'name' => $product->getName(),
                'sku' => $product->getSku(),
                'image' => $this->_getProductImage($product),
                'price' => Mage::helper('core')->currency($product->getFinalPrice(), true, false)
            );
        }

        return $return;
    }

    /**
     * Return the url of the product image
     *
     * @param $product
     * @return string
     */
    protected function _getProductImage($product)
    {

        try{
            $imgSrc = (string) Mage::helper('catalog/image')->init($product, 'small_image')->resize(200);
        }
        catch(Exception $e) {
            $imgSrc = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg',array('_area'=>'frontend'));
        }

        return $imgSrc;
    }


}