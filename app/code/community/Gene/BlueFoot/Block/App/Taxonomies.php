<?php
/**
 * Class Gene_BlueFoot_Block_App_Taxonomies
 * @author Hob Adams <hob@gene.co.uk>
 */
class Gene_BlueFoot_Block_App_Taxonomies extends Gene_BlueFoot_Block_App_View
{


    /**
     * @param bool|false $taxonomy
     * @return string
     */
    public function getHtml($taxonomy = false)
    {
        $html = '';
        if ($taxonomy) {
            $terms = $this->getParentTaxonomyTerms($taxonomy);
            if ($terms && count($terms)) {
                foreach($terms as $term) {
                    $html .= $this->_buildMenu($term);
                }
            }
        }
        return $html;
    }


    /**
     * Function to get the max depth of the taxonomy navigation
     * @return int|mixed
     */
    protected function _getDepth()
    {
        if (Mage::getStoreConfig('bluefoot_app/sidebar/taxonomy_depth')) {
            return Mage::getStoreConfig('bluefoot_app/sidebar/taxonomy_depth');
        } else {
            return 2;
        }
    }

    /**
     * Function to build up the taxonomy menu
     *
     * @param $term
     * @return string
     */
    protected function _buildMenu($term, $counter = 1)
    {
        $currentTerm = $this->_getCurrentTerm();
        $html = '';

        $html .= '<li class="' . $this->_getMenuClasses($term, $counter) . '">';
        // check if it is the active term;
        if ($currentTerm && ($term->getId() == $currentTerm->getId())) {
            $html .= '<span>' . $this->_getNestedItemPrefix($counter) . '<span class="bluefoot-taxonomy-menu-title">' . $term->getTitle() . '</span></span>';
        } else {
            $html .= '<a href="' . $this->getTermUrl($term) . '">' . $this->_getNestedItemPrefix($counter) . '<span class="bluefoot-taxonomy-menu-title">' . $term->getTitle() . '</span></a>';
        }
        if ($term->getChildCount()) {
            $counter++;
            // Only add menu items if they are within the max depth
            if ($counter <= $this->_getDepth()) {
                $html .= '<ol class="bluefoot-taxonomy-menu-' . $counter . '  bluefoot-child-menu">';
                foreach($this->getChildTerms($term) as $child) {
                    $html .= $this->_buildMenu($child, $counter);
                }
                $html .= '</ol>';
            }

        }

        $html .= '</li>';

        return $html;

    }


    /**
     * Function to set classes of taxonomy list items
     *
     * @param bool|false $term
     * @param int $counter
     * @return string
     */
    protected function _getMenuClasses($term = false, $counter = 1)
    {
        $classes = array();

        $classes[] = 'bluefoot-taxonomy-menu-item-' . $counter;
        if ($term && $term->getChildCount()) {
            $classes[] = 'parent';
        }

        $currentTerm = $this->_getCurrentTerm();
        if ($term && $currentTerm && ($term->getId() == $currentTerm->getId())) {
            $classes[] = 'current';
        }

        return $this->_renderMenuClasses($classes);

    }

    /**
     * @param $classes
     * @return string
     */
    protected function _renderMenuClasses($classes)
    {
        if (!empty($classes)) {
            return implode($classes, ' ');
        }
        return '';
    }

    /**
     * Function
     * @param int $counter
     * @return string
     */
    protected function _getNestedItemPrefix($counter = 1)
    {
        $html = '';
        if ($counter && $counter > 1) {
            $html .= '<span class="bluefoot-menu-item-prefix">';
            foreach(range(1, $counter -1) as $i) {
                $html .= '-';
            }
            $html .= '</span> ';
        }
        return $html;
    }

    /**
     * Get current taxonomy term
     * @return bool
     */
    protected function _getCurrentTerm()
    {
        $term = Mage::registry('current_genecms_term');
        if ($term && $term->getId()) {
            return $term;
        }
        return false;
    }
}