<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Api\Data;

interface EventInterface
{
    public const ID = 'id';
    public const INSTANCE = 'instance';
    public const CHANNEL = 'channel';
    public const LEVEL = 'level';
    public const MESSAGE = 'message';
    public const LOGGED_AT = 'logged_at';
    public const CONTEXT = 'context';
    public const EXTRA = 'extra';

    /**
     * Get id
     */
    public function getId(): ?int;

    /**
     * Set id
     * @param int $id
     */
    public function setId($id): self;

    /**
     * Get instance
     */
    public function getInstance(): string;

    /**
     * Set instance
     */
    public function setInstance(string $instance): self;

    /**
     * Get channel
     */
    public function getChannel(): string;

    /**
     * Set channel
     */
    public function setChannel(string $channel): self;

    /**
     * Get level
     */
    public function getLevel(): int;

    /**
     * Set level
     */
    public function setLevel(int $level): self;

    /**
     * Get message
     */
    public function getMessage(): string;

    /**
     * Set message
     */
    public function setMessage(string $message): self;

    /**
     * Get logged at
     */
    public function getLoggedAt(): string;

    /**
     * Set logged at
     */
    public function setLoggedAt(string $loggedAt): self;

    /**
     * Get context
     */
    public function getContext(): ?string;

    /**
     * Set context
     */
    public function setContext(?string $context): self;

    /**
     * Get extra
     */
    public function getExtra(): ?string;

    /**
     * Set extra
     */
    public function setExtra(?string $extra): self;
}
