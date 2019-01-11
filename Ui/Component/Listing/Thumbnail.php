<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Ui\Component\Listing;

class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    const ALT_FIELD = 'name';

    /** @var string */
    protected $baseUrl = '';

    /** @var string */
    protected $route = '*';

    /** @var string */
    protected $controller = '*';

    /** @var string */
    protected $key = 'id';

    /** @var \Mygento\Base\Helper\Image */
    private $imageHelper;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\UrlInterface */
    private $urlBuilder;

    /**
     * @param \Mygento\Base\Helper\Image $imageHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Mygento\Base\Helper\Image $imageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param string[] $dataSource
     * @return string[]
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $fieldName = $this->getData('name');
                $image = $item[$fieldName];

                $imageUrl = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . $this->baseUrl . $image;
                $thumbnailUrl = $this->imageHelper->resize(
                    $image,
                    $this->baseUrl
                );

                $item[$fieldName . '_src'] = $thumbnailUrl;
                $item[$fieldName . '_alt'] = $this->getAlt($item);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    $this->route . '/' . $this->controller . '/edit',
                    [
                        'id' => $item[$this->key]
                    ]
                );
                $item[$fieldName . '_orig_src'] = $imageUrl;
            }
        }
        return $dataSource;
    }

    /**
     * @param string[] $row
     * @return string
     */
    private function getAlt($row)
    {
        $altField = $this->getData('config/altField') ?: self::ALT_FIELD;
        return $row[$altField] ?? '';
    }
}
