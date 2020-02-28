<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Base Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements \Psr\Log\LoggerInterface
{
    /** @var string */
    protected $code = 'mygento';

    /** @var \Mygento\Base\Model\Logger\Logger */
    protected $logger;

    /** @var \Mygento\Base\Model\LogManager */
    private $logManager;

    /** @var \Magento\Framework\Encryption\Encryptor */
    private $encryptor;

    /**
     * @param \Mygento\Base\Model\LogManager $logManager
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Mygento\Base\Model\LogManager $logManager,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);

        $this->encryptor = $encryptor;
        $this->logManager = $logManager;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $phone
     * @return string
     */
    public function normalizePhone($phone)
    {
        return preg_replace('/\s+/', '', str_replace(['(', ')', '-', ' '], '', trim($phone)));
    }

    /**
     * @param AdapterInterface $conn
     * @param string $table
     * @param array $insertData
     * @param array $updateFields
     * @param bool $clean
     * @param mixed $cleanWhere
     */
    public function batchInsertData(
        AdapterInterface $conn,
        string $table,
        array $insertData,
        array $updateFields = [],
        bool $clean = false,
        $cleanWhere = ''
    ) {
        try {
            $conn->beginTransaction();
            if ($clean) {
                $conn->delete($table, $cleanWhere);
            }
            foreach ($insertData as $row) {
                $conn->insertOnDuplicate(
                    $table,
                    $row,
                    $updateFields
                );
            }
            $conn->commit();
        } catch (\Exception $e) {
            $this->error($e->getMessage(), ['exception' => $e]);
            $conn->rollBack();
        }
    }

    /**
     * @param string $path
     */
    public function decrypt($path)
    {
        return $this->encryptor->decrypt($path);
    }

    /**
     * @return \Monolog\Logger|\Mygento\Base\Model\Logger\Logger
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $type = $this->getGlobalConfig('mygento_base/logger/target');
            $level = (int) $this->getGlobalConfig($this->getLoglevelPath());
            $this->logger = $this->logManager->getLogger($this->code, $type, $level);
        }

        return $this->logger;
    }

    /**
     * @param string $configPath
     * @param string|null $scopeCode
     * @return string
     */
    public function getConfig($configPath, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * @param string $configPath
     * @param string|null $scopeCode
     * @return string
     */
    public function getGlobalConfig($configPath, $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = [])
    {
        $this->getLogger()->alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = [])
    {
        $this->getLogger()->critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = [])
    {
        $this->getLogger()->debug($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = [])
    {
        $this->getLogger()->emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = [])
    {
        $this->getLogger()->error($message, $context);
    }

    /**
     * Info
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = [])
    {
        $this->getLogger()->info($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $this->getLogger()->log($level, $message, $context);
    }

    /**
     * Notice
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = [])
    {
        $this->getLogger()->notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = [])
    {
        $this->getLogger()->warning($message, $context);
    }

    /**
     * @param mixed $text
     * @deprecated
     */
    public function addLog($text)
    {
        if (is_array($text)) {
            // @codingStandardsIgnoreStart
            $text = print_r($text, true);
            // @codingStandardsIgnoreEnd
        }
        $this->getLogger()->log(\Monolog\Logger::DEBUG, $text);
    }

    /**
     * @return string
     */
    protected function getLoglevelPath()
    {
        $code = $this->code === 'mygento' ? 'mygento_base' : $this->code;

        return $code . '/general/loglevel';
    }
}
