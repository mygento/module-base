<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Framework\Model\AbstractModel;

class Event extends AbstractModel implements \Mygento\Base\Api\Data\EventInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mygento\Base\Model\ResourceModel\Event::class);
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param int $id
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get instance
     * @return string|null
     */
    public function getInstance()
    {
        return $this->getData(self::INSTANCE);
    }

    /**
     * Set instance
     * @param string $instance
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setInstance($instance)
    {
        return $this->setData(self::INSTANCE, $instance);
    }

    /**
     * Get channel
     * @return string|null
     */
    public function getChannel()
    {
        return $this->getData(self::CHANNEL);
    }

    /**
     * Set channel
     * @param string $channel
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setChannel($channel)
    {
        return $this->setData(self::CHANNEL, $channel);
    }

    /**
     * Get level
     * @return int|null
     */
    public function getLevel()
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * Set level
     * @param int $level
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setLevel($level)
    {
        return $this->setData(self::LEVEL, $level);
    }

    /**
     * Get message
     * @return string|null
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set message
     * @param string $message
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Get logged at
     * @return string|null
     */
    public function getLoggedAt()
    {
        return $this->getData(self::LOGGED_AT);
    }

    /**
     * Set logged at
     * @param string $loggedAt
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setLoggedAt($loggedAt)
    {
        return $this->setData(self::LOGGED_AT, $loggedAt);
    }

    /**
     * Get context
     * @return string|null
     */
    public function getContext()
    {
        return $this->getData(self::CONTEXT);
    }

    /**
     * Set context
     * @param string $context
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setContext($context)
    {
        return $this->setData(self::CONTEXT, $context);
    }

    /**
     * Get extra
     * @return string|null
     */
    public function getExtra()
    {
        return $this->getData(self::EXTRA);
    }

    /**
     * Set extra
     * @param string $extra
     * @return \Mygento\Base\Api\Data\EventInterface
     */
    public function setExtra($extra)
    {
        return $this->setData(self::EXTRA, $extra);
    }
}
