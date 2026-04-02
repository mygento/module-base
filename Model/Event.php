<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\Base\Api\Data\EventInterface;

class Event extends AbstractModel implements EventInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_base_event';

    /**
     * Get id
     */
    public function getId(): ?int
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param int $id
     */
    public function setId($id): self
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get instance
     */
    public function getInstance(): string
    {
        return $this->getData(self::INSTANCE);
    }

    /**
     * Set instance
     */
    public function setInstance(string $instance): self
    {
        return $this->setData(self::INSTANCE, $instance);
    }

    /**
     * Get channel
     */
    public function getChannel(): string
    {
        return $this->getData(self::CHANNEL);
    }

    /**
     * Set channel
     */
    public function setChannel(string $channel): self
    {
        return $this->setData(self::CHANNEL, $channel);
    }

    /**
     * Get level
     */
    public function getLevel(): int
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * Set level
     */
    public function setLevel(int $level): self
    {
        return $this->setData(self::LEVEL, $level);
    }

    /**
     * Get message
     */
    public function getMessage(): string
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set message
     */
    public function setMessage(string $message): self
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Get logged at
     */
    public function getLoggedAt(): string
    {
        return $this->getData(self::LOGGED_AT);
    }

    /**
     * Set logged at
     */
    public function setLoggedAt(string $loggedAt): self
    {
        return $this->setData(self::LOGGED_AT, $loggedAt);
    }

    /**
     * Get context
     */
    public function getContext(): ?string
    {
        return $this->getData(self::CONTEXT);
    }

    /**
     * Set context
     */
    public function setContext(?string $context): self
    {
        return $this->setData(self::CONTEXT, $context);
    }

    /**
     * Get extra
     */
    public function getExtra(): ?string
    {
        return $this->getData(self::EXTRA);
    }

    /**
     * Set extra
     */
    public function setExtra(?string $extra): self
    {
        return $this->setData(self::EXTRA, $extra);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Event::class);
    }
}
