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

namespace Sample\News\Ui\DataProvider\Author\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Sample\News\Model\ResourceModel\Author\CollectionFactory;

class AuthorData implements ModifierInterface
{
    /**
     * @var \Sample\News\Model\ResourceModel\Author\Collection
     */
    protected $collection;

    /**
     * @param CollectionFactory $authorCollectionFactory
     */
    public function __construct(
        CollectionFactory $authorCollectionFactory
    ) {
        $this->collection = $authorCollectionFactory->create();
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function modifyData(array $data)
    {
        $items = $this->collection->getItems();
        /** @var $author \Sample\News\Model\Author */
        foreach ($items as $author) {
            $_data = $author->getData();
            if (isset($_data['avatar'])) {
                $avatar = [];
                $avatar[0]['name'] = $author->getAvatar();
                $avatar[0]['url'] = $author->getAvatarUrl();
                $_data['avatar'] = $avatar;
            }
            if (isset($_data['resume'])) {
                $resume = [];
                $resume[0]['name'] = $author->getResume();
                $resume[0]['url'] = $author->getResumeUrl();
                $_data['resume'] = $resume;
            }
            $author->setData($_data);
            $data[$author->getId()] = $_data;
        }
        return $data;
    }
}
