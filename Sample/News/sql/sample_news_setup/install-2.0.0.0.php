<?php
if (!$this->tableExists('sample_news_article')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_article'))
        ->addColumn('entity_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Article ID')
        ->addColumn('title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Article Title')
        ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Article String Identifier')
        ->addColumn('content', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', array(), 'Article Content')
        ->addColumn('meta_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Article Meta Title')
        ->addColumn('meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', array(), 'Article Meta Description')
        ->addColumn('meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', array(), 'Article Meta Keywords')
        ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'default'   => '1',
        ), 'Is Article Active')
        ->addColumn('creation_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
        ->addColumn('update_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        ), 'Modification Time')
        ->setComment('News article');
    $this->getConnection()->createTable($table);
}
if (!$this->tableExists('sample_news_article_store')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_article_store'))
        ->addColumn('article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'primary'   => true,
        ), 'Article ID')
        ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Store ID')
        ->addIndex($this->getIdxName('sample_news_article_store', array('store_id')),
            array('store_id'))
        ->addForeignKey($this->getFkName('sample_news_article_store', 'article_id', 'sample_news_article', 'entity_id'),
            'article_id', $this->getTable('sample_news_article'), 'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->addForeignKey($this->getFkName('sample_news_article_store', 'store_id', 'store', 'store_id'),
            'store_id', $this->getTable('store'), 'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->setComment('News To Store Linkage Table');
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_article_product')) {
    $table = $this->getConnection()->newTable(
        $this->getTable('sample_news_article_product')
    )
    ->addColumn(
        'article_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('unsigned' => true, 'nullable' => false,),
        'Article ID'
    )->addColumn(
        'product_id',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        10,
        array('unsigned' => true, 'nullable' => false,),
        'Product ID'
    )->addColumn(
        'position',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => '0'),
        'Position'
        )->addIndex($this->getIdxName('sample_news_article_product', array('product_id')), array('product_id'))
    //TODO: add foreign key to entity table
//        ->addForeignKey($this->getFkName('sample_news_article_product', 'article_id', 'sample_news_article', 'entity_id'), 'product_id', $this->getTable('sample_news_article'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->addForeignKey($this->getFkName('sample_news_article_product', 'product_id', 'catalog_product_entity', 'entity_id'),    'product_id', $this->getTable('catalog_product_entity'), 'entity_id', \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->addIndex(
            $this->getIdxName(
                'sample_news_article_product',
                array('article_id', 'product_id'),
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            array('article_id', 'product_id'),
            array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))->setComment(
            'Article To product Linkage Table'
        );
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_article_category')) {
    $table = $this->getConnection()->newTable(
        $this->getTable('sample_news_article_category')
    )->addColumn(
            'article_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'),
            'Article ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'),
            'Category ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'default' => '0'),
            'Position'
        )->addIndex(
            $this->getIdxName('sample_news_article_category', array('article_id')),
            array('article_id')
        )
    //TODO: add foreign key to main entity table
//        ->addForeignKey(
//            $this->getFkName('sample_news_article_category', 'article_id', 'sample_news_article', 'entity_id'),
//            'article_id',
//            $this->getTable('sample_news_article'),
//            'entity_id',
//            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
//            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
//        )
        ->addForeignKey(
            $this->getFkName('sample_news_article_category', 'category_id', 'catalog_category_entity', 'entity_id'),
            'category_id',
            $this->getTable('catalog_category_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Article To Category Linkage Table'
        );
    $this->getConnection()->createTable($table);
}