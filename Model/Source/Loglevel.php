<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

use Monolog\Level;

class Loglevel implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array<int,string>
     */
    public function toOptionArray(): array
    {
        $levels = [];
        $list = array_combine(Level::NAMES, Level::VALUES);
        foreach ($list as $level => $value) {
            $levels[$value] = $level;
        }

        return $levels;
    }
}
