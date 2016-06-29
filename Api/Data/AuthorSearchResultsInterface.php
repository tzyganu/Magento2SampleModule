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
namespace Sample\News\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface AuthorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get author list.
     *
     * @return \Sample\News\Api\Data\AuthorInterface[]
     */
    public function getItems();

    /**
     * Set authors list.
     *
     * @param \Sample\News\Api\Data\AuthorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
