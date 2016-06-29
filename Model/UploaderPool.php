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
namespace Sample\News\Model;

use Magento\Framework\ObjectManagerInterface;

class UploaderPool
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var array
     */
    protected $uploaders;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $uploaders
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $uploaders = []
    ) {
        $this->objectManager = $objectManager;
        $this->uploaders     = $uploaders;
    }

    /**
     * @param $type
     * @return Uploader
     * @throws \Exception
     */
    public function getUploader($type)
    {
        if (!isset($this->uploaders[$type])) {
            throw new \Exception("Uploader not found for type: ".$type);
        }
        if (!is_object($this->uploaders[$type])) {
            $this->uploaders[$type] = $this->objectManager->create($this->uploaders[$type]);

        }
        $uploader = $this->uploaders[$type];
        if (!($uploader instanceof Uploader)) {
            throw new \Exception("Uploader for type {$type} not instance of ". Uploader::class);
        }
        return $uploader;
    }
}
