<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Logger;

class Logger extends \Magento\Framework\Logger\Monolog
{
    /**
     *
     * @param string $name
     */
    public function __construct(
        $name
    ) {
        parent::__construct(
            $name,
            []
        );
    }
}
