<?php

/**
 * @author Mygento Team
 * @copyright 2014-2020 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class LogTarget implements \Magento\Framework\Data\OptionSourceInterface
{
    /** @var \Mygento\Base\Model\LogManager */
    private $logManager;

    /**
     * @param \Mygento\Base\Model\LogManager $logManager
     */
    public function __construct(\Mygento\Base\Model\LogManager $logManager)
    {
        $this->logManager = $logManager;
    }

    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->logManager->getHandlers() as $handler) {
            $options[$handler] = ucfirst($handler);
        }

        return $options;
    }
}
