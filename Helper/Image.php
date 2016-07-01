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
namespace Sample\News\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Sample\News\Model\ImageFactory;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Image extends AbstractHelper
{
    /**
     * Media config node
     */
    const MEDIA_TYPE_CONFIG_NODE = 'images';

    /**
     * Current model
     *
     * @var \Sample\News\Model\Image
     */
    protected $model;

    /**
     * Scheduled for resize image
     *
     * @var bool
     */
    protected $scheduleResize = true;

    /**
     * Scheduled for rotate image
     *
     * @var bool
     */
    protected $scheduleRotate = false;

    /**
     * Angle
     *
     * @var int
     */
    protected $angle;

    /**
     * Watermark file name
     *
     * @var string
     */
    protected $watermark;

    /**
     * Watermark Position
     *
     * @var string
     */
    protected $watermarkPosition;

    /**
     * Watermark Size
     *
     * @var string
     */
    protected $watermarkSize;

    /**
     * Watermark Image opacity
     *
     * @var int
     */
    protected $watermarkImageOpacity;

    /**
     * Current Product
     *
     * @var \Magento\Framework\Model\AbstractModel
     */
    protected $entity;

    /**
     * Image File
     *
     * @var string
     */
    protected $imageFile;

    /**
     * Image Placeholder
     *
     * @var string
     */
    protected $placeholder;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * image factory
     *
     * @var \Sample\News\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    /**
     * @var \Magento\Framework\Config\View
     */
    protected $configView;

    /**
     * Image configuration attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $entityCode;

    /**
     * @param Context $context
     * @param ImageFactory $imageFactory
     * @param Repository $assetRepo
     * @param ConfigInterface $viewConfig
     * @param $entityCode
     */
    public function __construct(
        Context $context,
        ImageFactory $imageFactory,
        Repository $assetRepo,
        ConfigInterface $viewConfig,
        $entityCode
    ) {
        $this->imageFactory = $imageFactory;
        $this->assetRepo    = $assetRepo;
        $this->viewConfig   = $viewConfig;
        $this->entityCode   = $entityCode;
        parent::__construct($context);
    }

    /**
     * Reset all previous data
     *
     * @return $this
     */
    protected function _reset()
    {
        $this->model                    = null;
        $this->scheduleRotate           = false;
        $this->angle                    = null;
        $this->watermark                = null;
        $this->watermarkPosition        = null;
        $this->watermarkSize            = null;
        $this->watermarkImageOpacity    = null;
        $this->entity                   = null;
        $this->imageFile                = null;
        $this->attributes               = [];
        return $this;
    }

    /**
     * Initialize Helper to work with Image
     *
     * @param \Magento\Framework\Model\AbstractModel $entity
     * @param string $imageId
     * @param array $attributes
     * @return $this
     */
    public function init($entity, $imageId, $attributes = [])
    {
        $this->_reset();

        $this->attributes = array_merge(
            $this->getConfigView()->getMediaAttributes('Sample_News', self::MEDIA_TYPE_CONFIG_NODE, $imageId),
            $attributes
        );

        $this->setEntity($entity);
        $this->setImageProperties();
        $this->setWatermarkProperties();

        return $this;
    }

    /**
     * Set image properties
     *
     * @return $this
     */
    protected function setImageProperties()
    {
        $this->getModel()->setDestinationSubdir($this->getType());

        $this->getModel()->setWidth($this->getWidth());
        $this->getModel()->setHeight($this->getHeight());

        // Set 'keep frame' flag
        $frame = $this->getFrame();
        if (!empty($frame)) {
            $this->getModel()->setKeepFrame($frame);
        }

        // Set 'constrain only' flag
        $constrain = $this->getAttribute('constrain');
        if (!empty($constrain)) {
            $this->getModel()->setConstrainOnly($constrain);
        }

        // Set 'keep aspect ratio' flag
        $aspectRatio = $this->getAttribute('aspect_ratio');
        if (!empty($aspectRatio)) {
            $this->getModel()->setKeepAspectRatio($aspectRatio);
        }

        // Set 'transparency' flag
        $transparency = $this->getAttribute('transparency');
        if (!empty($transparency)) {
            $this->getModel()->setKeepTransparency($transparency);
        }

        // Set background color
        $background = $this->getAttribute('background');
        if (!empty($background)) {
            $this->getModel()->setBackgroundColor($background);
        }

        return $this;
    }

