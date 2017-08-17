<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = "adminhtml_term";
        $this->_blockGroup = "gene_bluefoot";
        $currentTaxonomy = Mage::registry('current_taxonomy');
        if($currentTaxonomy && $currentTaxonomy->getId()){
            $this->_headerText = Mage::helper("gene_bluefoot")->__("Manage Taxonomy Terms - " . $currentTaxonomy->getTitle());
        }else{
            $this->_headerText = Mage::helper("gene_bluefoot")->__("Manage Taxonomy Terms");
        }

        $this->_addButtonLabel = Mage::helper("gene_bluefoot")->__("Add Term");

        $this->_addButton('back', array(
            'label'     => Mage::helper("gene_bluefoot")->__("Back"),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));

        $appId = Mage::registry("current_taxonomy")->getContentApp()->getId();

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

    public function getBackUrl()
    {
        $currentTaxonomy = Mage::registry('current_taxonomy');
        return $this->getUrl('*/genecms_setup_taxonomy/index', array('taxonomy' => $currentTaxonomy->getId()));
    }

    public function getCreateUrl()
    {
        $currentTaxonomy = Mage::registry('current_taxonomy');
        return $this->getUrl('*/*/new', array('taxonomy' => $currentTaxonomy->getId()));
    }

}