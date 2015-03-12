<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sample\News\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (!$installer->tableExists('sample_news_author')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('sample_news_author'));
            $table->addColumn(
                    'author_id',
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_TEXT,
                    255,
                    ['nullable'  => false,],
                    'Author Name'
                )
                ->addColumn(
                    'url_key',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable'  => false,],
                    'Author Url Key'
                )
                ->addColumn(
                    'biography',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Author Biography'
                )
                ->addColumn(
                    'dob',
                    Table::TYPE_DATE,
                    null,
                    [],
                    'Author Birth date'
                )
                ->addColumn(
                    'awards',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Author Awards'
                )
                ->addColumn(
                    'type',
                    Table::TYPE_INTEGER,
                    null,
                    [],
                    'Author Type'
                )
                ->addColumn(
                    'avatar',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Author Avatar'
                )
                ->addColumn(
                    'resume',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Author Resume'
                )
                ->addColumn(
                    'country',
                    Table::TYPE_TEXT,
                    2,
                    [],
                    'Author Country'
                )
                ->addColumn(
                    'meta_title',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Author Meta Title'
                )
                ->addColumn(
                    'meta_description',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Author Meta Description'
                )
                ->addColumn(
                    'meta_keywords',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Author Meta Keywords'
                )
                ->addColumn(
                    'is_active',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable'  => false,
                        'default'   => '1',
                    ],
                    'Is Author Active'
                )
                ->addColumn(
                    'in_rss',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable'  => false,
                        'default'   => '1',
                    ],
                    'Show in rss'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Update at'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [],
                    'Creation Time'
                )
                ->setComment('News authors');
            $installer->getConnection()->createTable($table);
        }

        //Create Authors to Store table
        if (!$installer->tableExists('sample_news_author_store')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('sample_news_author_store'));
            $table->addColumn(
                    'author_id',
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'unsigned'  => true,
                        'nullable'  => false,
                        'primary'   => true,
                    ],
                    'Store ID'
                )
                ->addIndex(
                    $installer->getIdxName('sample_news_author_store', ['store_id']),
                    ['store_id']
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_store', 'author_id', 'sample_news_author', 'author_id'),
                    'author_id',
                    $installer->getTable('sample_news_author'),
                    'author_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_store', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->setComment('Author To Store Link Table');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('sample_news_author_product')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('sample_news_author_product'));
            $table->addColumn(
                    'author_id',
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'default' => '0'
                    ],
                    'Position'
                )
                ->addIndex(
                    $installer->getIdxName('sample_news_author_product', ['product_id']),
                    ['product_id']
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_product', 'author_id', 'sample_news_author', 'author_id'),
                    'author_id',
                    $installer->getTable('sample_news_author'),
                    'author_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                    'product_id',
                    $installer->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        'sample_news_author_product',
                        [
                            'author_id',
                            'product_id'
                        ],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [
                        'author_id',
                        'product_id'
                    ],
                    [
                        'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                    ]
                )
                ->setComment('Author To product Link Table');
            $installer->getConnection()->createTable($table);
        }

        if (!$installer->tableExists('sample_news_author_category')) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('sample_news_author_category'));
            $table->addColumn(
                    'author_id',
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_INTEGER,
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
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => false,
                        'default' => '0'
                    ],
                    'Position'
                )
                ->addIndex(
                    $installer->getIdxName('sample_news_author_category', ['category_id']),
                    ['category_id']
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_category', 'author_id', 'sample_news_author', 'author_id'),
                    'author_id',
                    $installer->getTable('sample_news_author'),
                    'author_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_category', 'category_id', 'catalog_category_entity', 'entity_id'),
                    'category_id',
                    $installer->getTable('catalog_category_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE
                )
                ->addIndex(
                    $installer->getIdxName(
                        'sample_news_author_category',
                        [
                            'author_id',
                            'category_id'
                        ],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [
                        'author_id',
                        'category_id'
                    ],
                    [
                        'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                    ]
                )
                ->setComment('Author To category Link Table');
            $installer->getConnection()->createTable($table);
        }
    }
}
