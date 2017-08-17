<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$taxonomyTable = $installer->getTable('gene_bluefoot/taxonomy');

if(!$installer->getConnection()->tableColumnExists($taxonomyTable, 'term_defaults_serialized')){
    $installer->getConnection()
        ->addColumn($taxonomyTable,
            'term_defaults_serialized',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Term Defaults serialized'
            )
        );

}

$installer->endSetup();