<?php

/**
 * Class Gene_BlueFoot_Model_Stage_Widget_Source
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Gene_BlueFoot_Model_Stage_Widget_Source extends Mage_Core_Model_Abstract
{
    /**
     * Declare the default group for all widgets
     */
    const DEFAULT_GROUP = 'BlueFoot';

    /**
     * Return the available widgets
     *
     * @return array
     */
    public function toOptionArray()
    {
        $widgets = Mage::helper('gene_bluefoot/config')->getConfig('widgets');
        if ($widgets) {
            $groups = array();
            foreach($widgets->children() as $name => $config) {
                if ($config->attributes()->translate) {
                    $translateNodes = explode(' ', (string) $config->attributes()->translate);
                    foreach($translateNodes as $translate) {
                        if(isset($config->{$translate})) {
                            $config->{$translate} = Mage::helper('gene_bluefoot')->__((string) $config->{$translate});
                        }
                    }
                }

                $groups[(isset($config->group) ? (string) $config->group : self::DEFAULT_GROUP)][] = array(
                    'label' => $config->label,
                    'value' => $config->alias
                );
            }

            $optionArray = array(array(
                'label' => Mage::helper('gene_bluefoot')->__('None'),
                'value' => ''
            ));

            foreach($groups as $groupName => $values) {
                $optionArray[] = array(
                    'label' => $groupName,
                    'value' => $values
                );
            }

            return $optionArray;
        }

        return false;
    }

    /**
     * Return the widgets configuration
     *
     * @param $widget
     *
     * @return mixed
     */
    public function getWidgetConfig($widget)
    {
        return Mage::helper('gene_bluefoot/config')->getConfig('widgets/' . $widget);
    }

    /**
     * Return a widgets configuration from it's alias
     *
     * @param $alias
     *
     * @return bool
     */
    public function getWidgetConfigFromAlias($alias)
    {
        $widgets = Mage::helper('gene_bluefoot/config')->getConfig('widgets');
        if ($widgets) {
            foreach ($widgets->children() as $name => $config) {
                if ((string) $config->alias == $alias) {
                    return $config;
                }
            }
        }

        return false;
    }
}