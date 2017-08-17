<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$installer->createEntityTypeTable(
    'gene_bluefoot/type'
);

$installer->createEntityTables(
    'gene_bluefoot/entity'
);

$installer->createAttributeTable(
    'gene_bluefoot/eav_attribute'
);

$installer->createTaxonomyTable(
    'gene_bluefoot/taxonomy'
);

//create taxonomy term entity table and entity data tables
$installer->createTaxonomyTermTable(
    'gene_bluefoot/taxonomy_term'
);
$installer->createEntityTables(
    'gene_bluefoot/taxonomy_term',
    array(
        'no-main' => true
    )
);

$installer->installEntities();

$installer->createAppTable(
    'gene_bluefoot/content_app',
    'gene_bluefoot/content_app_content_type',
    'gene_bluefoot/content_app_taxonomy'
);

$installer->createInstallerTable(
    'gene_bluefoot/install'
);


$installer->endSetup();