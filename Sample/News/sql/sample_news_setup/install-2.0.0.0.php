<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
if (!$this->tableExists('sample_news_article')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_article'))
        ->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ),
            'Article ID'
        )
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
        ->addColumn('in_rss', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
            'default'   => '1',
        ), 'Show in rss')
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
            'unsigned' => true,
            'nullable' => false,
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
    ->addColumn('article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Article ID')
    ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Product ID')
    ->addColumn(
        'position',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => '0'),
        'Position')
    ->addIndex($this->getIdxName('sample_news_article_product', array('product_id')), array('product_id'))
    ->addForeignKey(
        $this->getFkName('sample_news_article_product', 'article_id', 'sample_news_article', 'entity_id'),
        'article_id',
        $this->getTable('sample_news_article'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName('sample_news_article_product', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id',
        $this->getTable('catalog_product_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
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
    )
    ->addColumn('article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Article ID')
    ->addColumn('category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Category ID')
    ->addColumn(
        'position',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => '0'),
        'Position'
    )->addIndex(
        $this->getIdxName('sample_news_article_category', array('article_id')),
        array('article_id')
    )
    ->addForeignKey(
        $this->getFkName('sample_news_article_category', 'article_id', 'sample_news_article', 'entity_id'),
        'article_id',
        $this->getTable('sample_news_article'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
    )
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
if (!$this->tableExists('sample_news_section')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_section'))
        ->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array(
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ),
            'Section ID'
        )
        ->addColumn('name', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Section Name')

        ->addColumn('status', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(), 'Enabled')
        ->addColumn('parent_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Section Parent id')
        ->addColumn('path', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Path')

        ->addColumn('position', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Section Position')

        ->addColumn('level', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Section Level')

        ->addColumn('children_count', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Section Children count')

        ->addColumn('in_rss', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(), 'Section In RSS')
        ->addColumn('meta_title', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'Meta title')
        ->addColumn('meta_keywords', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(), 'Meta keywords')
        ->addColumn('meta_description', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', array(), 'Meta description')
        ->addColumn('identifier', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Section String Identifier')
        ->addColumn('updated_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Modification Time')
        ->addColumn('creation_time', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(), 'Creation Time')
        ->setComment('Section Table');
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_section_store')) {
    $table = $this->getConnection()
        ->newTable($this->getTable('sample_news_section_store'))
        ->addColumn('section_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'   => true,
        ), 'Section ID')
        ->addColumn('store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Store ID')
        ->addIndex($this->getIdxName('sample_news_section_store', array('store_id')),
            array('store_id'))
        ->addForeignKey($this->getFkName('sample_news_section_store', 'section_id', 'sample_news_section', 'entity_id'),
            'section_id', $this->getTable('sample_news_section'), 'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->addForeignKey($this->getFkName('sample_news_section_store', 'store_id', 'store', 'store_id'),
            'store_id', $this->getTable('store'), 'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE, \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
        ->setComment('Section To Store Linkage Table');
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_section_product')) {
    $table = $this->getConnection()->newTable(
        $this->getTable('sample_news_section_product')
    )
    ->addColumn('section_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Section ID')
    ->addColumn('product_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        'primary'   => true,
    ), 'Product ID')
    ->addColumn(
        'position',
        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        null,
        array('nullable' => false, 'default' => '0'),
        'Position')
    ->addIndex($this->getIdxName('sample_news_section_product', array('product_id')), array('product_id'))
    ->addForeignKey(
        $this->getFkName('sample_news_section_product', 'section_id', 'sample_news_section', 'entity_id'),
        'section_id',
        $this->getTable('sample_news_section'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $this->getFkName('sample_news_section_product', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id',
        $this->getTable('catalog_product_entity'),
        'entity_id',
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE)
    ->addIndex(
        $this->getIdxName(
            'sample_news_section_product',
            array('section_id', 'product_id'),
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('section_id', 'product_id'),
        array('type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))->setComment(
        'Section To Product Linkage Table'
    );
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_section_category')) {
    $table = $this->getConnection()->newTable(
        $this->getTable('sample_news_section_category')
    )
        ->addColumn('section_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'   => true,
        ), 'Section ID')
        ->addColumn('category_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'   => true,
        ), 'Category ID')
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'default' => '0'),
            'Position'
        )->addIndex(
            $this->getIdxName('sample_news_section_category', array('section_id')),
            array('section_id')
        )
        ->addForeignKey(
            $this->getFkName('sample_news_section_category', 'section_id', 'sample_news_section', 'entity_id'),
            'section_id',
            $this->getTable('sample_news_section'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName('sample_news_section_category', 'category_id', 'catalog_category_entity', 'entity_id'),
            'category_id',
            $this->getTable('catalog_category_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Section To Category Linkage Table'
        );
    $this->getConnection()->createTable($table);
}

if (!$this->tableExists('sample_news_article_section')) {
    $table = $this->getConnection()->newTable(
        $this->getTable('sample_news_article_section')
    )
        ->addColumn('article_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'   => true,
        ), 'Article ID')
        ->addColumn('section_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'primary'   => true,
        ), 'Section ID')
        ->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false, 'default' => '0'),
            'Position'
        )->addIndex(
            $this->getIdxName('sample_news_article_section', array('article_id')),
            array('article_id')
        )
        ->addForeignKey(
            $this->getFkName('sample_news_article_section', 'article_id', 'sample_news_article', 'entity_id'),
            'article_id',
            $this->getTable('sample_news_article'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $this->getFkName('sample_news_article_section', 'section_id', 'sample_news_section', 'entity_id'),
            'section_id',
            $this->getTable('sample_news_section'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Article To Section Linkage Table'
        );
    $this->getConnection()->createTable($table);
}

