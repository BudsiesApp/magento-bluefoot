<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Entity_Edit
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Entity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'gene_bluefoot';
        $this->_controller = 'adminhtml_entity';

        parent::__construct();

        if($this->_getCurrentEntity() && $this->_getCurrentEntity()->getContentType()){
            $this->_updateButton('save', 'label','Save ' . $this->_getCurrentEntity()->getContentType()->getSingularName());
            $this->_updateButton('delete', 'label', 'Delete ' . $this->_getCurrentEntity()->getContentType()->getSingularName());
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

        $appId = Mage::registry("entity")->getContentApp()->getId();

        if ($appId) {

            if($this->getRequest()->getParam('goback')) {
                $this->_addButton('goback', array(
                    'label' => Mage::helper('gene_bluefoot')->__('Back to App'),
                    'onclick' => "setLocation('{$this->getUrl('*/genecms_setup_app/edit/', array('id' => $appId))}')"
                ));
            }
        }
    }

    public function getHeaderText()
    {
        if ($this->_getCurrentEntity() && $this->_getCurrentEntity()->getId()) {
            return 'Edit ' . $this->htmlEscape(Mage::registry('entity')->getTitle()) . ' Content';
        }else {
            return 'New Content';
        }
    }

    /**
     * @return Gene_BlueFoot_Model_Entity|bool
     */
    protected function _getCurrentEntity()
    {
        if(Mage::registry('entity')){
            return Mage::registry('entity');
        }

        return false;
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
        if($typeId = $this->getRequest()->getParam('type_id')){
            return $this->getUrl('*/*/', array('type_id' => $typeId));
        }

        $currentEntity = $this->_getCurrentEntity();
        if($currentEntity){
            if($typeId = $currentEntity->getContentType()->getId()){
                return $this->getUrl('*/*/', array('type_id' => $typeId));
            }
        }
        return $this->getUrl('*/*/');
    }


}
