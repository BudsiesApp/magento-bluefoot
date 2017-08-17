<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Data_Widget_Video
 *
 * @author Chloe Langford <chloe@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Data_Widget_Video extends Gene_BlueFoot_Model_Attribute_Data_Abstract
    implements Gene_BlueFoot_Model_Attribute_Data_Interface
{


    /**
     * Return Video data from the given field
     *
     * @return mixed
     */
    public function getVideo()
    {
        return $this->getEntity()->getData($this->getAttribute()->getData('attribute_code'));
    }

    /**
     * Return an array of basic video data used by the page builder
     *
     * @return array
     */
    public function asJson()
    {
        $url = Mage::helper('gene_bluefoot/video')->previewAction($this->getVideo());

        return array(
            'url' => $url
        );
    }

}