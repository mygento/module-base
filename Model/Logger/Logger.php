<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Logger;

class Logger extends \Magento\Framework\Logger\Monolog
{
    public function __construct(
        string $name,
    ) {
        parent::__construct(
            $name,
            [],
        );
    }
}
