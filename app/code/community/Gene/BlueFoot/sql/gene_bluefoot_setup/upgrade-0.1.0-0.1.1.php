<?php
$installer = $this;


/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

if(!$installer->getConnection()->tableColumnExists($installer->getTable('gene_bluefoot/type'), 'show_in_page_builder')){
    $installer->getConnection()
        ->addColumn($installer->getTable('gene_bluefoot/type'),
            'show_in_page_builder',
            array(
                'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable' => true,
                'default' => '1',
                'length' => 1,
                'comment' => 'Show in Page Builder'
            )
        );
}

$installer->endSetup();