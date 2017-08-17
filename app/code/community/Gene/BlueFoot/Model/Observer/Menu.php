<?php

/**
 * Class Gene_BlueFoot_Model_Observer_Admin
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Observer_Menu extends Varien_Event_Observer
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function addDynamicMenuItems(Varien_Event_Observer $observer)
    {
        try {
            $menu = $observer->getMenu();

            if (!$menu instanceof Varien_Data_Tree_Node) {
                return;
            }
            /**
             * @var $menu Varien_Data_Tree_Node
             */

            $tree = $menu->getTree();

            $appCollection = Mage::getModel('gene_bluefoot/app')->getCollection();

                foreach ($appCollection as $app) {

                    $node = new Varien_Data_Tree_Node(array(
                        'name' => $app->getTitle(),
                        'id' => 'bluefoot_app_' . $app->getId(),
                        'url' => $app->getAppUrl(), // point somewhere
                    ), 'id', $tree, $menu);

                    if ($app->getMenuPosition() == 'start') {
                        //add to beginning
                        $this->_prependNode($node, $menu);
                    } elseif($app->getMenuPosition() == 'last') {
                        //add to end
                        $menu->addChild($node);
                    }else{
                        //do nothing
                    }

                }
        }catch (Exception $e){
            Mage::logException($e);
            //we don't want to break frontend from rendering
        }

        return;
    }

    /**
     * @param $node
     * @param $menu
     */
    protected function _prependNode($node, $menu)
    {
        //add our item to the menu
        $menu->addChild($node);
        $nodeId = $node->getId();
        $addNodes = array();
        foreach($menu->getChildren()->getNodes() as $childNode)
        {
            if ($childNode->getId() != $nodeId){
                $addNodes[] = $childNode;
                //remove other child items
                $menu->getChildren()->delete($childNode);
            }
        }

        //re-add deleted child items after out item
        foreach($addNodes as $r){
            $menu->getChildren()->add($r);
        }
    }
}