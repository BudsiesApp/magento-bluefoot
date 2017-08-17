<?php

/**
 * Class Gene_BlueFoot_Model_Stage_Source_Row_Template
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Source_Row_Template extends Mage_Core_Model_Abstract
{

    /**
     * Return the row template options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $config = Mage::helper('gene_bluefoot/config')->getConfig('row_templates');
        foreach ($config->asArray() as $template) {
            $options[] = array(
                'label' => $template['label'],
                'value' => $template['value']
            );
        }

        return $options;
    }
}