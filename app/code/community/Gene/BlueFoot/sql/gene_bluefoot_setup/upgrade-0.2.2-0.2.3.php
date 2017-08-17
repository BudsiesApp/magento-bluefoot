<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

$appTable = $installer->getTable('gene_bluefoot/content_app');

if(!$installer->getConnection()->tableColumnExists($appTable, 'internal_description')){
    $installer->getConnection()
        ->addColumn($appTable,
            'internal_description',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'nullable' => true,
                'default' => '',
                'comment' => 'Internal Description'
            )
        );

}
$installer->endSetup();