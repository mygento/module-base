<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Logger;

use Magento\Framework\Filesystem\DriverInterface;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    public function __construct(
        string $name,
        DriverInterface $filesystem,
        ?string $filePath = null,
    ) {
        $this->fileName = DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR .
            'log' . DIRECTORY_SEPARATOR . $name . '.log';
        parent::__construct($filesystem, $filePath);
    }
}
