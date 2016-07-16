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
namespace Sample\News\Controller\Author;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;

class Rss extends \Magento\Rss\Controller\Feed\Index
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->getRequest()->setParam('type', 'authors');
        parent::execute();
    }
}
