<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Taxonomy
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Edit_Tab_Taxonomy extends Mage_Adminhtml_Block_Widget
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('gene/bluefoot/setup/app/configure/taxonomy.phtml');
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
            //'taxonomy_id' => 'Internal ID',
            'title' => 'Title',
            'type' => 'Type',
            'term_url_prefix' => 'Term URL prefix',
        );
    }

    /**
     * @return Gene_BlueFoot_Model_Resource_Taxonomy_Collection
     */
    public function getTaxonomies()
    {
        $app = $this->_getCurrentApp();
        $taxonomies = $app->getTaxonomies();
        return $taxonomies;
    }

    public function getEditUrl($id)
    {
        return $this->getUrl('*/genecms_setup_taxonomy/edit/', array('id' => $id, 'goback' => 'toapp'));
    }

    public function getAddUrl($id)
    {
        return $this->getUrl('*/genecms_taxonomyterm/new/', array('taxonomy' => $id, 'goback' => 'toapp'));
    }

    public function getViewUrl($id)
    {
        return $this->getUrl('*/genecms_taxonomyterm/index/taxonomy/', array('taxonomy' => $id, 'goback' => 'toapp'));
    }

    public function getCreateUrl()
    {
        $appId = $this->_getCurrentApp()->getId();
        return $this->getUrl('*/genecms_setup_taxonomy/edit/', array('app_id' => $appId));
    }
}
