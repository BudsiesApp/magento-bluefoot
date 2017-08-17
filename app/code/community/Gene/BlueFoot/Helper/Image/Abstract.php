<?php

/**
 * Class Gene_BlueFoot_Helper_Image_Abstract
 * @author Mark Wallman <mark@gene.co.uk>
 */
abstract class Gene_BlueFoot_Helper_Image_Abstract extends Mage_Core_Helper_Data
{
    /**
     * path to image placeholder
     * override in child
     * @var string
     */
    protected $_placeholder     = '';

    protected $_usePlaceholder = false;

    protected $_placeholderBaseDir = '';

    /**
     * subdirectory to save imaves
     * override in child
     * @var string
     */
    protected $_subdir          = '';

    /**
     * image processor
     * @var null|Varien_Image_Adapter_Gd2
     */
    protected $_imageProcessor     = null;

    /**
     * image to process
     */
    protected $_image             = null;

    /**
     * error message
     * @var string
     */
    protected $_openError         = "";

    /**
     * keep image frame
     * @var bool
     */
    protected $_keepFrame       = false;

    /**
     * keep image aspect ratio
     * @var bool
     */
    protected $_keepAspectRatio = true;

    /**
     * constrain image
     * @var bool
     */
    protected $_constrainOnly   = true;

    /**
     * addaptive resize - crop
     * https://github.com/wearefarm/magento-adaptive-resize/blob/master/README.md
     * @var bool
     */
    protected $_adaptiveResize  = 'center'; // false|center|top|bottom

    /**
     * image width
     * @var mixed(null|int)
     */
    protected $_width           = null;

    /**
     * image height
     * @var mixed(null|int)
     */
    protected $_height          = null;

    /**
     * image resize needed
     * @var mixed (null|array)
     */
    protected $_scheduledResize = null;

    /**
     * image is resized
     * @var bool
     */
    protected $_resized         = false;

    /**
     * addaptive resize positions
     * https://github.com/wearefarm/magento-adaptive-resize/blob/master/README.md
     * @var array
     */
    protected $_adaptiveResizePositions = array(
        'center' => array(0.5,0.5),
        'top'    => array(1,0),
        'bottom' => array(0,1)
    );

    /**
     * resized image folder name
     * @var string
     */
    protected $_resizeFolderName = 'cache';

    public function __construct()
    {
        $this->_placeholderBaseDir = Mage::getDesign()->getSkinUrl('images');
    }

    /**
     * get the image base dir
     *
     * @access public
     * @return string

     */
    public function getImageBaseDir()
    {
        return Mage::getBaseDir('media') . DS . $this->_subdir;
    }

    /**
     * get the image url for object
     *
     * @access public
     * @return string

     */
    public function getImageBaseUrl()
    {
        return Mage::getBaseUrl('media').$this->_subdir;
    }

    /**
     * init image from varien object
     *
     * @access public
     * @param Varien_Object $object
     * @param string $imageField
     * @return $this

     */
    public function initFromObject(Varien_Object $object, $imageField = 'image')
    {
        $this->_imageProcessor = null;
        $this->_image = $object->getDataUsingMethod($imageField);
        if (!$this->_image) {
            $this->_image = '/'.$this->_placeholder;
        }
        $this->_width = null;
        $this->_height = null;
        $this->_scheduledResize = false;
        $this->_resized = false;
        $this->_adaptiveResize = 'center';

        try {
            if($this->_usePlaceholder){
                $this->_getImageProcessor()->open($this->_placeholderBaseDir.'/'.$this->_placeholder);
            }else{
                $this->_getImageProcessor()->open($this->getImageBaseDir().DS.$this->_image);
            }

        } catch (Exception $e) {
            $this->_openError = $e->getMessage();
            try {
                $this->_getImageProcessor()->open($this->_placeholderBaseDir.'/'.$this->_placeholder);
                $this->_image = '/'.$this->_placeholder;
            } catch(Exception $e) {
                $this->_openError .= "\n".$e->getMessage();
                $this->_image = null;
            }
        }
        return $this;
    }

