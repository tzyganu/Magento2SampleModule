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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem;
use Magento\Framework\Image as MagentoImage;
use Magento\Framework\Image\Factory as MagentoImageFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\FileSystem as ViewFileSystem;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @method string getFile()
 * @method string getLabel()
 * @method string getPosition()
 */
class Image extends AbstractModel
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * Default quality value (for JPEG images only).
     *
     * @var int
     */
    protected $quality = 80;

    /**
     * @var bool
     */
    protected $keepAspectRatio = true;

    /**
     * @var bool
     */
    protected $keepFrame = true;

    /**
     * @var bool
     */
    protected $keepTransparency = true;

    /**
     * @var bool
     */
    protected $constrainOnly = true;

    /**
     * @var int[]
     */
    protected $backgroundColor = [255, 255, 255];

    /**
     * @var string
     */
    protected $baseFile;

    /**
     * @var bool
     */
    protected $isBaseFilePlaceholder;

    /**
     * @var string|bool
     */
    protected $newFile;

    /**
     * @var MagentoImage
     */
    protected $processor;

    /**
     * @var string
     */
    protected $destinationSubdir;

    /**
     * @var int
     */
    protected $angle;

    /**
     * @var string
     */
    protected $watermarkFile;

    /**
     * @var int
     */
    protected $watermarkPosition;

    /**
     * @var int
     */
    protected $watermarkWidth;

    /**
     * @var int
     */
    protected $watermarkHeight;

    /**
     * @var int
     */
    protected $watermarkImageOpacity = 70;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\View\FileSystem
     */
    protected $viewFileSystem;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Uploader
     */
    protected $uploader;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param Uploader $uploader
     * @param Database $coreFileStorageDatabase
     * @param Filesystem $filesystem
     * @param ImageFactory $imageFactory
     * @param Repository $assetRepo
     * @param ViewFileSystem $viewFileSystem
     * @param ScopeConfigInterface $scopeConfig
     * @param $entityCode
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        Uploader $uploader,
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        MagentoImageFactory $imageFactory,
        Repository $assetRepo,
        ViewFileSystem $viewFileSystem,
        ScopeConfigInterface $scopeConfig,
        $entityCode,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager             = $storeManager;
        $this->uploader                 = $uploader;
        $this->coreFileStorageDatabase  = $coreFileStorageDatabase;
        $this->imageFactory             = $imageFactory;
        $this->assetRepo                = $assetRepo;
        $this->viewFileSystem           = $viewFileSystem;
        $this->scopeConfig              = $scopeConfig;
        $this->entityCode               = $entityCode;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mediaDirectory->create($this->uploader->getBasePath());

    }

    /**
     * @param int $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Get image quality
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepAspectRatio($keep)
    {
        $this->keepAspectRatio = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepFrame($keep)
    {
        $this->keepFrame = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepTransparency($keep)
    {
        $this->keepTransparency = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setConstrainOnly($flag)
    {
        $this->constrainOnly = (bool)$flag;
        return $this;
    }

    /**
     * @param int[] $rgbArray
     * @return $this
     */
    public function setBackgroundColor(array $rgbArray)
    {
        $this->backgroundColor = $rgbArray;
        return $this;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        // determine width and height from string
        list($width, $height) = explode('x', strtolower($size), 2);
        foreach (['width', 'height'] as $wh) {
            ${$wh} = (int)${$wh};
            if (empty(${$wh})) {
                ${$wh} = null;
            }
        }

        // set sizes
        $this->setWidth($width)->setHeight($height);

        return $this;
    }

    /**
     * @param string|null $file
     * @return bool
     */
    protected function checkMemory($file = null)
    {
        return $this->getMemoryLimit() > $this->getMemoryUsage() + $this->getNeedMemoryForFile(
            $file
        )
        || $this->getMemoryLimit() == -1;
    }

    /**
     * @return string
     */
    protected function getMemoryLimit()
    {
        $memoryLimit = trim(strtoupper(ini_get('memory_limit')));

        if (!isset($memoryLimit[0])) {
            $memoryLimit = "128M";
        }

        if (substr($memoryLimit, -1) == 'K') {
            return substr($memoryLimit, 0, -1) * 1024;
        }
        if (substr($memoryLimit, -1) == 'M') {
            return substr($memoryLimit, 0, -1) * 1024 * 1024;
        }
        if (substr($memoryLimit, -1) == 'G') {
            return substr($memoryLimit, 0, -1) * 1024 * 1024 * 1024;
        }
        return $memoryLimit;
    }

    /**
     * @return int
     */
    protected function getMemoryUsage()
    {
        if (function_exists('memory_get_usage')) {
            return memory_get_usage();
        }
        return 0;
    }

    /**
     * @param string|null $file
     * @return float|int
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getNeedMemoryForFile($file = null)
    {
        $file = $file === null ? $this->getBaseFile() : $file;
        if (!$file) {
            return 0;
        }

        if (!$this->mediaDirectory->isExist($file)) {
            return 0;
        }

        $imageInfo = getimagesize($this->mediaDirectory->getAbsolutePath($file));

        if (!isset($imageInfo[0]) || !isset($imageInfo[1])) {
            return 0;
        }
        if (!isset($imageInfo['channels'])) {
            // if there is no info about this parameter lets set it for maximum
            $imageInfo['channels'] = 4;
        }
        if (!isset($imageInfo['bits'])) {
            // if there is no info about this parameter lets set it for maximum
            $imageInfo['bits'] = 8;
        }
        return round(
            ($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65
        );
    }

    /**
     * Convert array of 3 items (decimal r, g, b) to string of their hex values
     *
     * @param int[] $rgbArray
     * @return string
     */
    protected function rgbToString($rgbArray)
    {
        $result = [];
        foreach ($rgbArray as $value) {
            if (null === $value) {
                $result[] = 'null';
            } else {
                $result[] = sprintf('%02s', dechex($value));
            }
        }
        return implode($result);
    }

    /**
     * Set filenames for base file and new file
     *
     * @param string $file
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setBaseFile($file)
    {
        $this->isBaseFilePlaceholder = false;

        if ($file && 0 !== strpos($file, '/', 0)) {
            $file = '/' . $file;
        }
        $baseDir = $this->uploader->getBasePath();

        if ($file) {
            if (!$this->fileExists($baseDir . $file) || !$this->checkMemory($baseDir . $file)) {
                $file = null;
            }
        }
        if (!$file) {
            $this->isBaseFilePlaceholder = true;
            $this->newFile = true;
            return $this;
        }

        $baseFile = $baseDir . $file;

        if (!$file || !$this->mediaDirectory->isFile($baseFile)) {
            throw new \Exception(__('We can\'t find the image file.'));
        }

        $this->baseFile = $baseFile;

        // build new filename (most important params)
        $path = [
            $this->uploader->getBasePath(),
            'cache',
            $this->storeManager->getStore()->getId(),
            $path[] = $this->getDestinationSubdir(),
        ];
        if (!empty($this->width) || !empty($this->height)) {
            $path[] = "{$this->width}x{$this->height}";
        }

        // add misk params as a hash
        $miscParams = [
            ($this->keepAspectRatio ? '' : 'non') . 'proportional',
            ($this->keepFrame ? '' : 'no') . 'frame',
            ($this->keepTransparency ? '' : 'no') . 'transparency',
            ($this->constrainOnly ? 'do' : 'not') . 'constrainonly',
            $this->rgbToString($this->backgroundColor),
            'angle' . $this->angle,
            'quality' . $this->quality,
        ];

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeight();
        }

        $path[] = md5(implode('_', $miscParams));

        // append prepared filename
        $this->newFile = implode('/', $path) . $file;
        // the $file contains heading slash

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseFile()
    {
        return $this->baseFile;
    }

    /**
     * @return bool|string
     */
    public function getNewFile()
    {
        return $this->newFile;
    }

    /**
     * Retrieve 'true' if image is a base file placeholder
     *
     * @return bool
     */
    public function isBaseFilePlaceholder()
    {
        return (bool)$this->isBaseFilePlaceholder;
    }

    /**
     * @param MagentoImage $processor
     * @return $this
     */
    public function setImageProcessor($processor)
    {
        $this->processor = $processor;
        return $this;
    }

    /**
     * @return MagentoImage
     */
    public function getImageProcessor()
    {
        if (!$this->processor) {
            $filename = $this->getBaseFile() ? $this->mediaDirectory->getAbsolutePath($this->getBaseFile()) : null;
            $this->processor = $this->imageFactory->create($filename);
        }
        $this->processor->keepAspectRatio($this->keepAspectRatio);
        $this->processor->keepFrame($this->keepFrame);
        $this->processor->keepTransparency($this->keepTransparency);
        $this->processor->constrainOnly($this->constrainOnly);
        $this->processor->backgroundColor($this->backgroundColor);
        $this->processor->quality($this->quality);
        return $this->processor;
    }

    /**
     * @see \Magento\Framework\Image\Adapter\AbstractAdapter
     * @return $this
     */
    public function resize()
    {
        if ($this->getWidth() === null && $this->getHeight() === null) {
            return $this;
        }
        $this->getImageProcessor()->resize($this->width, $this->height);
        return $this;
    }

    /**
     * @param int $angle
     * @return $this
     */
    public function rotate($angle)
    {
        $angle = intval($angle);
        $this->getImageProcessor()->rotate($angle);
        return $this;
    }

    /**
     * Set angle for rotating
     *
     * This func actually affects only the cache filename.
     *
     * @param int $angle
     * @return $this
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $file
     * @param string $position
     * @param array $size ['width' => int, 'height' => int]
     * @param int $width
     * @param int $height
     * @param int $opacity
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setWatermark(
        $file,
        $position = null,
        $size = null,
        $width = null,
        $height = null,
        $opacity = null
    ) {
        if ($this->isBaseFilePlaceholder) {
            return $this;
        }

        if ($file) {
            $this->setWatermarkFile($file);
        } else {
            return $this;
        }

        if ($position) {
            $this->setWatermarkPosition($position);
        }
        if ($size) {
            $this->setWatermarkSize($size);
        }
        if ($width) {
            $this->setWatermarkWidth($width);
        }
        if ($height) {
            $this->setWatermarkHeight($height);
        }
        if ($opacity) {
            $this->setWatermarkImageOpacity($opacity);
        }
        $filePath = $this->getWatermarkFilePath();

        if ($filePath) {
            $imagePreprocessor = $this->getImageProcessor();
            $imagePreprocessor->setWatermarkPosition($this->getWatermarkPosition());
            $imagePreprocessor->setWatermarkImageOpacity($this->getWatermarkImageOpacity());
            $imagePreprocessor->setWatermarkWidth($this->getWatermarkWidth());
            $imagePreprocessor->setWatermarkHeight($this->getWatermarkHeight());
            $imagePreprocessor->watermark($filePath);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function saveFile()
    {
        if ($this->isBaseFilePlaceholder && $this->newFile === true) {
            return $this;
        }
        $filename = $this->mediaDirectory->getAbsolutePath($this->getNewFile());
        $this->getImageProcessor()->save($filename);
        $this->coreFileStorageDatabase->saveFile($filename);
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->newFile === true) {
            $url = $this->assetRepo->getUrl(
                "Sample_News::images/".$this->entityCode."/placeholder/{$this->getDestinationSubdir()}.jpg"
            );
        } else {
            $url = $this->storeManager->getStore()->getBaseUrl(
                    UrlInterface::URL_TYPE_MEDIA
                ) . $this->newFile;
        }

        return $url;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function setDestinationSubdir($dir)
    {
        $this->destinationSubdir = $dir;
        return $this;
    }

    /**
     * @return string
     */
    public function getDestinationSubdir()
    {
        return $this->destinationSubdir;
    }

    /**
     * @return bool|void
     */
    public function isCached()
    {
        if (is_string($this->newFile)) {
            return $this->fileExists($this->newFile);
        }
        return false;
    }

    /**
     * Set watermark file name
     *
     * @param string $file
     * @return $this
     */
    public function setWatermarkFile($file)
    {
        $this->watermarkFile = $file;
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    public function getWatermarkFile()
    {
        return $this->watermarkFile;
    }

    /**
     * Get relative watermark file path
     * or false if file not found
     *
     * @return string | bool
     */
    protected function getWatermarkFilePath()
    {
        $filePath = false;

        if (!($file = $this->getWatermarkFile())) {
            return $filePath;
        }
        $baseDir = $this->uploader->getBasePath();

        $candidates = [
            $baseDir . '/watermark/stores/' . $this->storeManager->getStore()->getId() . $file,
            $baseDir . '/watermark/websites/' . $this->storeManager->getWebsite()->getId() . $file,
            $baseDir . '/watermark/default/' . $file,
            $baseDir . '/watermark/' . $file,
        ];
        foreach ($candidates as $candidate) {
            if ($this->mediaDirectory->isExist($candidate)) {
                $filePath = $this->mediaDirectory->getAbsolutePath($candidate);
                break;
            }
        }
        if (!$filePath) {
            $filePath = $this->viewFileSystem->getStaticFileName($file);
        }

        return $filePath;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return $this
     */
    public function setWatermarkPosition($position)
    {
        $this->watermarkPosition = $position;
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    public function getWatermarkPosition()
    {
        return $this->watermarkPosition;
    }

    /**
     * Set watermark image opacity
     *
     * @param int $imageOpacity
     * @return $this
     */
    public function setWatermarkImageOpacity($imageOpacity)
    {
        $this->watermarkImageOpacity = $imageOpacity;
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    public function getWatermarkImageOpacity()
    {
        return $this->watermarkImageOpacity;
    }

    /**
     * Set watermark size
     *
     * @param array $size
     * @return $this
     */
    public function setWatermarkSize($size)
    {
        if (is_array($size)) {
            $this->setWatermarkWidth($size['width'])->setWatermarkHeight($size['height']);
        }
        return $this;
    }

    /**
     * Set watermark width
     *
     * @param int $width
     * @return $this
     */
    public function setWatermarkWidth($width)
    {
        $this->watermarkWidth = $width;
        return $this;
    }

    /**
     * Get watermark width
     *
     * @return int
     */
    public function getWatermarkWidth()
    {
        return $this->watermarkWidth;
    }

    /**
     * Set watermark height
     *
     * @param int $height
     * @return $this
     */
    public function setWatermarkHeight($height)
    {
        $this->watermarkHeight = $height;
        return $this;
    }

    /**
     * Get watermark height
     *
     * @return string
     */
    public function getWatermarkHeight()
    {
        return $this->watermarkHeight;
    }

    /**
     * @return void
     */
    public function clearCache()
    {
        $directory = $this->uploader->getBasePath() . '/cache';
        $this->mediaDirectory->delete($directory);

        $this->coreFileStorageDatabase->deleteFolder($this->mediaDirectory->getAbsolutePath($directory));
    }

    /**
     * First check this file on FS
     * If it doesn't exist - try to download it from DB
     *
     * @param string $filename
     * @return bool
     */
    protected function fileExists($filename)
    {
        if ($this->mediaDirectory->isFile($filename)) {
            return true;
        } else {
            return $this->coreFileStorageDatabase->saveFileToFilesystem(
                $this->mediaDirectory->getAbsolutePath($filename)
            );
        }
    }

    /**
     * Return resized image information
     *
     * @return array
     */
    public function getResizedImageInfo()
    {
        $fileInfo = null;
        if ($this->newFile === true) {
            $asset = $this->assetRepo->createAsset(
                "Sample_News::images/".$this->entityCode."/placeholder/{$this->getDestinationSubdir()}.jpg"
            );
            $img = $asset->getSourceFile();
            $fileInfo = getimagesize($img);
        } else {
            if ($this->mediaDirectory->isFile($this->mediaDirectory->getAbsolutePath($this->newFile))) {
                $fileInfo = getimagesize($this->mediaDirectory->getAbsolutePath($this->newFile));
            }
        }
        return $fileInfo;
    }
}
