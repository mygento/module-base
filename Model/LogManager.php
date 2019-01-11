<?php

/**
 * @author Mygento Team
 * @copyright 2014-2019 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

class LogManager
{
    /** @var \Mygento\Base\Model\Logger\LoggerFactory */
    private $loggerFactory;

    /** @var array */
    private $handlers;

    /**
     * @param \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory
     * @param array $handlers
     */
    public function __construct(
        \Mygento\Base\Model\Logger\LoggerFactory $loggerFactory,
        $handlers = []
    ) {
        $this->handlers = $handlers;
        $this->loggerFactory = $loggerFactory;
    }

    /**
     *
     * @param string $name
     * @param string $type
     * @param int $level
     * @return \Monolog\Logger
     */
    public function getLogger(
        $name,
        $type = 'file',
        $level = \Monolog\Logger::DEBUG
    ) {
        if (isset($this->loggers[$name])) {
            return $this->loggers[$name];
        }
        if (!isset($this->handlers[$type])) {
            //TODO throw exception
            return null;
        }
        $logger = $this->loggerFactory->create(['name' => $name]);
        switch ($type) {
            case 'file':
                $handler = $this->handlers[$type]->create(['name' => $name]);
                $handler->setLevel($level);
                $logger->pushHandler($handler);
                break;
            default:
                $handler = $this->handlers[$type]->create();
                $handler->setLevel($level);
                $logger->pushHandler($handler);
        }
        $this->loggers[$name] = $logger;
        return $this->loggers[$name];
    }

    /**
     * Get Handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        return array_keys($this->handlers);
    }
}
