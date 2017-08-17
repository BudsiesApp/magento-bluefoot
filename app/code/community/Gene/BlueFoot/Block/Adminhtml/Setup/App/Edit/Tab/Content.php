<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Content
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Content extends Mage_Adminhtml_Block_Widget
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/app/configure/content-type.phtml');
    }

    /**
     * @return Gene_BlueFoot_Model_App
     */
    protected function _getCurrentApp()
    {
        return Mage::registry("current_app");
    }

    public function getInfoDataFields()
    {
        return array(
            //'type_id' => 'Internal ID',
            'name' => 'Name',
            'identifier' => 'Identifier',
            'singular_name' => 'Singular Name',
            'plural_name' => 'Plural Name',
//            'entity_url_prefix' => 'Content URL prefix',
        );
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Type_Collection
     */
    public function getContentTypes()
    {
        $app = $this->_getCurrentApp();
        $contentTypes = $app->getContentTypes();

        return $contentTypes;
    }

    public function getEditUrl($id)
    {
        return $this->getUrl('*/genecms_setup_content/edit/', array('id' => $id, 'goback' => 'toapp'));
    }

    public function getAddUrl($id)
    {
        return $this->getUrl('*/genecms_entity/new/', array('type_id' => $id, 'goback' => 'toapp'));
    }

    public function getViewUrl($id)
    {
        return $this->getUrl('/genecms_entity/index/', array('type_id' => $id, 'goback' => 'toapp'));
    }

    public function getCreateUrl()
    {
        return $this->getUrl('/genecms_setup_content/new/', array('app_id' => $this->_getCurrentApp()->getId(), 'goback' => 'toapp'));
    }
}
