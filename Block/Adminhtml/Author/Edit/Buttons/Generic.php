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
namespace Sample\News\Block\Adminhtml\Author\Edit\Buttons;

use Magento\Backend\Block\Widget\Context;
use Sample\News\Api\AuthorRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Generic
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var AuthorRepositoryInterface
     */
    protected $authorRepository;

    /**
     * @param Context $context
     * @param AuthorRepositoryInterface $authorRepository
     */
    public function __construct(
        Context $context,
        AuthorRepositoryInterface $authorRepository
    ) {
        $this->context = $context;
        $this->authorRepository = $authorRepository;
    }

    /**
     * Return Author page ID
     *
     * @return int|null
     */
    public function getAuthorId()
    {
        try {
            return $this->authorRepository->getById(
                $this->context->getRequest()->getParam('author_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
