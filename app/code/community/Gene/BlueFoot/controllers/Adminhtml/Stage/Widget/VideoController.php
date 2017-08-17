<?php

require_once(Mage::getModuleDir('controllers','Gene_BlueFoot') . DS . 'Adminhtml' . DS . 'StageController.php');

/**
 * Class Gene_BlueFoot_Adminhtml_Stage_Widget_VideoController
 *
 * @author Chloe Langford <chloe@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Stage_Widget_VideoController extends Gene_BlueFoot_Adminhtml_StageController
{

    /**
     * Grab the URL and detect if it is Vimeo or YouTube, then return correct URL
     * @return string
     */
    public function previewAction()
    {

        $url = $this->getRequest()->getParam('url');

        // Return URL
        $results = array(
            'key' => Mage::helper('gene_bluefoot/video')->previewAction($url)
        );

       return $this->returnJson($results);
    }

    /**
     * As this controller is used in multiple locations access is always required
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('page_builder');
    }
}