<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Catalog\Model\Product\Image as ProductImage;
use Magento\Framework\App\Filesystem\DirectoryList;

class Image
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\Filesystem */
    private $filesystem;

    /** @var \Magento\Framework\Image\AdapterFactory */
    private $imageFactory;

    /** @var array */
    private $imageConfig;

    /** @var \Magento\Framework\View\ConfigInterface */
    private $viewConfig;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\View\ConfigInterface $configInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\View\ConfigInterface $configInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->viewConfig = $configInterface;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $image
     * @param string $folder
     * @param string $viewId
     * @param string $moduleName
     * @return string
     */
    public function resize($image, $folder = '', $viewId = '', $moduleName = '')
    {
        if (!$image) {
            return null;
        }

        $imageConfig = $this->getImageConfig($viewId, $moduleName);
        if (!$imageConfig) {
            return $this->getMediaUrl() . $folder . $image;
        }

        $thumbnailUrl = $folder . $imageConfig['type'] . DIRECTORY_SEPARATOR
            . $imageConfig['width'] . 'x'
            . $imageConfig['height'] . DIRECTORY_SEPARATOR
            . $image;

        $basePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $thumbnailPath = $basePath->getAbsolutePath($thumbnailUrl);

        if ($basePath->isExist($thumbnailPath)) {
            return $this->getMediaUrl() . $thumbnailUrl;
        }

        $imageUrl = $folder . $image;
        $imagePath = $basePath->getAbsolutePath($imageUrl);

        if (!$basePath->isExist($imagePath)) {
            return '';
        }

        $imageResize = $this->imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->constrainOnly($imageConfig['constrain'] ?? true);
        $imageResize->keepAspectRatio($imageConfig['aspect_ratio'] ?? true);
        $imageResize->keepFrame($imageConfig['frame'] ?? true);
        $imageResize->keepTransparency($imageConfig['transparency'] ?? true);
        $imageResize->backgroundColor($imageConfig['background'] ?? '[255, 255, 255]');
        $imageResize->quality($this->scopeConfig->getValue(ProductImage::XML_PATH_JPEG_QUALITY));
        $imageResize->resize($imageConfig['width'], $imageConfig['height']);
        $imageResize->save($thumbnailPath);

        return $this->getMediaUrl() . $thumbnailUrl;
    }

    /**
     * @param string $viewId
     * @param string $moduleName
     * @return array|null
     */
    public function getImageConfig($viewId, $moduleName)
    {
        if (!$this->imageConfig) {
            $this->imageConfig = $this->viewConfig->getViewConfig()->getMediaEntities(
                $moduleName,
                'images'
            );
        }

        return $this->imageConfig[$viewId] ?? null;
    }

    /**
     * Get Media Url
     * @return string
     */
    public function getMediaUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
    }
}
