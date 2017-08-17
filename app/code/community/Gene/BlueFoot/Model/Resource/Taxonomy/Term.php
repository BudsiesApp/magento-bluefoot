<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Taxonomy_Term
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Taxonomy_Term extends Gene_BlueFoot_Model_Resource_Abstract
{
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();

    protected function _construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY);
        $this->setConnection(
            $resource->getConnection('gene_bluefoot_read'),
            $resource->getConnection('gene_bluefoot_write')
        );
    }


    /**
     * Process category data after save category object
     * update path value
     *
     * @param Varien_Object $object
     * @return $this
     */
    protected function _afterSave(Varien_Object $object)
    {
        if (substr($object->getPath(), -1) == '/') {
            $object->setPath($object->getPath() . $object->getId());
            $this->_savePath($object);

        }elseif($object->getPath() == $object->getParentId()){
            if($object->getParentId()) {
                $object->setPath($object->getPath() . '/' . $object->getId());
            }else{
                $object->setPath($object->getId());
            }

            $this->_savePath($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * Update path field
     *
     * @param Gene_BlueFoot_Model_Taxonomy_Term $object
     * @return $this
     */
    protected function _savePath($object)
    {
        if ($object->getId()) {
            $this->_getWriteAdapter()->update(
                $this->getEntityTable(),
                array('path' => $object->getPath()),
                array('entity_id = ?' => $object->getId())
            );
        }
        return $this;
    }


    public function changeParent(Gene_BlueFoot_Model_Taxonomy_Term $term, Gene_BlueFoot_Model_Taxonomy_Term $newParent)
    {
        $oldPath = $term->getOrigData('path');
        $childrenCount  = $this->getChildrenCount($term->getId()) + 1;
        $table          = $this->getEntityTable();
        $adapter        = $this->_getWriteAdapter();
        $pathField      = $adapter->quoteIdentifier('path');

        /**
         * Decrease children count for all old term parent categories
         */
        if($childrenCount > 1) {
            $adapter->update(
                $table,
                array('children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)),
                array('entity_id IN(?)' => $term->getParentIds())
            );
        }

        /**
         * Increase children count for new term parents
         */
        $adapter->update(
            $table,
            array('children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)),
            array('entity_id IN(?)' => $newParent->getPathIds())
        );

        if($newParent->getId()){
            $newPath          = sprintf('%s/%s', $newParent->getPath(), $term->getId());
        }else{
            $newPath          = sprintf('%s',$term->getId());
        }


        /**
         * Update children nodes path
         */
        $adapter->update(
            $table,
            array(
                'path' => new Zend_Db_Expr('REPLACE(' . $pathField . ','.
                    $adapter->quote($oldPath . '/'). ', '.$adapter->quote($newPath . '/').')'
                )
            ),
            array($pathField . ' LIKE ?' => $oldPath . '/%')
        );


        /**
         * Update moved category data
         */
        $data = array(
            'path'      => $newPath,
            'parent_id' =>$newParent->getId() ? $newParent->getId() : 0
        );
        $adapter->update($table, $data, array('entity_id = ?' => $term->getId()));

        // Update category object to new data
        $term->addData($data);

        //die('fin');
        return $this;
    }


    /**
     * Get children term count
     *
     * @param int $termId
     * @return int
     */
    public function getChildrenCount($parentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'children_count')
            ->where('entity_id = :entity_id');
        $bind = array('entity_id' => $parentId);

        return $this->_getReadAdapter()->fetchOne($select, $bind);
    }

    public function calculateChildrenCount(Varien_Object $parent)
    {
        $parentPath = $parent->getPath();
        if($parentPath){
            $select = $this->_getReadAdapter()->select()
                ->from($this->getEntityTable(), 'COUNT(*)')
                ->where('path LIKE :parent_path');
            $bind = array('parent_path' => $parentPath . '/%');

            return $this->_getReadAdapter()->fetchOne($select, $bind);
        }

        return 0;
    }

    public function getChildIds($parentId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), 'entity_id')
            ->where('parent_id = :parent_id');
        $bind = array('parent_id' => $parentId);

        return $this->_getReadAdapter()->fetchCol($select, $bind);
    }

}
