<?php

/**
 * Class Gene_BlueFoot_Model_App_Wizard_Session
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App_Wizard_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct($data = array())
    {
        $this->init('admin/app_wizard');

        $this->addData($data);
    }

    /**
     * @return array
     */
    public function getCompletedSteps()
    {
        if(!is_array($this->getData('completed_steps'))){
            $this->setData('completed_steps', array());
        }

        return $this->getData('completed_steps');
    }

    /**
     * @param $stepId
     * @param $data
     * @return $this
     */
    public function setStepData($stepId, $data)
    {
        $this->_data['step_data'][$stepId] = $data;
        return $this;
    }

    /**
     * @param $stepId
     * @return mixed
     */
    public function getStepData($stepId)
    {
        return isset($this->_data['step_data'][$stepId]) ? $this->_data['step_data'][$stepId] : false;
    }

}