<?php
/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Sample
 * @package   Sample_News
 * @copyright 2016 Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru
 */
namespace Sample\News\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;


class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    // @codingStandardsIgnoreStart
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    // @codingStandardsIgnoreEnd
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

            $installer->getConnection()->addIndex(
                $installer->getTable('sample_news_author'),
                $setup->getIdxName(
                    $installer->getTable('sample_news_author'),
                    ['name','photo'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'name',
                    'biography',
                    'url_key',
                    'resume',
                    'country',
                    'meta_title',
                    'meta_keywords',
                    'meta_description'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
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
                    Table::ACTION_CASCADE
                )
                ->addForeignKey(
                    $installer->getFkName('sample_news_author_store', 'store_id', 'store', 'store_id'),
                    'store_id',
                    $installer->getTable('store'),
                    'store_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Author To Store Link Table');
            $installer->getConnection()->createTable($table);
        }

    }
}
