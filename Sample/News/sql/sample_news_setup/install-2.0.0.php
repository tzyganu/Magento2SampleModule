<?php
if (!$this->tableExists('sample_news_author')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_author'))
        ->addColumn(
            'author_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Author ID'
        )
        ->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable'  => false,],
            'Author Name'
        )
        ->addColumn(
            'url_key',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable'  => false,],
            'Author Url Key'
        )
        ->addColumn(
            'biography',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Author Biography'
        )
        ->addColumn(
            'dob',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            [],
            'Author Birth date'
        )
        ->addColumn(
            'awards',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Author Awards'
        )
        ->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Author Type'
        )
        ->addColumn(
            'avatar',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Author Avatar'
        )
        ->addColumn(
            'resume',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Author Resume'
        )
        ->addColumn(
            'country',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            2,
            [],
            'Author Country'
        )
        ->addColumn(
            'meta_title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Author Meta Title'
        )
        ->addColumn(
            'meta_description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Author Meta Description'
        )
        ->addColumn(
            'meta_keywords',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Author Meta Keywords'
        )
        ->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => false,
                'default'   => '1',
            ],
            'Is Author Active'
        )
        ->addColumn(
            'in_rss',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable'  => false,
                'default'   => '1',
            ],
            'Show in rss'
        )
        ->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Update at'
        )
        ->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )
        ->setComment('News authors');
    $this->getConnection()->createTable($table);
}

//Create Authors to Store table
if (!$this->tableExists('sample_news_author_store')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_author_store'))
        ->addColumn(
            'author_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary'   => true,
            ],
            'Author ID'
        )
        ->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ],
            'Store ID'
        )
        ->addIndex(
            $this->getIdxName('sample_news_author_store', ['store_id']),
            ['store_id']
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_store', 'author_id', 'sample_news_author', 'author_id'),
            'author_id',
            $this->getTable('sample_news_author'),
            'author_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $this->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->setComment('Author To Store Link Table');
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_author_product')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_author_product'))
        ->addColumn(
            'author_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary'   => true,
            ],
            'Author ID'
        )
        ->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary'   => true,
            ],
            'Product ID'
        )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default' => '0'
            ],
            'Position'
        )
        ->addIndex(
            $this->getIdxName('sample_news_author_product', ['product_id']),
            ['product_id']
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_product', 'author_id', 'sample_news_author', 'author_id'),
            'author_id',
            $this->getTable('sample_news_author'),
            'author_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_product', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $this->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addIndex(
            $this->getIdxName(
                'sample_news_author_product',
                [
                    'author_id',
                    'product_id'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [
                'author_id',
                'product_id'
            ],
            [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ]
        )
        ->setComment('Author To product Link Table');
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_author_category')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_author_category'))
        ->addColumn(
            'author_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary'   => true,
            ],
            'Author ID'
        )
        ->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false,
                'primary'   => true,
            ],
            'Category ID'
        )
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'nullable' => false,
                'default' => '0'
            ],
            'Position'
        )
        ->addIndex(
            $this->getIdxName('sample_news_author_category', ['category_id']),
            ['category_id']
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_category', 'author_id', 'sample_news_author', 'author_id'),
            'author_id',
            $this->getTable('sample_news_author'),
            'author_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName('sample_news_author_category', 'category_id', 'catalog_category_entity', 'entity_id'),
            'category_id',
            $this->getTable('catalog_category_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addIndex(
            $this->getIdxName(
                'sample_news_author_category',
                [
                    'author_id',
                    'category_id'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            [
                'author_id',
                'category_id'
            ],
            [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ]
        )
        ->setComment('Author To category Link Table');
    $this->getConnection()->createTable($table);
}
