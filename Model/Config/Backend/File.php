<?php

/**
 * @author Mygento Team
 * @copyright 2014-2021 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * @method getFieldConfig()
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class File extends \Magento\Framework\App\Config\Value
{
    /**
     * Upload max file size in kilobytes
     *
     * @var int
     */
    protected $maxFileSize = 0;

    /**
     * @var \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface
     */
    private $requestData;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @param \Magento\Framework\File\UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\File\UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->requestData = $requestData;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->directoryList = $directoryList;
    }

    /**
     * Save uploaded file before saving config value
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $file = $this->getFileData();
        if (!empty($file)) {
            $uploadDir = $this->getUploadDir();

            try {
                /** @var \Magento\Framework\File\Uploader $uploader */
                $uploader = $this->uploaderFactory->create(['fileId' => $file]);
                $uploader->setAllowedExtensions($this->getAllowedExtensions());
                $uploader->setAllowRenameFiles(true);
                $uploader->addValidateCallback('size', $this, 'validateMaxSize');
                $result = $uploader->save($uploadDir);
            } catch (\Exception $e) {
                throw new LocalizedException(__('%1', $e->getMessage()));
            }

            $filename = $result['file'];
            if ($filename) {
                if ($this->addWhetherScopeInfo()) {
                    $filename = $this->prependScopeInfo($filename);
                }
                $this->setValue($filename);
            }

            return $this;
        }
        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
        } else {
            $this->unsValue();
        }

        return $this;
    }

    /**
     * Validation callback for checking max file size
     *
     * @param string $filePath Path to temporary uploaded file
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function validateMaxSize($filePath)
    {
        $directory = $this->filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        if ($this->maxFileSize > 0 &&
            $directory->stat(
                $directory->getRelativePath($filePath)
            )['size'] > $this->maxFileSize * 1024
        ) {
            throw new LocalizedException(
                __(
                    'The file you\'re uploading exceeds the server size limit of %1 kilobytes.',
                    $this->maxFileSize
                )
            );
        }
    }

    /**
     * Getter for allowed extensions of uploaded files
     *
     * @return array
     */
    protected function getAllowedExtensions()
    {
        return [];
    }

    /**
     * Receiving uploaded file data
     *
     * @return array
     */
    private function getFileData()
    {
        $file = [];
        // phpcs:disable
        //FIXME: TODO: $value is string. Check why do we treat it as array
        // phpcs:enable
        $value = $this->getValue();
        $tmpName = $this->requestData->getTmpName($this->getPath());
        if ($tmpName) {
            $file['tmp_name'] = $tmpName;
            $file['name'] = $this->requestData->getName($this->getPath());
        } elseif (!empty($value['tmp_name'])) {
            $file['tmp_name'] = $value['tmp_name'];
            $file['name'] = $value['value'] ?? $value['name'];
        }

        return $file;
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return bool
     */
    private function addWhetherScopeInfo()
    {
        $fieldConfig = $this->getFieldConfig();
        $dirParams = array_key_exists('upload_dir', $fieldConfig) ? $fieldConfig['upload_dir'] : [];

        return is_array($dirParams) && array_key_exists(
            'scope_info',
            $dirParams
        ) && $dirParams['scope_info'];
    }

    /**
     * Return path to directory for upload file
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    private function getUploadDir()
    {
        $fieldConfig = $this->getFieldConfig();

        if (!array_key_exists('upload_dir', $fieldConfig)) {
            throw new LocalizedException(
                __('The base directory to upload file is not specified.')
            );
        }

        if (is_array($fieldConfig['upload_dir'])) {
            $uploadDir = $fieldConfig['upload_dir']['value'];
            if (array_key_exists(
                'scope_info',
                $fieldConfig['upload_dir']
            ) && $fieldConfig['upload_dir']['scope_info']
            ) {
                $uploadDir = $this->appendScopeInfo($uploadDir);
            }

            if (array_key_exists('config', $fieldConfig['upload_dir'])) {
                $uploadDir = $this->getUploadDirPath($uploadDir);
            }
        } else {
            $uploadDir = (string) $fieldConfig['upload_dir'];

            return $this->directoryList->getPath('var') . '/' . $uploadDir;
        }

        return $uploadDir;
    }

    /**
     * Retrieve upload directory path
     *
     * @param string $uploadDir
     * @return string
     */
    private function getUploadDirPath($uploadDir)
    {
        return $this->directoryList->getPath('var') . '/' . $uploadDir;
    }

    /**
     * Prepend path with scope info
     *
     * E.g. 'stores/2/path' , 'websites/3/path', 'default/path'
     *
     * @param string $path
     * @return string
     */
    private function prependScopeInfo($path)
    {
        $scopeInfo = $this->getScope();
        if (ScopeConfigInterface::SCOPE_TYPE_DEFAULT != $this->getScope()) {
            $scopeInfo .= '/' . $this->getScopeId();
        }

        return $scopeInfo . '/' . $path;
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    private function appendScopeInfo($path)
    {
        $path .= '/' . $this->getScope();
        if (ScopeConfigInterface::SCOPE_TYPE_DEFAULT != $this->getScope()) {
            $path .= '/' . $this->getScopeId();
        }

        return $path;
    }
}
