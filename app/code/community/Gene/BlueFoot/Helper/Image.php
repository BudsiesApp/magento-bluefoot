<?php

/**
 * Class Gene_BlueFoot_Helper_Image
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Helper_Image extends Gene_BlueFoot_Helper_Image_Abstract
{
    protected $_subdir = 'gene-bluefoot';

    protected $_config = array();

    protected $_placeholder = 'placeholder/placeholder.jpg';
    protected $_placeholderBaseDir = null;

    public function __construct()
    {
        $this->_placeholderBaseDir = Mage::getDesign()->getSkinUrl('images');
    }

    public function getImageConfig($identifier, $additionalParams = array())
    {
        $configModel = Mage::getModel('gene_bluefoot/image_config');
        return $configModel->getImageConfig($identifier, $additionalParams);
    }

    public function useConfig($identifier, $additionalParams = array())
    {
        $this->_config = $this->getImageConfig($identifier, $additionalParams);

        $config = new Varien_Object($this->_config);

        $width = $config->getWidth();
        $height = $config->getHeight();



        $adaptiveResize = $config->getAdaptiveResize();
        $keepFrame = $config->getKeepFrame();
        $keepAspectRatio = $config->getKeepAspectRatio();
        $keepTransparency = $config->getKeepTransparency();
        $backgroundColor = $config->getBackgroundColor();
        $constrainOnly = $config->getConstrainOnly();
        $quality = $config->getQuality();
        $placeholder = $config->getPlaceHolder();

        if($placeholder){
            $this->_placeholder = $placeholder;
        }
        if($quality){
            $this->quality($quality);
        }

        if(in_array($adaptiveResize, array(false, true, 'center', 'top', 'bottom'))){
            $this->adaptiveResize($adaptiveResize);
        }

        if($backgroundColor){
            $this->backgroundColor($backgroundColor);
        }

        $this->keepFrame($keepFrame);
        $this->keepAspectRatio($keepAspectRatio);
        $this->keepTransparency($keepTransparency);
        $this->constrainOnly($constrainOnly);

        if($width || $height){
            $this->resize($width, $height);
        }


        return $this;

    }


}