    /**
     * Set watermark properties
     * @return $this
     */
    protected function setWatermarkProperties()
    {
        // @codingStandardsIgnoreStart
        //TODO: set proper watermarks paths
        // @codingStandardsIgnoreEnd
        $this->setWatermark(
            $this->scopeConfig->getValue(
                "design/watermark/{$this->getModel()->getDestinationSubdir()}_image",
                ScopeInterface::SCOPE_STORE
            )
        );
        $this->setWatermarkImageOpacity(
            $this->scopeConfig->getValue(
                "design/watermark/{$this->getModel()->getDestinationSubdir()}_imageOpacity",
                ScopeInterface::SCOPE_STORE
            )
        );
        $this->setWatermarkPosition(
            $this->scopeConfig->getValue(
                "design/watermark/{$this->getModel()->getDestinationSubdir()}_position",
                ScopeInterface::SCOPE_STORE
            )
        );
        $this->setWatermarkSize(
            $this->scopeConfig->getValue(
                "design/watermark/{$this->getModel()->getDestinationSubdir()}_size",
                ScopeInterface::SCOPE_STORE
            )
        );
        return $this;
    }

    /**
     * Schedule resize of the image
     * $width *or* $height can be null - in this case, lacking dimension will be calculated.
     *
     * @see \Sample\News\Model\Image
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        $this->getModel()->setWidth($width)->setHeight($height);
        $this->scheduleResize = true;
        return $this;
    }

    /**
     * Set image quality, values in percentage from 0 to 100
     *
     * @param int $quality
     * @return $this
     */
    public function setQuality($quality)
    {
        $this->getModel()->setQuality($quality);
        return $this;
    }

    /**
     * Guarantee, that image picture width/height will not be distorted.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see \Sample\News\Model\Image
     * @param bool $flag
     * @return $this
     */
    public function keepAspectRatio($flag)
    {
        $this->getModel()->setKeepAspectRatio($flag);
        return $this;
    }

    /**
     * Guarantee, that image will have dimensions, set in $width/$height
     * Applicable before calling resize()
     * Not applicable, if keepAspectRatio(false)
     *
     * @see \Sample\News\Model\Image
     * @param bool $flag
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function keepFrame($flag)
    {
        $this->getModel()->setKeepFrame($flag);
        return $this;
    }

    /**
     * Guarantee, that image will not lose transparency if any.
     * Applicable before calling resize()
     * It is true by default.
     *
     * @see \Sample\News\Model\Image
     * @param bool $flag
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function keepTransparency($flag)
    {
        $this->getModel()->setKeepTransparency($flag);
        return $this;
    }

    /**
     * Guarantee, that image picture will not be bigger, than it was.
     * Applicable before calling resize()
     * It is false by default
     *
     * @param bool $flag
     * @return $this
     */
    public function constrainOnly($flag)
    {
        $this->getModel()->setConstrainOnly($flag);
        return $this;
    }

    /**
     * Set color to fill image frame with.
     * Applicable before calling resize()
     * The keepTransparency(true) overrides this (if image has transparent color)
     * It is white by default.
     *
     * @see \Sample\News\Model\Image\Image
     * @param array $colorRGB
     * @return $this
     */
    public function backgroundColor($colorRGB)
    {
        // assume that 3 params were given instead of array
        if (!is_array($colorRGB)) {
            $colorRGB = func_get_args();
        }
        $this->getModel()->setBackgroundColor($colorRGB);
        return $this;
    }

    /**
     * Rotate image into specified angle
     *
     * @param int $angle
     * @return $this
     */
    public function rotate($angle)
    {
        $this->setAngle($angle);
        $this->getModel()->setAngle($angle);
        $this->scheduleRotate = true;
        return $this;
    }

