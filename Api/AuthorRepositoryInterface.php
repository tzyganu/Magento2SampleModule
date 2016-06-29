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
namespace Sample\News\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Sample\News\Api\Data\AuthorInterface;

/**
 * @api
 */
interface AuthorRepositoryInterface
{
    /**
     * Save page.
     *
     * @param AuthorInterface $author
     * @return AuthorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(AuthorInterface $author);

    /**
     * Retrieve Author.
     *
     * @param int $authorId
     * @return AuthorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($authorId);

    /**
     * Retrieve pages matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Sample\News\Api\Data\AuthorSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete author.
     *
     * @param AuthorInterface $author
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(AuthorInterface $author);

    /**
     * Delete author by ID.
     *
     * @param int $authorId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($authorId);
}
