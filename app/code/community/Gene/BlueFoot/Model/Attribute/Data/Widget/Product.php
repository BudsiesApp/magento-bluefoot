<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_Product
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_Product extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{

    /**
     * Return a product from the given field
     *
     * @return mixed
     */
    public function getProduct()
    {
        return Mage::getModel('catalog/product')->load($this->getEntity()->getData($this->getAttribute()->getData('attribute_code')));
    }

    /**
     * Return an array of basic product data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {
        $product = $this->getProduct();

        return array(
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'image' => $this->_getProductImage(),
            'price' => Mage::helper('core')->currency($product->getFinalPrice(), true, false)
        );
    }

    /**
     * Return the url of the product image
     *
     * @return string
     */
    protected function _getProductImage()
    {

        try{
            $imgSrc = (string) Mage::helper('catalog/image')->init($this->getProduct(), 'small_image')->resize(200);
        }
        catch(Exception $e) {
            $imgSrc = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg',array('_area'=>'frontend'));
        }

        return $imgSrc;
    }


}