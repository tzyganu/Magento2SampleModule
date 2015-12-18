<?php
namespace Sample\News\Controller\Adminhtml\Author;

class MassEnable extends MassDisable
{
    /**
     * @var string
     */
    protected $successMessage = 'A total of %1 authors have been enabled';
    /**
     * @var string
     */
    protected $errorMessage = 'An error occurred while enabling authors.';
    /**
     * @var bool
     */
    protected $isActive = true;
}
