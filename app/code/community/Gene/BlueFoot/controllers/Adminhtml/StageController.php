<?php

/**
 * Handle any requests from the stage for config etc.
 *
 * Class Gene_BlueFoot_Adminhtml_StageController
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Adminhtml_StageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return the full configuration needed for the stage
     *
     * @return $this
     */
    public function configAction()
    {
        $config = Mage::helper('gene_bluefoot')->getStageData();

        // Does the request have any entityIds present?
        if($entityIds = $this->getRequest()->getPost('entityIds')) {
            $config = array_merge($config, array(
                'entities' => Mage::helper('gene_bluefoot/stage')->getEntityConfig($entityIds)
            ));
        }

        return $this->returnJson($config);
    }

    /**
     * Use data models to update data
     *
     * @return \Gene_BlueFoot_Adminhtml_StageController
     */
    public function dataUpdateAction()
    {
        if ($this->getRequest()->getParam('code')
            && $this->getRequest()->getParam('data')
            && $this->getRequest()->getParam('fields'))
        {
            // Build the data model update
            $dataModelUpdates = Mage::getSingleton('gene_bluefoot/stage_data')->buildDataModelUpdate(
                $this->getRequest()->getParam('code'),
                $this->getRequest()->getParam('data'),
                $this->getRequest()->getParam('fields')
            );

            if($dataModelUpdates) {
                return $this->returnJson(array('success' => true, 'fields' => $dataModelUpdates));
            }
        }

        return $this->returnJson(array('success' => false));
    }

    /**
     * Allow users to create new templates
     *
     * @return \Gene_BlueFoot_Adminhtml_StageController
     * @throws \Exception
     */
    public function saveTemplateAction()
    {
        if ($this->getRequest()->getPost('structure')) {
            $postData = $this->getRequest()->getPost();
            $postData['has_data'] = ($this->getRequest()->getPost('has_data') == 'true' ? 1 : 0);
            //Save our image data to a png and store the url to the model
            if(isset($postData['preview']))
            {
                $imageData = Mage::helper('gene_bluefoot/data')->prepareImageData($postData['preview']);
                $templateDir = Mage::helper('gene_bluefoot/data')->getTemplatePreviewImageDirectoryName();
                //Create the path where the image should be saved
                $url = $templateDir.'/'.$postData['name'].'.png';
                //Really not sure that this should be running every time we try to save a template.
                if (!is_dir($templateDir))
                {
                    mkdir($templateDir,0777, true);
                }

                file_put_contents($url, $imageData);
                $postData['preview'] = Mage::helper('gene_bluefoot/data')->getTemplatePreviewImageUrl($postData['name']);
            }
            $template = Mage::getModel('gene_bluefoot/stage_template');
            $template->addData($postData);
            if ($template->save())
            {
                //Get our new template data to send back in the return
                $templateData[] = array(
                    'id' => $template->getId(),
                    'name' => $template->getData('name'),
                    'preview' => $template->getData('preview'),
                    'structure' => $template->getData('structure'),
                    'pinned' => (bool) $template->getData('pinned')
                );
                return $this->returnJson(array('success' => true,'template'=>$templateData));
            }
        }

        return $this->returnJson(array('success' => false));
    }

    /**
     * Allows users to delete templates.
     *
     * @return Gene_BlueFoot_Adminhtml_StageController
     */
    public function deleteTemplateAction()
    {
        $id = $this->getRequest()->getPost('id');
        if ($id)
        {
            try
            {
                $model = Mage::getModel('gene_bluefoot/stage_template');
                $model->setId($id);
                $model->delete();
                return $this->returnJson(array('success' => true));
            }
            catch (Exception $e)
            {
                $this->returnJson(array('success' => false,'exception'=>$e->getMessage()));
            }

        }
        return $this->returnJson(array('success' => false));
    }
    /**
     * Allow for templates to have a pin value set which is then used for ordering in : todo
     * @return Gene_BlueFoot_Adminhtml_StageController
     */
    public function pinTemplateAction()
    {
        $id = $this->getRequest()->getPost('id');
        $pinned = filter_var ($this->getRequest()->getPost('pinned'), FILTER_VALIDATE_BOOLEAN);

        if (isset($id) && isset($pinned))
        {
            try
            {
                $model = Mage::getModel('gene_bluefoot/stage_template')->load($id);
                $model->addData(array('pinned'=>$pinned));
                $model->save();
                return $this->returnJson(array('success' => true, 'id' => $id, 'pinned' => $pinned));
            }
            catch (Exception $e)
            {
                $this->returnJson(array('success' => false,'exception'=>$e->getMessage()));
            }

        }
        return $this->returnJson(array('success' => false));
    }

    /**
     * Return JSON to the browser
     *
     * @param           $array
     * @param bool|true $doCheck
     *
     * @return $this
     */
    public function returnJson($array, $doCheck = true) {

        // If we're trying to return JSON only allow over an Ajax request
        if(!$this->getRequest()->isAjax() && $doCheck) {
            $this->getResponse()->setBody('Direct action disallowed.');
            return $this;
        }

        // If the data is an array we'll need to encode it
        if(is_array($array)) {
            $array = Mage::helper('core')->jsonEncode($array);
        }

        // Set the content type
        $this->getResponse()->setHeader('Content-type', 'application/json');

        // Set the body of the return
        $this->getResponse()->setBody($array);

        // Return the controller model
        return $this;

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