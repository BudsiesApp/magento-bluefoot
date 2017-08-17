<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_entity';
        $this->_headerText = 'Manage Content';
        $this->_addButtonLabel = 'Create Content';

        $typeFilter = Mage::registry('type_filter');
        if(is_object($typeFilter)){
            $this->_headerText = 'Manage ' . $typeFilter->getPluralName() . '';
            $this->_addButtonLabel = 'Create New ' . $typeFilter->getSingularName() . '';
        }

        $appId = Mage::registry("type_filter")->getContentApp()->getId();

        if ($appId) {

            if($this->getRequest()->getParam('goback')) {
                $this->_addButton('goback', array(
                    'label' => Mage::helper('gene_bluefoot')->__('Back to App'),
                    'onclick' => "setLocation('{$this->getUrl('*/genecms_setup_app/edit/', array('id' => $appId))}')"
                ));
            }
        }

        parent::__construct();
    }

    public function getCreateUrl()
    {
        $typeFilter = Mage::registry('type_filter');
        if(is_object($typeFilter)){
            return $this->getUrl('*/*/edit', array('type_id' => $typeFilter->getId()));
        }else{
            return $this->getUrl('*/*/new');
        }

    }
}