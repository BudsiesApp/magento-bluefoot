<?php
$installer = $this;

/**
 * @var $installer Gene_BlueFoot_Model_Resource_Setup
 */
$installer->startSetup();

if(!$installer->getConnection()->isTableExists($this->getTable('gene_bluefoot/type_group'))){
    /* Create our content block / type group table */
    $table = $installer->getConnection()
        ->newTable($installer->getTable('gene_bluefoot/type_group'))
        ->addColumn('group_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary'  => true,
        ), 'Rewrite Id')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Lowercase name for group')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => false,
        ), 'Name of group')
        ->addColumn('icon', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable' => true,
        ), 'Icon for group');

    $installer->getConnection()->createTable($table);
}

if(!$installer->getConnection()->tableColumnExists($installer->getTable('gene_bluefoot/type'), 'group_id')){
    /* Add the group ID column into the entity types */
    $installer->getConnection()
        ->addColumn($installer->getTable('gene_bluefoot/type'),
            'group_id',
            array(
                'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable' => true,
                'unsigned' => true,
                'comment'  => 'Group ID',
                'after'    => 'attribute_set_id'
            )
        );

    /* Create a foreign key between the content type and the groups */
    $installer->getConnection()->addForeignKey(
        $installer->getConnection()->getForeignKeyName($installer->getTable('gene_bluefoot/type'), 'group_id', $installer->getTable('gene_bluefoot/type_group'), 'group_id'),
        $installer->getTable('gene_bluefoot/type'),
        'group_id',
        $installer->getTable('gene_bluefoot/type_group'),
        'group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

    // The groups to be created
    $groups = array(
        'general' => array(
            'icon' => '<i class="fa fa-chevron-down"></i>',
            'name' => 'General'
        ),
        'media' => array(
            'icon' => '<i class="fa fa-chevron-down"></i>',
            'name' => 'Media'
        ),
        'commerce' => array(
            'icon' => '<i class="fa fa-chevron-down"></i>',
            'name' => 'Commerce'
        ),
        'other' => array(
            'icon' => '<i class="fa fa-chevron-down"></i>',
            'name' => 'Other'
        )
    );

    foreach ($groups as $code => $data) {
        // Create a general group
        $group = Mage::getModel('gene_bluefoot/type_group');
        $group->setData('code', $code);
        $group->addData($data);
        $group->save();
    }
}

$installer->endSetup();