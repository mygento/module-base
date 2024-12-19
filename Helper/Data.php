<?php

/**
 * @author Mygento Team
 * @copyright 2014-2025 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Helper;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Psr\Log\LoggerInterface;

/**
 * Base Data helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper implements LoggerInterface
{
    /** @var string */
    protected $code = 'mygento';

    /** @var \Mygento\Base\Model\Logger\Logger */
    protected $logger;

    /** @var \Mygento\Base\Model\LogManager */
    private $logManager;

    /** @var \Magento\Framework\Encryption\Encryptor */
    private $encryptor;

    public function __construct(
        \Mygento\Base\Model\LogManager $logManager,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Framework\App\Helper\Context $context,
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
        $cleanWhere = '',
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
                    $updateFields,
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
            $scopeCode,
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
            $scopeCode,
        );
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->alert($message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->critical($message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->debug($message, $context);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->emergency($message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->error($message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->info($message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->log($level, $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->getLogger()->notice($message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
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
