<?php

/**
 * Class Gene_BlueFoot_Model_Resource_Setup
 * @author Mark Wallman <mark@gene.co.uk>
 */
class Gene_BlueFoot_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{

    /**
     * Add additional data values on top of those which already exist
     * @param array $attr
     * @return array
     */
    protected function _prepareValues($attr)
    {
        $data = parent::_prepareValues($attr);
        $data = array_merge($data, array(
            'frontend_input_renderer'       => $this->_getValue($attr, 'input_renderer'),
            'is_global'                     => $this->_getValue(
                $attr,
                'global',
                Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
            ),
            'is_visible'                    => $this->_getValue($attr, 'visible', 1),
            'is_wysiwyg_enabled'            => $this->_getValue($attr, 'wysiwyg_enabled', 0),
            'apply_to'                      => $this->_getValue($attr, 'apply_to'),
        ));
        return $data;
    }

    /**
     * Create default attributes
     * Used by parent method installEntities()
     * @return array
     */
    public function getDefaultEntities()
    {
        return array(
            'gene_bluefoot_entity' => array(
                'entity_model' => 'gene_bluefoot/entity',
                'table' => 'gene_bluefoot/entity',
                'attribute_model' => 'gene_bluefoot/resource_eav_attribute',
                'additional_attribute_table' => 'gene_bluefoot/eav_attribute',
                'entity_attribute_collection' => 'gene_bluefoot/attribute_collection',
                //'default_group' => 'General',
                'attributes' => array(
                    'title'               => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Title',
                        'input'                      => 'text',
                        'sort_order'                 => 1,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'note'                       => '',
                        'visible'                    => 1,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'identifier'               => array(
                        'type'                       => 'static',
                        'label'                      => 'Identifier',
                        'input'                      => 'text',
                        'sort_order'                 => 1,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'                   => false,
                        'note'                       => '',
                        'visible'                    => 2,
                    ),

                    'url_key'            => array(
                        'type'                       => 'varchar',
                        'label'                      => 'URL Key',
                        'input'                      => 'text',
                        'backend'                    => 'gene_bluefoot/attribute_backend_urlkey',
                        'required'                   => false,
                        'sort_order'                 => 3,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'is_active'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Is Active',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 4,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'published_date'         => array(
                        'type'                       => 'datetime',
                        'label'                      => 'Published Date',
                        'input'                      => 'date',
                        'backend'                    => 'eav/entity_attribute_backend_datetime',
                        'sort_order'                 => 5,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0
                    ),

                    'featured_image'         => array(
                        'type'                       => 'text',
                        'label'                      => 'Featured Image',
                        'required'                   => false,
                        'input'                      => 'image',
                        'backend'                    => 'gene_bluefoot/attribute_backend_image',
                        'sort_order'                 => 6,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'excerpt'            => array(
                        'type'                       => 'text',
                        'label'                      => 'Excerpt',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General',
                        'note'                      => 'Used on content listing'
                    ),

                    'main_content'            => array(
                        'type'                       => 'text',
                        'label'                      => 'Main Content',
                        'input'                      => 'textarea',
                        'wysiwyg_enabled'         => 1,
                        'required'                   => false,
                        'sort_order'                 => 8,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'meta_title'         => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Title',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Meta'
                    ),

                    'meta_keywords'      => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Keywords',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Meta'
                    ),

                    'meta_description'   => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Description',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 8,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Meta'
                    ),

                    'custom_template'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Template',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 1,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),
                    'custom_design'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Design',
                        'input'                      => 'select',
                        'source'                     => 'core/design_source_design',
                        'required'                   => false,
                        'sort_order'                 => 2,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),
                    'custom_layout_update' => array(
                        'type'                       => 'text',
                        'label'                      => 'Custom Layout Update',
                        'input'                      => 'textarea',
                        'backend'                    => 'catalog/attribute_backend_customlayoutupdate',
                        'required'                   => false,
                        'sort_order'                 => 4,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),
                    'page_layout'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Layout',
                        'input'                      => 'select',
                        'source'                     => 'catalog/product_attribute_source_layout',
                        'required'                   => false,
                        'sort_order'                 => 5,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),

                    'created_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_created',
                        'sort_order'                 => 19,
                        'visible'                    => false,
                        'user_defined'               => 0
                    ),
                    'updated_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_updated',
                        'sort_order'                 => 20,
                        'visible'                    => false,
                        'user_defined'               => 0
                    ),

                    'enable_sharing'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Enable Sharing',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 1,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Advanced'
                    ),

                    'enable_comments'          => array(
                        'type'                       => 'int',
                        'label'                      => 'Enable Comments',
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'sort_order'                 => 2,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Advanced'
                    ),

                )
            ),
            'gene_bluefoot_taxonomy_term' => array(
                'entity_model' => 'gene_bluefoot/taxonomy_term',
                'table' => 'gene_bluefoot/taxonomy_term',
                'attribute_model' => 'gene_bluefoot/resource_eav_attribute',
                'additional_attribute_table' => 'gene_bluefoot/eav_attribute',
                'entity_attribute_collection' => 'gene_bluefoot/taxonomy_term_attribute_collection',
                //'default_group' => 'General',
                'attributes' => array(
                    'title' => array(
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Title',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '1',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '10',
                        'note'           => '',
                        'visible'        => '1',
                        'wysiwyg_enabled'=> '0',
                    ),
                    'url_key'            => array(
                        'type'                       => 'varchar',
                        'label'                      => 'URL Key',
                        'input'                      => 'text',
                        'backend'                    => 'gene_bluefoot/attribute_backend_urlkey',
                        'required'                   => false,
                        'sort_order'                 => 20,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'General'
                    ),
                    'status' => array(
                        'group'          => 'General',
                        'type'           => 'int',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Enabled',
                        'input'          => 'select',
                        'source'         => 'eav/entity_attribute_source_boolean',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '1',
                        'sort_order'       => '30',
                        'visible'        => '1',
                    ),

                    'description'            => array(
                        'type'                       => 'text',
                        'label'                      => 'Description',
                        'input'                      => 'textarea',
                        'wysiwyg_enabled'         => 1,
                        'required'                   => false,
                        'sort_order'                 => 40,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => false,
                        'group'                     => 'General'
                    ),

                    'image'         => array(
                        'type'                       => 'text',
                        'label'                      => 'Image',
                        'required'                   => false,
                        'input'                      => 'image',
                        'backend'                    => 'gene_bluefoot/attribute_backend_image',
                        'sort_order'                 => 50,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'General'
                    ),

                    'taxonomy_id' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Taxonomy id',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),
                    'parent_id' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Parent id',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),
                    'path' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Path',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),
                    'position' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Position',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),
                    'level' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Level',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),
                    'children_count' => array(
                        'group'          => 'General',
                        'type'           => 'static',
                        'backend'        => '',
                        'frontend'       => '',
                        'label'          => 'Children count',
                        'input'          => 'text',
                        'source'         => '',
                        'global'         => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'required'       => '',
                        'user_defined'   => false,
                        'default'        => '',
                        'unique'         => false,
                        'sort_order'       => '0',
                        'note'           => '',
                        'visible'        => '0',
                    ),

                    'meta_title'         => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Title',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 6,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Meta'
                    ),

                    'meta_keywords'      => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Keywords',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 7,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Meta'
                    ),

                    'meta_description'   => array(
                        'type'                       => 'text',
                        'label'                      => 'Meta Description',
                        'input'                      => 'textarea',
                        'required'                   => false,
                        'sort_order'                 => 8,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Meta'
                    ),


                    'page_layout'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Page Layout',
                        'input'                      => 'select',
                        'source'                     => 'gene_bluefoot/taxonomy_term_attribute_source_layout',
                        'required'                   => false,
                        'sort_order'                 => 10,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Design'
                    ),

                    'show_description'        => array(
                        'type'                       => 'int',
                        'label'                      => 'Show Description',
                        'required'                   => false,
                        'input'                      => 'select',
                        'source'                     => 'eav/entity_attribute_source_boolean',
                        'backend'                    => '',
                        'sort_order'                 => 20,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                    ),

                    'display_mode'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Display Mode',
                        'required'                   => false,
                        'input'                      => 'select',
                        'source'                     => 'gene_bluefoot/taxonomy_term_attribute_source_displaymode',
                        'sort_order'                 => 30,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),

                    'column_type'        => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Column Type',
                        'required'                   => false,
                        'input'                      => 'select',
                        'source'                     => 'gene_bluefoot/taxonomy_term_attribute_source_columntype',
                        'sort_order'                 => 40,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 0,
                        'group'                     => 'Design'
                    ),


                    'custom_template'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Template',
                        'input'                      => 'text',
                        'required'                   => false,
                        'sort_order'                 => 50,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Design'
                    ),

                    'custom_design'      => array(
                        'type'                       => 'varchar',
                        'label'                      => 'Custom Design',
                        'input'                      => 'select',
                        'source'                     => 'core/design_source_design',
                        'required'                   => false,
                        'sort_order'                 => 60,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Design'
                    ),
                    'custom_layout_update' => array(
                        'type'                       => 'text',
                        'label'                      => 'Custom Layout Update',
                        'input'                      => 'textarea',
                        'backend'                    => 'catalog/attribute_backend_customlayoutupdate',
                        'required'                   => false,
                        'sort_order'                 => 70,
                        'global'                     => Gene_BlueFoot_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'user_defined'               => 1,
                        'group'                     => 'Design'
                    ),

                    'created_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_created',
                        'sort_order'                 => 19,
                        'visible'                    => false,
                        'user_defined'               => 0
                    ),
                    'updated_at'         => array(
                        'type'                       => 'static',
                        'input'                      => 'text',
                        'backend'                    => 'eav/entity_attribute_backend_time_updated',
                        'sort_order'                 => 20,
                        'visible'                    => false,
                        'user_defined'               => 0
                    ),
                )
            )
        );
    }

    /**
     * Create Core CMS Entity Table
     *
     * @param $tableName
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Exception
     */
    public function createEntityTypeTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Entity Type Id')
            ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                'nullable'  => true,
                'default'   => null,
            ), 'Content Identifier')
            ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
            ), 'Attribute Set Id')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'Type Name')
            ->addColumn('content_type', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(
                'nullable'  => false,
            ), 'Content Type')
            ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
                'nullable'  => true
            ), 'Type Description')


            ->addColumn('url_key_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(
                'nullable'  => true
            ), 'URL Key Prefix')


            ->addColumn('renderer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'Renderer')
            ->addColumn('item_view_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'Single Template')
            ->addColumn('list_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'List Template')
            ->addColumn('list_item_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'List Item Template')
            ->addColumn('item_layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable'  => true
            ), 'Item Layout Update XML')
            ->addColumn('list_layout_update_xml', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
                'nullable'  => true
            ), 'List Layout Update XML')
            ->addColumn('singular_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'Singular Name')
            ->addColumn('plural_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
                'nullable'  => true
            ), 'Plural Name')

            ->addColumn('include_in_sitemap', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                'nullable'  => false,
                'default' => 0
            ), 'Include In Sitemap')

            ->addColumn('searchable', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
                'nullable'  => false,
                'default' => 0
            ), 'Searchable')

            ->addColumn('icon_class', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
                'nullable'  => true
            ), 'Icon Class')
            ->addColumn('color', Varien_Db_Ddl_Table::TYPE_TEXT, 11, array(
                'nullable'  => true
            ), 'Color')
            ->addIndex($this->getIdxName($tableName, array('identifier')),
                array('identifier'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
            ->addIndex($this->getIdxName($tableName, array('attribute_set_id')),
                array('attribute_set_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
        ;

        try {
            $connection->createTable($table);
            //$connection->commit(); //these do not work when creating a table
        } catch (Exception $e) {
            //$connection->rollBack();
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Can\'t create table: %s', $tableName));
        }

        return $this;

    }

    /**
     * @param $tableName
     * @throws Zend_Db_Exception
     */
    public function createInstallerTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('installation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Installer ID')
            ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 40, array(), 'Indentifier')
            ->addColumn('status', Varien_Db_Ddl_Table::TYPE_TEXT, 25, array(), 'Installer Status')
            ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 140, array(), 'Installer Name')
            ->addColumn('import_file', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Import File')
            ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Installer Description')
            ->addColumn('date_added', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Date Added')
            ->addColumn('date_installed', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Date installed')
            ->addColumn('date_uninstalled', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(), 'Date uninstalled')
            ->addColumn('log_data', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(), 'Log History')
            ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(), 'Additional Data')
            ->addIndex($this->getIdxName($tableName, array('identifier')),
                array('identifier'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
            ->setComment('Installer table');

        $connection->createTable($table);
    }

    /**
     * @param $tableName
     * @throws Zend_Db_Exception
     */
    public function createAttributeTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Attribute ID')
            ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute scope')
            ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('default'=> 0), 'Attribute uses WYSIWYG')
            ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('default'=> 1), 'Attribute is visible')
            ->addColumn('content_scope', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array('default'=> 0), 'Attribute belongs to which content scope')
            ->addColumn('frontend_input_renderer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Frontend Input Renderer')
            ->addColumn('widget' , Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Widget')
            ->addColumn('data_model', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'The model which the attribute can fetch data from')
            ->addColumn('template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Template')
            ->addColumn('list_template', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(), 'Template')
            ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_BLOB, 255, array(), 'Additional Data')
            ->setComment('Additional attribute table');

        $connection->createTable($table);
    }

    public function createTaxonomyTable($tableName)
    {
        $connection = $this->getConnection();

        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('taxonomy_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned' => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Taxonomy ID')
            ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array('default' => 'category'), 'Taxonomy Scope')
            ->addColumn('is_nestable', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array('default'=> 0), 'Is Nestable')
            ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array('default'=> 1), 'Is Active')
            ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Title')
            ->addColumn('plural_name', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Plural Name')
            ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Description')
            ->addColumn('additional_data', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(), 'Additional Data')
            ->addColumn('term_url_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(), 'Prefix for taxonomy term urls')
            ->setComment('Taxonomy table');

        $connection->createTable($table);
    }

    public function createTaxonomyTermTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Entity ID')
            ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ), 'Entity Type ID')
            ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ), 'Attribute Set ID')
            ->addColumn('taxonomy_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
            ), 'Taxonomy id')
            ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
            ), 'Parent id')

            ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            ), 'Path')

            ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
            ), 'Position')

            ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
            ), 'Level')

            ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
            ), 'Children count')

            ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            ), 'Creation Time')
            ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            ), 'Update Time')
            ->addIndex($this->getIdxName($tableName, array('entity_type_id')),
                array('entity_type_id'))
            ->addIndex($this->getIdxName($tableName, array('attribute_set_id')),
                array('attribute_set_id'))
            ->addForeignKey($this->getFkName($tableName, 'attribute_set_id', 'eav/attribute_set','attribute_set_id'),
                'attribute_set_id', $this->getTable('eav/attribute_set'), 'attribute_set_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($this->getFkName($tableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->setComment('Taxonomy Term Table');

        $connection->createTable($table);
    }

    public function createTermContentTable($tableName)
    {
        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($tableName))
            ->addColumn('term_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'Term Entity ID')
            ->addColumn('content_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => false,
                'default'   => '0',
            ), 'Content Entity ID')
            ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ), 'Position')
            ->addIndex($this->getIdxName($tableName, array('term_id', 'content_id')),
                array('term_id', 'content_id'),
                array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
            )
            ->addForeignKey($this->getFkName($tableName, 'term_id', 'gene_bluefoot/taxonomy_term', 'entity_id'),
                'term_id', $this->getTable('gene_bluefoot/taxonomy_term'), 'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($this->getFkName($tableName, 'content_id', 'gene_bluefoot/entity', 'entity_id'),
                'content_id', $this->getTable('gene_bluefoot/entity'), 'entity_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->setComment('Taxonomy Term Content Table');

        $connection->createTable($table);
    }

    public function createAppTable($appTableName, $appContentTypesTable, $appTaxonomyTable)
    {
        $installationTable = $this->getTable('gene_bluefoot/install');
        $entityTypeTable = $this->getTable('gene_bluefoot/type');
        $taxonomyTable = $this->getTable('gene_bluefoot/taxonomy');

        $connection = $this->getConnection();
        $table = $connection
            ->newTable($this->getTable($appTableName))
            ->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'identity'  => true,
                'nullable'  => false,
                'primary'   => true,
            ), 'Attribute ID')
            ->addColumn('installation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => true,
            ), 'Installer ID')
            ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 140, array(), 'Title')
            ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(), 'Description')
            ->addColumn('menu_position', Varien_Db_Ddl_Table::TYPE_TEXT, 140, array(), 'Menu Position')
            ->addColumn('url_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(), 'Url Prefix')
            ->addColumn('option_data', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(), 'Option Data')

//            ->addForeignKey($this->getFkName($appTableName, 'installation_id', $installationTable, 'installation_id'),
//                'entity_type_id', $installationTable, 'installation_id',
//                Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ;

        $connection->createTable($table);


        $table = $connection
            ->newTable($this->getTable($appContentTypesTable))
            ->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'App ID')
            ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => true,
            ), 'CMS TYPE ID')
            ->addForeignKey($this->getFkName($appContentTypesTable, 'app_id', $appTableName, 'app_id'),
                'app_id', $appTableName, 'app_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($this->getFkName($appContentTypesTable, 'type_id', $entityTypeTable, 'type_id'),
                'type_id', $entityTypeTable, 'type_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ;

        $connection->createTable($table);

        $table = $connection
            ->newTable($this->getTable($appTaxonomyTable))
            ->addColumn('app_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'unsigned'  => true,
                'nullable'  => false,
            ), 'App ID')
            ->addColumn('taxonomy_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                'nullable'  => true,
            ), 'CMS TYPE ID')
            ->addForeignKey($this->getFkName($appTaxonomyTable, 'app_id', $appTableName, 'app_id'),
                'app_id', $appTableName, 'app_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
            ->addForeignKey($this->getFkName($appTaxonomyTable, 'taxonomy_id', $taxonomyTable, 'taxonomy_id'),
                'taxonomy_id', $taxonomyTable, 'taxonomy_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ;

        $connection->createTable($table);
    }


    /**
     * @param $baseTableName
     * @param array $options
     * @return $this
     * @throws Mage_Core_Exception
     * @throws Zend_Db_Exception
     */
    public function createEntityTables($baseTableName, array $options = array())
    {
        $isNoCreateMainTable = $this->_getValue($options, 'no-main', false);
        $isNoDefaultTypes    = $this->_getValue($options, 'no-default-types', false);
        $customTypes         = $this->_getValue($options, 'types', array());
        $tables              = array();

        $connection = $this->getConnection();

        if (!$isNoCreateMainTable) {

            //Create eav table
            $mainTable = $connection
                ->newTable($this->getTable($baseTableName))
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                ), 'Entity Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Entity Type Id')
                ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Attribute Set Id')

                ->addColumn('identifier', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(
                    'nullable'  => true,
                    'default'   => null,
                ), 'Content Identifier')


                ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Created At')
                ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
                    'nullable'  => false,
                ), 'Updated At')

                ->addIndex($this->getIdxName($baseTableName, array('entity_type_id')),
                    array('entity_type_id'))
                ->addIndex($this->getIdxName($baseTableName, array('identifier')),
                    array('identifier'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
                ->addForeignKey($this->getFkName($baseTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->setComment('Eav Entity Main Table');

            $tables[$this->getTable($baseTableName)] = $mainTable;
        }

        $types = array();
        if (!$isNoDefaultTypes) {
            $types = array(
                'datetime'  => array(Varien_Db_Ddl_Table::TYPE_DATETIME, null),
                'decimal'   => array(Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4'),
                'int'       => array(Varien_Db_Ddl_Table::TYPE_INTEGER, null),
                'text'      => array(Varien_Db_Ddl_Table::TYPE_TEXT, '64k'),
                'varchar'   => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255'),
                'char'      => array(Varien_Db_Ddl_Table::TYPE_TEXT, '255'),
            );
        }

        if (!empty($customTypes)) {
            foreach ($customTypes as $type => $fieldType) {
                if (count($fieldType) != 2) {
                    throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Wrong type definition for %s', $type));
                }
                $types[$type] = $fieldType;
            }
        }

        /**
         * Create table array($baseTableName, $type)
         */
        foreach ($types as $type => $fieldType) {
            $eavTableName = array($baseTableName, $type);

            $eavTable = $connection->newTable($this->getTable($eavTableName));
            $eavTable
                ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'identity'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                ), 'Value Id')
                ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Entity Type Id')
                ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Attribute Id')
                ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Store Id')
                ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ), 'Entity Id')
                ->addColumn('value', $fieldType[0], $fieldType[1], array(
                    'nullable'  => false,
                ), 'Attribute Value')
                ->addIndex($this->getIdxName($eavTableName, array('entity_type_id')),
                    array('entity_type_id'))
                ->addIndex($this->getIdxName($eavTableName, array('attribute_id')),
                    array('attribute_id'))
                ->addIndex($this->getIdxName($eavTableName, array('store_id')),
                    array('store_id'))
                ->addIndex($this->getIdxName($eavTableName, array('entity_id')),
                    array('entity_id'));
            if ($type !== 'text') {
                $eavTable->addIndex($this->getIdxName($eavTableName, array('attribute_id', 'value')),
                    array('attribute_id', 'value'));
                $eavTable->addIndex($this->getIdxName($eavTableName, array('entity_type_id', 'value')),
                    array('entity_type_id', 'value'));
            }

            $eavTable
                ->addForeignKey($this->getFkName($eavTableName, 'entity_id', $baseTableName, 'entity_id'),
                    'entity_id', $this->getTable($baseTableName), 'entity_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->addForeignKey($this->getFkName($eavTableName, 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
                    'entity_type_id', $this->getTable('eav/entity_type'), 'entity_type_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->addForeignKey($this->getFkName($eavTableName, 'store_id', 'core/store', 'store_id'),
                    'store_id', $this->getTable('core/store'), 'store_id',
                    Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
                ->setComment('Eav Entity Value Table');

            $eavTable->addIndex(
                $this->getIdxName($eavTableName, array('entity_id', 'attribute_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
                array('entity_id', 'attribute_id', 'store_id'),
                array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
            );

            $tables[$this->getTable($eavTableName)] = $eavTable;
        }

        try {
            foreach ($tables as $tableName => $table) {
                $connection->createTable($table);
            }
        } catch (Exception $e) {
            echo $e->getMessage() . '<br/><pre>';
            echo $e->getTraceAsString();
            die();
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Can\'t create table: %s', $tableName));
        }

        return $this;
    }


}
