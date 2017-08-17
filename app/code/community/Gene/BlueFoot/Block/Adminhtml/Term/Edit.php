<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Term_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Term_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_term';

        parent::__construct();

        $appId = Mage::registry("current_taxonomy")->getContentApp()->getId();

        if ($appId) {

            if($this->getRequest()->getParam('goback')) {
                $this->_addButton('goback', array(
                    'label' => Mage::helper('gene_bluefoot')->__('Back to App'),
                    'onclick' => "setLocation('{$this->getUrl('*/genecms_setup_app/edit/', array('id' => $appId))}')"
                ));
            }
        }

        $this->_addButton('saveandcontinue', array (
            'label' => 'Save and Continue',
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save' ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
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
