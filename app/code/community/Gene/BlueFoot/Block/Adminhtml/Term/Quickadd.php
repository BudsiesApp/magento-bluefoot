<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Quickadd
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Quickadd extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_term';
        $this->_mode = 'quick';

        $this->setTemplate('gene/bluefoot/term/quickadd.phtml');

        parent::__construct();


        $this->_addButton('quickaddterm', array (
            'label' => 'Save and Continue',
            'onclick' => 'quickAddTerm(\'term_quick_add\')',
            'class' => 'save' ), -100);

        $this->_removeButton('save');
        $this->_removeButton('back');
        $this->_removeButton('reset');

        $this->_formScripts[] = "

        ";
    }

    public function getHeaderText()
    {
        if ($this->_getCurrentEntity() && $this->_getCurrentEntity()->getId()) {
            return 'Edit term: ' . $this->htmlEscape($this->_getCurrentEntity()->getTitle()) . '';
        }else {
            return 'New Term';
        }
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term|bool
     */
    protected function _getCurrentEntity()
    {
        if(Mage::registry('current_term')){
            return Mage::registry('current_term');
        }

        return false;
    }

    public function _getCurrentTaxonomy()
    {
        return Mage::registry('current_taxonomy');
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save', array('store' => $this->getRequest()->getParam('store')));
    }

    public function getHeaderCssClass()
    {
        return '';
    }

    public function getBackUrl()
    {
        $taxonomy = $this->_getCurrentTaxonomy();
        return $this->getUrl('*/*', array('taxonomy' => $taxonomy->getId()));
    }


}
