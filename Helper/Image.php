<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

class Image
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\Filesystem */
    private $filesystem ;

    /** @var \Magento\Framework\Image\AdapterFactory */
    private $imageFactory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param string $image
     * @param string $folder
     * @param int $width
     * @param int $height
     * @return string
     */
    public function resize($image, $folder = '', $width = null, $height = null)
    {
        $imageUrl = $folder . $image;
        $thumbnailUrl = $folder . 'resized/' . $width . 'x' . $height . '/' . $image;

        $basePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $imagePath = $basePath->getAbsolutePath($imageUrl);
        $thumbnailPath = $basePath->getAbsolutePath($thumbnailUrl);

        $imageResize = $this->imageFactory->create();
        $imageResize->open($imagePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->keepAspectRatio(true);
        $imageResize->resize($width, $height);
        $imageResize->save($thumbnailPath);

        $resizedURL = $this->storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . $thumbnailUrl;
        return $resizedURL;
    }
}
