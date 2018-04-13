<?php
/**
 * @author Mygento
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class Loglevel implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        $levels = [];
        foreach (\Monolog\Logger::getLevels() as $level => $value) {
            $levels[$value] = $level;
        }
        return $levels;
    }
}
