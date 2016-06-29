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
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Model\Author;

class MassDisable extends MassAction
{
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Author $author
     * @return $this
     */
    protected function massAction(Author $author)
    {
        $author->setIsActive($this->isActive);
        $this->authorRepository->save($author);
        return $this;
    }
}
