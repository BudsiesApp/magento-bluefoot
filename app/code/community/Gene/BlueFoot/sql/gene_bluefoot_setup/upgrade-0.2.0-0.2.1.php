<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

// Fix the entity type
$entity = Mage::getModel('eav/entity_type')->loadByCode('gene_cms_entity');
if ($entity && $entity->getId()) {
    $entity->addData(array(
        'entity_type_code' => 'gene_bluefoot_entity',
        'entity_model' => 'gene_bluefoot/entity',
        'attribute_model' => 'gene_bluefoot/resource_eav_attribute',
        'entity_table' => 'gene_bluefoot/entity',
        'additional_attribute_table' => 'gene_bluefoot/eav_attribute',
        'entity_attribute_collection' => 'gene_bluefoot/attribute_collection'
    ));
    $entity->save();
}

// Fix the taxonomy terms
$taxonomy = Mage::getModel('eav/entity_type')->loadByCode('gene_cms_taxonomy_term');
if ($taxonomy && $taxonomy->getId()) {
    $taxonomy->addData(array(
        'entity_type_code' => 'gene_bluefoot_taxonomy_term',
        'entity_model' => 'gene_bluefoot/taxonomy_term',
        'attribute_model' => 'gene_bluefoot/resource_eav_attribute',
        'entity_table' => 'gene_bluefoot/taxonomy_term',
        'additional_attribute_table' => 'gene_bluefoot/eav_attribute',
        'entity_attribute_collection' => 'gene_bluefoot/taxonomy_term_attribute_collection'
    ));
    $taxonomy->save();
}

// Resolve any source / back-end models on attributes
$attributes = Mage::getResourceModel('gene_bluefoot/attribute_collection')
    ->addFieldToSelect('*');

if ($attributes->getSize()) {
    // Resolve attributes back-end and source models
    foreach ($attributes as $attribute) {
        $attribute->setBackendModel(str_replace('gene_cms/', 'gene_bluefoot/', $attribute->getBackendModel()));
        $attribute->setSourceModel(str_replace('gene_cms/', 'gene_bluefoot/', $attribute->getSourceModel()));
        $attribute->save();
    }
}

// Resolve any source / back-end models on taxonomy terms
$taxonomyTerms = Mage::getResourceModel('gene_bluefoot/taxonomy_term_attribute_collection')
    ->addFieldToSelect('*');

if ($taxonomyTerms->getSize()) {
    // Resolve attributes back-end and source models
    foreach ($taxonomyTerms as $term) {
        $term->setBackendModel(str_replace('gene_cms/', 'gene_bluefoot/', $term->getBackendModel()));
        $term->setSourceModel(str_replace('gene_cms/', 'gene_bluefoot/', $term->getSourceModel()));
        $term->save();
    }
}

$installer->endSetup();