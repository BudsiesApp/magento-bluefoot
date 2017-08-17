<?php

/**
 * Class Gene_BlueFoot_AppController
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_AppController extends Mage_Core_Controller_Front_Action
{
    const HANDLE_PREFIX = 'bluefoot_';


    protected $_currentApp;
    protected $_currentTerm;


    protected $_customLayoutHandles = array();

    /**
     * @return Gene_BlueFoot_Model_App|bool
     */
    protected function _getCurrentApp()
    {
        if(!$this->_currentApp) {

            $appId = $this->getRequest()->get('id');

            if ($appId) {
                $app = Mage::getModel('gene_bluefoot/app')->setStoreId(Mage::app()->getStore()->getId())->load($appId);
                if ($app && $app->getId()) {
                    $this->_currentApp = $app;
                }
            } elseif ($term = $this->_getCurrentTerm()) {
                $this->_currentTerm = $term;
                $taxonomy = $term->getTaxonomy();
                if ($taxonomy && $app = $taxonomy->getContentApp()) {
                    $this->_currentApp = $app;
                }
            }
        }

        return $this->_currentApp;
    }

    /**
     * @return Gene_BlueFoot_Model_Taxonomy_Term|bool
     */
    protected function _getCurrentTerm()
    {
        if(!$this->_currentTerm) {
            if ($termId = $this->getRequest()->get('term_id')) {
                $term = Mage::getModel('gene_bluefoot/taxonomy_term')->setStoreId(Mage::app()->getStore()->getId())->load($termId);
                if ($term && $term->getId()) {
                    $this->_currentTerm = $term;
                }
            }
        }

        return $this->_currentTerm;
    }

    protected function _initTermLayout(Gene_BlueFoot_Model_Taxonomy_Term $term)
    {
        $helper = Mage::helper('gene_bluefoot/view_term');
        $helper->initTermLayout($term, $this);

        return $this;
    }

    public function viewTermAction()
    {
        $currentApp = $this->_getCurrentApp();
        $currentTerm = $this->_getCurrentTerm();
        if(!$currentApp || !$currentTerm){
            $this->norouteAction();
            return;
        }

        if($currentTerm->hasIsActive() && !$currentTerm->getIsActive()){
            $this->norouteAction();
            return;
        }

        Mage::register('current_genecms_app', $currentApp);
        Mage::register('current_genecms_term', $currentTerm);
        Mage::register('bluefoot_page_type', 'term_view');

        $this->_initTermLayout($currentTerm);

        return $this->renderLayout();
    }

    public function viewAction()
    {
        $currentApp = $this->_getCurrentApp();
        if(!$currentApp){
            $this->norouteAction();
            return;
        }

        if($currentApp->hasData('is_active') && !$currentApp->getIsActive() && !$this->getRequest()->getParam('preview')){
            $this->norouteAction();
            return;
        }

        Mage::register('current_genecms_app', $currentApp);
        Mage::register('bluefoot_page_type', 'app_view');


        $handles = array(
            self::HANDLE_PREFIX . 'app_view',
            self::HANDLE_PREFIX . 'app_view_app_' . $currentApp->getUrlKey(),
            self::HANDLE_PREFIX . 'app_view_' . $currentApp->getId(),
            self::HANDLE_PREFIX . 'app_view_MODE_' . $currentApp->getDisplayMode(),
            self::HANDLE_PREFIX . 'app_view_' . $currentApp->getUrlKey(),
        );

        $this->addCustomLayoutHandle($handles);
        $this->loadLayout();
        //$this->loadLayoutUpdates();

        $this->generateLayoutXml()->generateLayoutBlocks();

        if ($pageLayout = $currentApp->getPageLayout()) {
            $this->getLayout()->helper('page/layout')->applyHandle($pageLayout);
            $this->getLayout()->helper('page/layout')
                ->applyTemplate($pageLayout);
        }

        return $this->renderLayout();
    }

    /**
     * @param string $handle
     * @return $this
     */
    public function addCustomLayoutHandle($handles)
    {
        if(!is_array($handles)){
            $handles = array($handles);
        }

        foreach($handles as $handle){
            $this->_customLayoutHandles[] = $handle;
        }

        return $this;
    }

    /**
     * @see Mage_Core_Controller_Front_Action
     * @return $this
     */
    public function addActionLayoutHandles()
    {
        parent::addActionLayoutHandles();
        if(count($this->_customLayoutHandles)){
            foreach($this->_customLayoutHandles as $updateHandle){
                $update = $this->getLayout()->getUpdate();
                $update->addHandle($updateHandle);
            }
        }
        return $this;
    }
}