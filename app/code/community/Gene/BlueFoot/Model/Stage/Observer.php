<?php

/**
 * Class Gene_BlueFoot_Model_Stage_Observer
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Observer
{
    /**
     * The instance of the new auto loader
     *
     * @var Gene_BlueFoot_Model_Stage_Observer
     */
    static protected $_instance = false;

    /**
     * Singleton pattern implementation
     *
     * @return Varien_Autoload
     */
    static public function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Varien_Autoload();
        }
        return self::$_instance;
    }

    /**
     * Register a new auto loader to allow us to override classes not loaded through the factory
     *
     * @return $this
     */
    public function registerAutoLoader()
    {
        // Check the version
        Mage::helper('gene_bluefoot/version')->checkVersion();

        // We're going to add in an auto loader between classes
        spl_autoload_unregister(array(Varien_Autoload::instance(), 'autoload'));

        // Use ours instead
        spl_autoload_register(array('Gene_BlueFoot_Model_Stage_Observer', 'autoload'));

        return $this;
    }

    /**
     * Over ride the Element_Editor class with our own modified version
     *
     * @param $className
     *
     * @return mixed
     */
    public function autoload($className)
    {
        /* When the system makes a request to Varien_Data_Form_Element_Editor, actually load the file for the class
        Gene_BlueFoot_Model_Stage_Form_Element_Editor, which actually contains an overridden version of
        Varien_Data_Form_Element_Editor */
        if($className == 'Varien_Data_Form_Element_Editor') {
            $className = 'Gene_BlueFoot_Model_Stage_Form_Element_Editor';
        }

        return self::instance()->autoload($className);
    }

    /**
     * Event called on all pre-dispatches to save page builder content
     *
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function adminhtmlSaveStructure(Varien_Event_Observer $observer)
    {
        // Grab the page builders from the request
        if($structures = Mage::app()->getRequest()->getPost('gene-bluefoot')) {

            // Call our builder class to save the pages
            Mage::getSingleton('gene_bluefoot/stage_save')->saveStructures($structures);

            // Remove the gene-bluefoot data from the post request
            Mage::app()->getRequest()->setPost('gene-bluefoot', false);
        }

        return $this;
    }

    /**
     * Render the front-end of the page builder contents
     *
     * @param \Varien_Event_Observer $observer
     *
     * @return $this
     */
    public function renderFrontend(Varien_Event_Observer $observer)
    {
        // Grab out the front controller and the response
        /* @var $frontController Mage_Core_Controller_Varien_Front */
        $frontController = $observer->getEvent()->getFront();

        /* @var $response Mage_Core_Controller_Response_Http */
        $response = $frontController->getResponse();

        // Check the response is an instance of HTTP
        if($response instanceof Mage_Core_Controller_Response_Http) {

            // Retrieve the HTML
            $html = $response->getBody();

            // Check we have some HTML's
            if (!empty($html)) {

                $render = Mage::getSingleton('gene_bluefoot/stage_render');

                // Do a very quick strpos to see if the html contains page builder mark up
                if (strpos($html, Gene_BlueFoot_Model_Stage_Save::GENE_CMS_STRING) !== false) {

                    // Render the page
                    $renderedPage = $render->render($html);

                    // Set the body
                    if ($renderedPage) {
                        $response->setBody($renderedPage);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Add CSS and JS assets from pagebuilder.xml to the head
     *
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function toHtmlBefore(Varien_Event_Observer $observer)
    {
        //Get the block
        $block = $observer->getEvent()->getBlock();

        /**
         * @var Gene_BlueFoot_Model_Stage_Render $render
         * @var Gene_BlueFoot_Helper_Config $config
         */
        $render = Mage::getSingleton('gene_bluefoot/stage_render');
        $config = Mage::helper('gene_bluefoot/config');

        //In the case of the head block, loop through the assets and add them in
        if ($block && $block instanceof Mage_Page_Block_Html_Head) {
            $templates = $config->getConfig('content_blocks/templates');

            // Iterate through all templates
            foreach($templates as $template) {
                foreach($template->asArray() as $item) {
                    // If the template has extra assets include them within the head
                    if (isset($item['assets'])) {
                        if ($assets = $item['assets']) {
                            $render->addAssets($assets, $block);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
