<?php

/**
 * Class Gene_BlueFoot_Model_Observer_Admin
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Observer_Admin extends Varien_Event_Observer
{
    /**
     * Dynamically create admin menu structure for apps
     *
     * @param Varien_Event_Observer $observer
     * @return bool|void
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        if (!($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu)) {
            return;
        }

        //make sure module is enabled
        if(!Mage::helper('core')->isModuleEnabled('Gene_BlueFoot')){
            return;
        }

        $appSortOrder = 72;

        try {
            $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();

            //load the template xml structure from our config
            $appMenuTemplate = Mage::getConfig()->getNode('adminhtml/menutemplate/gene_bluefoot_app/_code_');
            $contentTypeMenuTemplate = Mage::getConfig()->getNode('adminhtml/menutemplate/gene_bluefoot_app_type/_code_');
            $taxonomyMenuTemplate = Mage::getConfig()->getNode('adminhtml/menutemplate/gene_bluefoot_app_taxonomy/_code_');

            //make sure we have loaded the templates correctly
            if(!$appMenuTemplate || !$contentTypeMenuTemplate || !$taxonomyMenuTemplate){
                return;
            }


            $menu = $config->getNode('menu');
            /* @var $target Varien_Simplexml_Element */

            $target = $menu;

            if (!$target) {
                //we cannot find a menu so don't try and add our links
                return false;
            }

            //Expose template xml
            $appTemplateXml = $appMenuTemplate->asXml();
            $typeTemplateXml = $contentTypeMenuTemplate->asXml();
            $taxonomyTemplateXml = $taxonomyMenuTemplate->asXml();


            //menu item for each content APP
            $apps = Mage::getModel('gene_bluefoot/app')->getCollection();
            foreach($apps as $app){

                $typeXml = '';
                $taxonomyXml = '';

                $typeSortOrder = 10;

                $appIdentifier = 'genecms_app_'.$app->getId();

                $appXml = str_replace(
                    array('_code_', '_appname_', '_appid_', '_sort_order_'),
                    array($appIdentifier, ucfirst($app->getTitle()), $app->getId(), $appSortOrder),
                    $appTemplateXml
                );
                $appChild = new Varien_Simplexml_Element($appXml);
                $appChildren = new Varien_Simplexml_Element('<children/>');


                //Add each content type
                $contentTypeIds = $app->getContentTypeIds();
                if(is_array($contentTypeIds) && count($contentTypeIds)){
                    $typeXml = '';
                    $contentTypes = Mage::getModel('gene_bluefoot/type')->getCollection();
                    $contentTypes->addContentTypeFilter('content');
                    $contentTypes->addFieldToFilter('type_id', array('in' => $contentTypeIds));
                    foreach($contentTypes as $contentType){
                        $typeXml = str_replace(
                            array('_code_', '_type_name_', '_plural_name_', '_singular_name_', '_typeid_', '_sort_order_'),
                            array($contentType->getIdentifier(), $contentType->getName(), ucfirst($contentType->getPluralName()), ucfirst($contentType->getSingularName()), $contentType->getId(), $typeSortOrder),
                            $typeTemplateXml
                        );

                        $typeChild = new Varien_Simplexml_Element($typeXml);
                        $appChildren->appendChild($typeChild);

                        $typeSortOrder++;
                    }
                }



                $taxonomySortOrder = 10;

                //Add Each taxonomy
                $taxonomyIds = $app->getTaxonomyIds();
                if(is_array($taxonomyIds) && count($taxonomyIds)){
                    $taxonomies = Mage::getModel('gene_bluefoot/taxonomy')->getCollection();
                    $taxonomies->addFieldToFilter('taxonomy_id', array('in' => $taxonomyIds));
                    if(count($taxonomies)) {

                        $taxonomyParent = new Varien_Simplexml_Element('<taxonomies><title>Taxonomies</title><sort_order>'.($typeSortOrder + 10).'</sort_order><children/></taxonomies>');


                        foreach ($taxonomies as $taxonomy) {
                            $taxonomyIdentifier = 'genecms_app_taxonomy_' . $taxonomy->getId();
                            $taxonomyXml = str_replace(
                                array('_code_', '_taxonomyname_', '_taxonomyid_', '_sort_order_'),
                                array($taxonomyIdentifier, $taxonomy->getTitle(), $taxonomy->getId(), $taxonomySortOrder),
                                $taxonomyTemplateXml
                            );

                            $taxonomyElement = new Varien_Simplexml_Element($taxonomyXml);

                            $taxonomyParent->children->appendChild($taxonomyElement);
                            //$taxonomyParent->appendChild($taxonomyElement);

                            $taxonomySortOrder++;
                        }

                        $appChildren->appendChild($taxonomyParent);
                    }
                }

                $appChild->extendChild($appChildren);

                $target->appendChild($appChild);

                $appSortOrder++;

            }

        }catch (Exception $e){
            //do nothing on exception as we don't want to break the admin menu
        }
    }
}