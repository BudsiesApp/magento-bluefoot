<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$appTable = $installer->getTable('gene_bluefoot/content_app');

if(!$installer->getConnection()->tableColumnExists($appTable, 'page_layout')){
    $installer->getConnection()
        ->addColumn($appTable,
            'page_layout',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'length' => 20,
                'comment' => 'page layout'
            )
        );
}


if(!$installer->getConnection()->tableColumnExists($appTable, 'display_mode')) {
    $installer->getConnection()
        ->addColumn($appTable,
            'display_mode',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'length' => 30,
                'comment' => 'display mode'
            )
        );
}

if(!$installer->getConnection()->tableColumnExists($appTable, 'view_options_serialized')) {
    $installer->getConnection()
        ->addColumn($appTable,
            'view_options_serialized',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'View Options serialized'
            )
        );
}

$installer->endSetup();