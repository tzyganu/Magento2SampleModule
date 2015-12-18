<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Model\Author;
class MassDisable extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 authors have been disabled';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while disabling authors.';
    /**
     * @var bool
     */
    protected $isActive = false;

    /**
     * @param Author $author
     * @return $this
     */
    protected function doTheAction(Author $author)
    {
        $author->setIsActive($this->isActive);
        $author->save();
        return $this;
    }
}
