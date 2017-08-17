<?php

/**
 * Class Gene_BlueFoot_Controller_Router
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * Initialize Controller Router
     *
     * @param Varien_Event_Observer $observer
     */
    public function initControllerRouters($observer)
    {
        /* @var $front Mage_Core_Controller_Varien_Front */
        $front = $observer->getEvent()->getFront();

        $front->addRouter('bluefoot', $this);
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param Zend_Controller_Request_Http $request
     * @return bool
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));

        Mage::dispatchEvent('bluefoot_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue()) {
            return false;
        }

        $app = Mage::getModel('gene_bluefoot/app');
        $appId = $app->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if ($appId) {
            $request->setModuleName('genecms')
                ->setControllerName('app')
                ->setActionName('view')
                ->setParam('id', $appId);

            $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                $identifier
            );

            return true;
        }

        $rewrite = Mage::getModel('gene_bluefoot/url_rewrite');
        $rewrite->setStoreId(Mage::app()->getStore()->getId());
        $rewrite->loadByRequestPath($identifier);

        if($rewrite->getId()){
            $request->setPathInfo($rewrite->getTargetPath());
            return true;
        }else{
            $urlParts = explode('/', $identifier);
            if(count($urlParts) > 1){
                $potentialAppUrl = $urlParts[0];
                //try taking away the first part of the url if it is an app
                $app = Mage::getModel('gene_bluefoot/app');
                $appId = $app->checkIdentifier($potentialAppUrl, Mage::app()->getStore()->getId());
                if($appId){
                    unset($urlParts[0]);
                    $retryIdentifier = implode('/', $urlParts);
                    $rewrite->loadByRequestPath($retryIdentifier);
                    if($rewrite->getId()){
                        $request->setPathInfo($rewrite->getTargetPath());
                        return true;
                    }
                }
            }


        }
        return false;
    }
}
