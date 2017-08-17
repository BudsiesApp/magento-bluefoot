<?php

/**
 * Class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Block_Adminhtml_Setup_App_Wizard extends Mage_Adminhtml_Block_Widget_Container
{
    protected $_formScripts = array();
    protected $_formInitScripts = array();

    protected $_headerText = 'App Wizard';

    protected $_defaultInfoBlock = 'adminhtml/template';
    protected $_defaultInfoTemplate = 'gene/bluefoot/setup/app/wizard/step-info.phtml';

    public function getCurrentStep()
    {
        return $this->getData('current_step');
    }

    public function __construct()
    {
        parent::__construct();

        if (!$this->hasData('template')) {
            $this->setTemplate('gene/bluefoot/setup/app/wizard.phtml');
        }

        $this->_addButton('back', array(
            'label'     => Mage::helper('adminhtml')->__('Back'),
            'onclick'   => 'appWizard.backStep(\''.$this->getBackUrl().'\');',
            'class'     => 'back',
        ), -1);

        $this->_addButton('reset', array(
            'label'     => Mage::helper('adminhtml')->__('Start Over'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*').'\')',
        ), -1);

        $this->_addButton('save', array(
            'label'     => Mage::helper('adminhtml')->__('Next Step'),
            'onclick'   => 'appWizard.saveStep();',
            'class'     => 'save',
        ), 1);
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/genecms_setup_appwizard/loadPreviousStep');
    }

    /**
     * @param Varien_Object $step
     * @return Mage_Core_Block_Abstract
     */
    public function getStepBlock(Varien_Object $step)
    {
        $blockName = $step->getBlock();
        $block = $this->getLayout()->createBlock($blockName);
        $block->setParentBlock($this);

        return $block;
    }

    public function getNewContentTypeUrl()
    {
        return $this->getUrl('adminhtml/genecms_setup_appwizard/addContentType');
    }

    public function getReloadStepUrl()
    {
        return $this->getUrl('adminhtml/genecms_setup_appwizard/reloadStep');
    }

    /**
     * @param Varien_Object $step
     * @return string
     */
    public function getStepBlockHtml(Varien_Object $step)
    {
        $block = $this->getStepBlock($step);
        return $block->toHtml();
    }

    /**
     * @param Varien_Object $step
     * @return string
     */
    public function getStepInfoBlockHtml(Varien_Object $step)
    {
        $block = $this->getStepInfoBlock($step);
        if($block){
            return $block->toHtml();
        }

        return '';
    }

    /**
     * @param Varien_Object $step
     * @return Mage_Core_Block_Abstract
     */
    public function getStepInfoBlock(Varien_Object $step)
    {

        if(!$blockName = $step->getInfoBlock()){
            $blockName = $this->_defaultInfoBlock;
        }

        if(!$blockTemplate = $step->getInfoTemplate()){
            $blockTemplate = $this->_defaultInfoTemplate;
        }



        $block = $this->getLayout()->createBlock($blockName);

        $block->setParentBlock($this);

        if($step->getUserInfo()){
            $block->setUserInfo($step->getUserInfo());
        }

        $block->setTemplate($blockTemplate);

        return $block;
    }

    public function getFormInitScripts()
    {
        if ( !empty($this->_formInitScripts) && is_array($this->_formInitScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formInitScripts) . '</script>';
        }
        return '';
    }

    public function getFormScripts()
    {
        if ( !empty($this->_formScripts) && is_array($this->_formScripts) ) {
            return '<script type="text/javascript">' . implode("\n", $this->_formScripts) . '</script>';
        }
        return '';
    }

    public function getHeaderWidth()
    {
        return '';
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-' . strtr($this->_controller, '_', '-');
    }

    public function getHeaderText()
    {
        return $this->_headerText . ' : ' . $this->getCurrentStep()->getTitle();
    }

    public function getHeaderHtml()
    {
        return '<h3 id="wizard-step-title" class="' . $this->getHeaderCssClass() . '">' . $this->getHeaderText() . '</h3>';
    }
}