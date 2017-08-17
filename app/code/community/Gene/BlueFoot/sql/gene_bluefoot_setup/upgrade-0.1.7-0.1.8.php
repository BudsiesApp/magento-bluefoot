<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

//first check if attribute exists
$attr = Mage::getResourceModel('gene_bluefoot/eav_attribute')
    ->loadByCode(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'image');

if(!$attr->getId()){
    $installer->addAttribute(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, 'image', array(
        'type'                       => 'text',
        'label'                      => 'Image',
        'required'                   => false,
        'input'                      => 'image',
        'backend'                    => 'gene_bluefoot/attribute_backend_image',
        'sort_order'                 => 50,
        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'user_defined'               => 0,
        'group'                      => ''
    ));

    $attributeSetId = Mage::getModel('gene_bluefoot/taxonomy_term')->getDefaultAttributeSet()->getId();
    $installer->addAttributeToSet(Gene_BlueFoot_Model_Taxonomy_Term::ENTITY, $attributeSetId, 'General', 'image', 50);
}


$installer->endSetup();