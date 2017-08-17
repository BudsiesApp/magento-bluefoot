<?php
/**
 * Class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Video
 *
 * @author Chloe Langford <chloe@gene.co.uk>
 */
class Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Video extends Gene_BlueFoot_Block_Entity_Pagebuilder_Block_Default
{


    /**
     * @return Gene_BlueFoot_Model_Entity|null
     */
    public function getEntity()
    {
        return $this->getData('entity');
    }


    /**
     * Get the video
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getVideo()
    {
        /* @var $dataModel Gene_BlueFoot_Model_Attribute_Data_Widget_Video */
        $dataModel = $this->getEntity()->getResource()->getAttribute('video_url')->getDataModel($this->getEntity());
        if ($dataModel instanceof Gene_BlueFoot_Model_Attribute_Data_Widget_Video && method_exists($dataModel, 'getVideo')) {
            return $dataModel->getVideo();
        }
        return false;
    }

    /**
     * Function to get video data as an object
     * @return bool|Varien_Object
     */
    public function getVideoData()
    {
        $video = $this->getVideo();
        if ($video) {

            list($url) = explode(',', $video);

            return new Varien_Object(array(
                'url' => Mage::helper('gene_bluefoot/video')->previewAction($url)
            ));
        }
        return false;
    }

    /**
     * Get the style information for the video block
     *
     * @return string
     */
    public function getIframeStyle()
    {
        $entity = $this->getEntity();
        $styles = array();
        if ($height = $entity->getVideoHeight()) {
            $styles[] = 'height: ' . $height;
        }
        if ($width = $entity->getVideoWidth()) {
            $styles[] = 'width: ' . $width;
        }

        if (!empty($styles)) {
            return implode(';', $styles);
        }
    }
}