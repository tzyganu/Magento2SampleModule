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
namespace Sample\News\Plugin\Block;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Tree\Node;
use Magento\Theme\Block\Html\Topmenu as TopmenuBlock;
use Sample\News\Model\Author\Url;

class Topmenu
{
    /**
     * @var Url
     */
    protected $url;
    /**
     * @var Http
     */
    protected $request;

    /**
     * @param Url $url
     * @param Http $request
     */
    public function __construct(
        Url $url,
        Http $request
    ) {
        $this->url      = $url;
        $this->request  = $request;
    }

    /**
     * @param TopmenuBlock $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    // @codingStandardsIgnoreStart
    public function beforeGetHtml(
        TopmenuBlock $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        // @codingStandardsIgnoreEnd
        $node = new Node(
            $this->getNodeAsArray(),
            'id',
            $subject->getMenu()->getTree(),
            $subject->getMenu()
        );
        $subject->getMenu()->addChild($node);
    }

    /**
     * @return array
     */
    protected function getNodeAsArray()
    {
        return [
            'name' => __('Authors'),
            'id' => 'authors-node',
            'url' => $this->url->getListUrl(),
            'has_active' => false,
            'is_active' => in_array($this->request->getFullActionName(), $this->getActiveHandles())
        ];
    }

    /**
     * @return array
     */
    protected function getActiveHandles()
    {
        return [
            'sample_news_author_index',
            'sample_news_author_view'
        ];
    }
}
