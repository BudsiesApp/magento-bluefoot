<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_Attribute_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_setup_attribute';

        parent::__construct();

        $this->_updateButton('save', 'label','Save Attribute');
        $this->_updateButton('delete', 'label', 'Delete Attribute');

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
        if ($this->_getCurrentAttribute() && $this->_getCurrentAttribute()->getAttributeId()) {
            return 'Edit Attribute: ' . $this->htmlEscape(Mage::registry('gene_cms_attribute')->getName()) . '';
        }
        else {
            return 'New Content Attribute';
        }
    }

    protected function _getCurrentAttribute()
    {
        return Mage::registry('gene_cms_attribute');
    }

    public function getFormActionUrl()
    {
        return $this->getUrl('*/genecms_setup_attribute/save');
    }

    public function getHeaderCssClass()
    {
        return '';
    }


}
