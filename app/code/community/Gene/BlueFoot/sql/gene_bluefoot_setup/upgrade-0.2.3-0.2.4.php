<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$appTable = $installer->getTable('gene_bluefoot/content_app');

if(!$installer->getConnection()->tableColumnExists($appTable, 'meta_title')){
    $installer->getConnection()
        ->addColumn($appTable,
            'meta_title',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 255,
                'default' => '',
                'comment' => 'Meta Title'
            )
        );

}

if(!$installer->getConnection()->tableColumnExists($appTable, 'meta_keywords')){
    $installer->getConnection()
        ->addColumn($appTable,
            'meta_keywords',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Meta Keywords'
            )
        );

}

if(!$installer->getConnection()->tableColumnExists($appTable, 'meta_description')){
    $installer->getConnection()
        ->addColumn($appTable,
            'meta_description',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Meta Description'
            )
        );

}
$installer->endSetup();