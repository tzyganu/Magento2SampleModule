<?php
namespace Sample\News\Controller\Adminhtml\Author;

use Sample\News\Model\Author;
class MassDelete extends MassAction
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 record(s) have been deleted';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while deleting record(s).';

    /**
     * @param $author
     * @return $this
     */
    protected function doTheAction(Author $author)
    {
        $author->delete();
        return $this;
    }
}