    /**
     *
     * init image
     *
     * @param null $image
     * @param bool $absPath
     * @return $this
     */
    public function init($image = null, $absPath = false)
    {
        $this->_imageProcessor = null;
        $this->_image = $image;
        if (!$this->_image) {
            $this->_image = '/' . $this->_placeholder;
            $this->_usePlaceholder = true;
        }
        $this->_width = null;
        $this->_height = null;
        $this->_scheduledResize = false;
        $this->_resized = false;
        $this->_adaptiveResize = 'center';

        try {
            if($this->_usePlaceholder){
                $this->_getImageProcessor()->open($this->_placeholderBaseDir . '/' . $this->_placeholder);
            }else{
                $this->_getImageProcessor()->open($this->getImageBaseDir() . DS . $this->_image);
            }

        } catch (Exception $e) {
            $this->_openError = $e->getMessage();
            try {
                $this->_getImageProcessor()->open($this->_placeholderBaseDir.'/'.$this->_placeholder);
                $this->_image = '/'.$this->_placeholder;
            } catch(Exception $e) {
                $this->_openError .= "\n".$e->getMessage();
                $this->_image = null;
            }
        }
        return $this;
    }

    /**
     * get the image processor
     *
     * @access protected
     * @return Varien_Image_Adapter_Gd2

     */
    protected function _getImageProcessor()
    {
        if (is_null($this->_imageProcessor)) {
            $this->_imageProcessor = Varien_Image_Adapter::factory('GD2');
            $this->_imageProcessor->keepFrame($this->_keepFrame);
            $this->_imageProcessor->keepAspectRatio($this->_keepAspectRatio);
            $this->_imageProcessor->constrainOnly($this->_constrainOnly);
        }
        return $this->_imageProcessor;
    }

    /**
     * Get/set keepAspectRatio
     *
     * @access public
     * @param bool $value
     * @return mixed(bool|$this)

     */
    public function keepAspectRatio($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepAspectRatio($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepAspectRatio();
        }
    }

    /**
     * Get/set keepFrame
     *
     * @access public
     * @param bool $value
     * @return mixed(bool|$this)

     */
    public function keepFrame($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepFrame($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepFrame();
        }
    }

    /**
     * Get/set keepTransparency
     *
     * @access public
     * @param bool $value
     * @return mixed(bool|$this)

     */
    public function keepTransparency($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->keepTransparency($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->keepTransparency();
        }
    }

    /**
     * Get/set adaptiveResize
     *
     * @access public
     * @param bool|string $value
     * @return mixed(bool|$this)
     * https://github.com/wearefarm/magento-adaptive-resize/blob/master/README.md

     */
    public function adaptiveResize($value = null)
    {
        if (null !== $value) {
            $this->_adaptiveResize = $value;
            if ($value) {
                $this->keepFrame(false);
            }
            return $this;
        } else {
            return $this->_adaptiveResize;
        }
    }

    /**
     * Get/set constrainOnly
     *
     * @access public
     * @param bool $value
     * @return mixed(bool|$hit)

     */
    public function constrainOnly($value = null)
    {
       if (null !== $value) {
            $this->_getImageProcessor()->constrainOnly($value);
            return $this;
       } else {
            return $this->_getImageProcessor()->constrainOnly();
       }
    }

