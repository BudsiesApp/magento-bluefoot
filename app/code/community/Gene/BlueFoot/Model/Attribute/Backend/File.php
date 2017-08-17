<?php

/**
 * Class Gene_BlueFoot_Model_Attribute_Backend_File
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Attribute_Backend_File extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{

    /**
     * @param Varien_Object $object
     */
    public function afterSave($object)
    {

        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        $currentEntity = Mage::registry('current_entity_object');
        //we're only interested in saving this if editing the entity directly and not page builder
        if($currentEntity){
            if($currentEntity->getId() != $object->getId()){
                return $this;
            }
        }else{
            return $this;
        }

        $attibuteSetId = $object->getAttributeSetId();
        $contentType = Mage::getModel('gene_bluefoot/type')->loadByAttributeSetId($attibuteSetId);

        $contentTypeIdentifier = $contentType->getIdentifier();

        $path = Mage::helper('gene_bluefoot/config')->getUploadDir() . DS;

        try {
            $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
            //$uploader->setAllowedExtensions(array('pdf', 'jpeg', 'gif', 'png'));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($path);

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                Mage::logException($e);
            }
        }

        return $this;
    }
}
