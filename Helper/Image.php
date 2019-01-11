<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\Filesystem */
    private $filesystem;

    /** @var \Magento\Framework\Image\AdapterFactory */
    private $imageFactory;

    /**  @var array */
    private $imageConfig;

    /** @var string */
    private $module;

    /** @var string */
    private $imageType;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Framework\View\ConfigInterface $configInterface
     * @param string $module
     * @param string $imageType
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Framework\View\ConfigInterface $configInterface,
        $module,
        $imageType
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
        $this->viewConfig = $configInterface;
        $this->module = $module;
        $this->imageType = $imageType;
    }

    /**
     * @param string $image
     * @param string $folder
     * @return string
     */
    public function resize($image, $folder = '')
    {
        $imageConfig = $this->getImageConfig();
        if (!$imageConfig) {
            return $this->getMediaUrl() . $folder . $image;
        }

        $imageUrl = $folder . $image;
        $thumbnailUrl = $folder . 'resized' . DIRECTORY_SEPARATOR
          . $imageConfig['width'] . 'x'
          . $imageConfig['height'] . DIRECTORY_SEPARATOR
          . $image;

        $basePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $imagePath = $basePath->getAbsolutePath($imageUrl);
        $thumbnailPath = $basePath->getAbsolutePath($thumbnailUrl);

        $imageResize = $this->imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->constrainOnly($imageConfig['constrainOnly'] ?? true);
        $imageResize->keepTransparency($imageConfig['keepTransparency'] ?? true);
        $imageResize->keepFrame($imageConfig['keepFrame'] ?? true);
        $imageResize->keepAspectRatio($imageConfig['keepAspectRatio'] ?? true);
        $imageResize->resize($imageConfig['width'], $imageConfig['height']);
        $imageResize->save($thumbnailPath);

        return $this->getMediaUrl() . $thumbnailUrl;
    }

    /**
     * @return array|null
     */
    public function getImageConfig()
    {
        if (!$this->imageConfig) {
            $this->imageConfig = $this->viewConfig->getViewConfig()->getMediaEntities(
                $this->module,
                'images'
            );
        }
        return $this->imageConfig[$this->imageType] ?? null;
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