    /**
     * Get/set quality, values in percentage from 0 to 100
     *
     * @access public
     * @param int $value
     * @return mixed(bool|Gene_ExpertCms_Helper_Image_Abstract)

     */
    public function quality($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()->quality($value);
            return $this;
        } else {
            return $this->_getImageProcessor()->quality();
        }
    }

    /**
     * Get/set keepBackgroundColor
     *
     * @access public
     * @param array $value
     * @return mixed(bool|Gene_ExpertCms_Helper_Image_Abstract)

     */
    public function backgroundColor($value = null)
    {
        if (null !== $value) {
            $this->_getImageProcessor()-> backgroundColor($value);
            return $this;
        } else {
            return $this->_getImageProcessor()-> backgroundColor();
        }
    }

    /**
     * resize image
     *
     * @access public
     * @param int $width - defaults to null
     * @param int $height - defaults to null
     * @return Gene_ExpertCms_Helper_Image_Abstract

     */
    public function resize($width = null, $height = null)
    {
        $this->_scheduledResize = true;
        $this->_width  = $width;
        $this->_height = $height;
        return $this;
    }

    /**
     * get destination image prefix
     *
     * @access protected
     * @return Gene_ExpertCms_Helper_Image_Abstract

     */
    protected function _getDestinationImagePrefix()
    {
        if (!$this->_image) {
            return $this;
        }
        $imageRealPath = "";
        if ($this->_scheduledResize) {
            $width  = $this->_width;
            $height = $this->_height;
            $adaptive   = $this->adaptiveResize();
            $keepFrame  = $this->keepFrame();
            $keepAspectRatio= $this->keepAspectRatio();
            $constrainOnly  = $this->constrainOnly();
            $imageRealPath = $width.'x'.$height;
            $options = "";

            if (!$keepAspectRatio) {
                $imageRealPath .= '-exact';
            } else {
                if (!$keepFrame && $width && $height && ($adaptive !== false)) {
                    $adaptive = strtolower(trim($adaptive));
                    if (isset($this->_adaptiveResizePositions[$adaptive])) {
                        $imageRealPath .= '-'.$adaptive;
                    }
                }
            }
            if ($keepFrame) {
                $imageRealPath .= '-frame';
                $_backgroundColor = $this->backgroundColor();
                if ($_backgroundColor) {
                    $imageRealPath .= '-'.implode('-', $_backgroundColor);
                }
            }
            if (!$constrainOnly) {
                $imageRealPath .= '-zoom';
            }
        }
        return $imageRealPath;
    }

    /**
     * get image destination path
     *
     * @access protected
     * @return string

     */
    protected function _getDestinationPath()
    {
        if (!$this->_image) {
            return $this;
        }
        if ($this->_scheduledResize) {
            return str_replace('//', '/', $this->getImageBaseDir().DS.$this->_resizeFolderName.DS.$this->_getDestinationImagePrefix().DS.$this->_image);
        } else {
            return $this->getImageBaseDir().DS.$this->_image;
        }
    }

    /**
     * get image url
     *
     * @access protected
     * @return mixed (string|bool)

     */
    protected function _getImageUrl()
    {
        if (!$this->_image) {
            return false;
        }
        if ($this->_scheduledResize) {
            return  $this->getImageBaseUrl().str_replace('//', '/', '/'.$this->_resizeFolderName.'/'.$this->_getDestinationImagePrefix().'/'.$this->_image);
        } else {
           return rtrim('/', $this->getImageBaseUrl()) . '/' .$this->_image;
        }
    }

    /**
     * resize image
     *
     * @access protected
     * @return Gene_ExpertCms_Helper_Image_Abstract

     */
    protected function _doResize()
    {
        if (!$this->_image || !$this->_scheduledResize || $this->_resized) {
            return $this;
        }
        $this->_resized = true; //mark as resized
        $width = $this->_width;
        $height = $this->_height;
        $adaptive = $width && $height &&
                    $this->keepAspectRatio() && !$this->keepFrame() &&
                    ($this->adaptiveResize() !== false);
        $adaptivePosition = false;
        if ($adaptive) {
            $adaptive = strtolower(trim($this->adaptiveResize()));
            if (isset($this->_adaptiveResizePositions[$adaptive])) {
                $adaptivePosition = $this->_adaptiveResizePositions[$adaptive];
            }
        }
        $processor = $this->_getImageProcessor();

        if (!$adaptivePosition) {
            $processor->resize($width, $height);
            return $this;
        }
        //make adaptive resize
        //https://github.com/wearefarm/magento-adaptive-resize/blob/master/README.md
        $currentRatio = $processor->getOriginalWidth() / $processor->getOriginalHeight();
        $targetRatio  = $width / $height;
        if ($targetRatio > $currentRatio) {
            $processor->resize($width, null);
        } else {
            $processor->resize(null, $height);
        }
        $diffWidth  = $processor->getOriginalWidth() - $width;
        $diffHeight = $processor->getOriginalHeight() - $height;
        if ($diffWidth || $diffHeight) {
            $processor->crop(
                floor($diffHeight * $adaptivePosition[0]), //top rate
                floor($diffWidth / 2),
                ceil($diffWidth / 2),
                ceil($diffHeight *  $adaptivePosition[1]) //bottom rate
            );
        }
        return $this;
    }

    /**
     * to string - no need for cache expire because the image names will be different
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        try {
            if (!$this->_image) {
                throw new Exception($this->_openError);
            }

            $imageRealPath = $this->_getDestinationPath();

            if (!file_exists($imageRealPath)) {
                $this->_doResize();
                $this->_getImageProcessor()->save($imageRealPath);
            }
            return $this->_getImageUrl();
        } catch (Exception $e) {
            Mage::logException($e);
            return 'data:image/png;base64,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';
        }
    }
}