    /**
     * Add watermark to image
     * size param in format 100x200
     *
     * @param string $fileName
     * @param string $position
     * @param string $size
     * @param int $imageOpacity
     * @return $this
     */
    public function watermark($fileName, $position, $size = null, $imageOpacity = null)
    {
        $this->setWatermark($fileName)
            ->setWatermarkPosition($position)
            ->setWatermarkSize($size)
            ->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Set placeholder
     *
     * @param string $fileName
     * @return void
     */
    public function placeholder($fileName)
    {
        $this->placeholder = $fileName;
    }

    /**
     * Get Placeholder
     *
     * @param null|string $placeholder
     * @return string
     */
    public function getPlaceholder($placeholder = null)
    {
        if ($placeholder) {
            $placeholderFullPath = 'Sample_News::images/'.$this->entityCode.'/placeholder/' . $placeholder . '.jpg';
        } else {
            $placeholderFullPath = $this->placeholder
                ?: 'Sample_News::images/'.$this->entityCode.'/placeholder/' . $this->getModel()->getDestinationSubdir() . '.jpg';
        }
        return $placeholderFullPath;
    }

    /**
     * Apply scheduled actions
     *
     * @return $this
     * @throws \Exception
     */
    protected function applyScheduledActions()
    {
        $this->initBaseFile();
        if ($this->isScheduledActionsAllowed()) {
            $model = $this->getModel();
            if ($this->scheduleRotate) {
                $model->rotate($this->getAngle());
            }
            if ($this->scheduleResize) {
                $model->resize();
            }
            if ($this->getWatermark()) {
                $model->setWatermark($this->getWatermark());
            }
            $model->saveFile();
        }
        return $this;
    }

    /**
     * Initialize base image file
     *
     * @return $this
     */
    protected function initBaseFile()
    {
        $model = $this->getModel();
        $baseFile = $model->getBaseFile();
        if (!$baseFile) {
            if ($this->getImageFile()) {
                $model->setBaseFile($this->getImageFile());
            } else {
                $model->setBaseFile($this->getEntity()->getData($model->getDestinationSubdir()));
            }
        }
        return $this;
    }

    /**
     * Check if scheduled actions is allowed
     *
     * @return bool
     */
    protected function isScheduledActionsAllowed()
    {
        $model = $this->getModel();
        if ($model->isBaseFilePlaceholder()
            && $model->getNewFile() === true
            || $model->isCached()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve image URL
     *
     * @return string
     */
    public function getUrl()
    {
        try {
            $this->applyScheduledActions();
            return $this->getModel()->getUrl();
        } catch (\Exception $e) {
            return $this->getDefaultPlaceholderUrl();
        }
    }

    /**
     * @return $this
     */
    public function save()
    {
        $this->applyScheduledActions();
        return $this;
    }

    /**
     * Return resized image information
     *
     * @return array
     */
    public function getResizedImageInfo()
    {
        $this->applyScheduledActions();
        return $this->getModel()->getResizedImageInfo();
    }

    /**
     * @param null|string $placeholder
     * @return string
     */
    public function getDefaultPlaceholderUrl($placeholder = null)
    {
        try {
            $url = $this->assetRepo->getUrl($this->getPlaceholder($placeholder));
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $url = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAMAAABEpIrGAAAAnFBMVEUAAAAAAAAAAAAAAAC0tLS4uLi0tLT6+vrx8fHo6Ojj4OC+mpq7UE2wXlqzOTGtHwza2dnGaWeiJxDV0dGNJhari4uqeHi/W1edTkevSkWWNCOLEQiXEgbExMXbiorTgH+NT0a4RD+XIxTPFwm7BQHYAwHJAwDLd3aWcXDKV03JODOjNyunLCS6JQ6nCQO/q6viqKjFREOYQTrQIiHNANNfAAAAB3RSTlMCPiYZ3uvFTohbPwAAAURJREFUOMuVk9dygzAQRW3jeGUhikQvDtUl7uX//y0b4bGMCDPJAV64Z+5Ko9HkLxizEYynMCW/A9OXsETI8xkRSPeNNpDnOyZo5dChCe8pAUJ04Qd65bzYXilIaSiYrPhEisoEiS5YvOCVbdt8G1kDATGjbWV5QC7HiDNzKFCb2/J3wqKIVcuBEDImi60vxo5JEvYEQCFmB0/mt5sdHhNLF+CQHGS+3+Ok2k8HQprVAPf944E51JkuAMRZdrk7m7WDuZfncV9Awiz3uxzlcx5qAgHq5067kXkgzj7VBCSoz63YYX/stCKAQQNAKdrmJITTbERJJErojMA/Net1c/IDPBxK6cJ424WE7so0LXcuoauV585nk5fQh5AVgDvvBqgGBRaA+yFztcheAfWoytUIJbhd/3iDykcWuZhpl3eqYUz+wTdj6SFVkjRnJQAAAABJRU5ErkJggg==';
        }
        return $url;
    }

    /**
     * Get current Image model
     *
     * @return \Sample\News\Model\Image
     */
    protected function getModel()
    {
        if (!$this->model) {
            $this->model = $this->imageFactory->create(['entityCode' => 'author']);
        }
        return $this->model;
    }

    /**
     * Set Rotation Angle
     *
     * @param int $angle
     * @return $this
     */
    protected function setAngle($angle)
    {
        $this->angle = $angle;
        return $this;
    }

    /**
     * Get Rotation Angle
     *
     * @return int
     */
    protected function getAngle()
    {
        return $this->angle;
    }

    /**
     * Set watermark file name
     *
     * @param string $watermark
     * @return $this
     */
    protected function setWatermark($watermark)
    {
        $this->watermark = $watermark;
        $this->getModel()->setWatermarkFile($watermark);
        return $this;
    }

    /**
     * Get watermark file name
     *
     * @return string
     */
    protected function getWatermark()
    {
        return $this->watermark;
    }

    /**
     * Set watermark position
     *
     * @param string $position
     * @return $this
     */
    protected function setWatermarkPosition($position)
    {
        $this->watermarkPosition = $position;
        $this->getModel()->setWatermarkPosition($position);
        return $this;
    }

    /**
     * Get watermark position
     *
     * @return string
     */
    protected function getWatermarkPosition()
    {
        return $this->watermarkPosition;
    }

    /**
     * Set watermark size
     * param size in format 100x200
     *
     * @param string $size
     * @return $this
     */
    public function setWatermarkSize($size)
    {
        $this->watermarkSize = $size;
        $this->getModel()->setWatermarkSize($this->parseSize($size));
        return $this;
    }

    /**
     * Get watermark size
     *
     * @return string
     */
    protected function getWatermarkSize()
    {
        return $this->watermarkSize;
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
        $this->getModel()->setWatermarkImageOpacity($imageOpacity);
        return $this;
    }

    /**
     * Get watermark image opacity
     *
     * @return int
     */
    protected function getWatermarkImageOpacity()
    {
        if ($this->watermarkImageOpacity) {
            return $this->watermarkImageOpacity;
        }

        return $this->getModel()->getWatermarkImageOpacity();
    }

    /**
     * Set current Entity
     *
     * @param \Magento\Framework\Model\AbstractModel
     * @return $this
     */
    protected function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * Get current Product
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set Image file
     *
     * @param string $file
     * @return $this
     */
    public function setImageFile($file)
    {
        $this->imageFile = $file;
        return $this;
    }

    /**
     * Get Image file
     *
     * @return string
     */
    protected function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Retrieve size from string
     *
     * @param string $string
     * @return array|bool
     */
    protected function parseSize($string)
    {
        $size = explode('x', strtolower($string));
        if (sizeof($size) == 2) {
            return [
                'width' => $size[0] > 0 ? $size[0] : null,
                'height' => $size[1] > 0 ? $size[1] : null
            ];
        }
        return false;
    }

    /**
     * Retrieve original image width
     *
     * @return int|null
     */
    public function getOriginalWidth()
    {
        return $this->getModel()->getImageProcessor()->getOriginalWidth();
    }

    /**
     * Retrieve original image height
     *
     * @return int|null
     */
    public function getOriginalHeight()
    {
        return $this->getModel()->getImageProcessor()->getOriginalHeight();
    }

    /**
     * Retrieve Original image size as array
     * 0 - width, 1 - height
     *
     * @return int[]
     */
    public function getOriginalSizeArray()
    {
        return [$this->getOriginalWidth(), $this->getOriginalHeight()];
    }

    /**
     * Retrieve config view
     *
     * @return \Magento\Framework\Config\View
     */
    protected function getConfigView()
    {
        if (!$this->configView) {
            $this->configView = $this->viewConfig->getViewConfig();
        }
        return $this->configView;
    }

    /**
     * Retrieve image type
     *
     * @return string
     */
    public function getType()
    {
        return $this->getAttribute('type');
    }

    /**
     * Retrieve image width
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->getAttribute('width');
    }

    /**
     * Retrieve image height
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->getAttribute('height') ?: $this->getAttribute('width');
    }

    /**
     * Retrieve image frame flag
     *
     * @return false|string
     */
    public function getFrame()
    {
        $frame = $this->getAttribute('frame');
        if (empty($frame)) {
            $frame = $this->getConfigView()->getVarValue('Sample_News', 'image_white_borders');
        }
        return $frame;
    }

    /**
     * Retrieve image attribute
     *
     * @param string $name
     * @return string
     */
    protected function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Return image label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->entity->getData('name');
    }
}
