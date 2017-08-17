<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Review
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard_Review extends Mage_Adminhtml_Block_Widget
{
    protected function _construct()
    {
        parent::_construct();

        $this->setActionUrl($this->getUrl("*/*/review", array("id" => $this->getRequest()->getParam("app_id"))));

        $this->setTemplate('gene/bluefoot/setup/app/wizard/review.phtml');
        $this->setDestElementId('edit_form');
        $this->setShowGlobalIcon(false);
    }

    public function getReviewSteps()
    {
        $wizardSession = Mage::getModel('gene_bluefoot/app_wizard_session');
        $wizardModel = Mage::getModel('gene_bluefoot/app_wizard');

        $contentTypeData = $wizardModel->getStepData('content_type_data');
        $taxonomyData = $wizardModel->getStepData('taxonomy_data');
        $appData = $wizardModel->getStepData('app_info');

        $this->setData('content_type', $contentTypeData);
        $this->setData('taxonomy', $taxonomyData);
        $this->setData('app_info', $appData);
    }

    public function getAppInfoUrl()
    {
        return $this->getUrl('*/*/loadStep/', array('step' => 'app_info'));
    }

    public function getContentTypeUrl()
    {
        return $this->getUrl('*/*/loadStep/', array('step' => 'content_types'));
    }

    public function getTaxonomyUrl()
    {
        return $this->getUrl('*/*/loadStep/', array('step' => 'taxonomy'));
    }
}