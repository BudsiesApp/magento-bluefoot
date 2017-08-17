<?php

require_once(Mage::getModuleDir('controllers','Gene_BlueFoot') . DS . 'Adminhtml' . DS . 'StageController.php');

/**
 * Class Gene_BlueFoot_Adminhtml_Stage_Widget_UploadController
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_Stage_Widget_UploadController extends Gene_BlueFoot_Adminhtml_StageController
{
    /**
     * Upload action from the page builder system
     *
     * @return $this
     *
     * @todo investigate using a temporary directory and move into final directory on page save
     */
    public function uploadAction()
    {
        // Grab an instance of the file uploader
        $fileUploader = new Varien_File_Uploader('file');

        // Set our parameters
        $fileUploader->setFilesDispersion(true);
        $fileUploader->setAllowRenameFiles(true);
        $fileUploader->setAllowedExtensions(array('jpeg','jpg','png','bmp','gif','svg', 'mp3'));
        $fileUploader->setAllowCreateFolders(true);

        // Error handling at it's best
        try {

            // Attempt to save the file
            if ($uploaded = $fileUploader->save(Mage::helper('gene_bluefoot/config')->getUploadDir())) {

                // Return a success callback once the file has been uploaded
                return $this->returnJson(array('success' => true, 'file' => $uploaded['file']));

            } else {
                throw new Exception('An unknown error has occurred');
            }

        } catch (Exception $e) {
            return $this->returnJson(array('error' => 'Unable to upload file: ' . $e->getMessage()));
        }
    }

    /**
     * As this controller is used in multiple locations access is always required
     *
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('page_builder');
    }
}