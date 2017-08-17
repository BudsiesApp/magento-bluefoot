<?php

/**
 * Class Gene_BlueFoot_Model_App_Viewoptions
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_App_Viewoptions extends Varien_Object
{
    /**
     * @param $serialisedData
     * @return $this
     */
    public function initSerializedData($serialisedData)
    {
        $data = @unserialize($serialisedData);
        if($data){
            $this->addData($data);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function serializeForSave()
    {
        $string = @serialize($this->getData());
        return $string;
    }
}