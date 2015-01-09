<?php
namespace Sample\News\Model\Resource\Author;

use \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Core\Model\EntityFactory;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\Model\Resource\Db\AbstractDb;
use \Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sample_news_author_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'author_collection';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * constructor
     *
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param null $connection
     * @param AbstractDb $resource
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Sample\News\Model\Author', 'Sample\News\Model\Resource\Author');
        $this->_map['fields']['author_id'] = 'main_table.author_id';
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('author_id');
        $connection = $this->getConnection();
        if (count($items)) {
            $select = $connection->select()->from(
                    ['author_store' => $this->getTable('sample_news_author_store')]
                )
                ->where(
                    'author_store.author_id IN (?)',
                    $items
                );

            if ($result = $connection->fetchPairs($select)) {
                foreach ($this as $item) {
                    /** @var $item \Sample\News\Model\Author */
                    if (!isset($result[$item->getData('author_id')])) {
                        continue;
                    }
                    $item->setData('store_id', $result[$item->getData('author_id')]);
                }
            }
        }
        return parent::_afterLoad();
    }

    /**
     * Add filter by store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof Store) {
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $store = [$store];
            }

            if ($withAdmin) {
                $store[] = Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store_id', ['in' => $store], 'public');
        }
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store_id')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('sample_news_author_store')],
                'main_table.author_id = store_table.author_id',
                []
            )
            ->group('main_table.author_id');
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
}
