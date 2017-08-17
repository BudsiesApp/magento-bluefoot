<?php

/**
 * Class Gene_BlueFoot_Model_App_Wizard_Step_Info
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App_Wizard_Step_Info extends Varien_Object
{
    protected $_errors = array();

    /**
     * @param array $data
     * @return bool
     */
    public function validate($data = array())
    {

        $dataObj = new Varien_Object($data);

        //check app with same name or url exists
        $apps = Mage::getModel('gene_bluefoot/app')->getCollection();
        $apps->addFieldToFilter(
            array('title', 'url_prefix'),
            array(
                array('like' => $dataObj->getTitle()),
                array('url_prefix', 'like' => $dataObj->getUrlPrefix())
            )
        );

        if($apps->getSize()){
            $this->_errors[] = 'An app with the same name or base url already exists.';
        }


        return !$this->hasErrors();
    }

    /**
     * @param array $requiredData
     * @param $data
     * @return bool
     */
    protected function checkRequiredData(array $requiredData, $data)
    {
        $errors = array();

        foreach($requiredData as $requiredKey){
            if(!isset($data[$requiredKey])){

            }elseif(isset($data[$requiredKey]) && empty($data[$requiredKey])){
                $errors = 'You must enter a value for field ' . $requiredKey;
            }
        }

        return (bool)count($errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool)count($this->getErrors());
    }
}