<?php

/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Column
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Column extends Gene_BlueFoot_Block_Entity_Pagebuilder_Structural_Abstract
{
    /**
     * Return the column width
     *
     * @return mixed|null
     */
    protected function _getWidth()
    {
        return $this->getFormData('width');
    }


    /**
     * Return the number of columns based on the width
     *
     * @param int $total
     * @return float
     */
    public function getColumnsFromWidth($total = 12)
    {

        // Work out the columns based on the total number of columns
        return round($this->_getWidth()*$total);

    }
}