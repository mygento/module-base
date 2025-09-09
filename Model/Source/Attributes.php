<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Source;

class Attributes extends AbstractAttributes
{
    /** @var array */
    protected $filterTypesNotEqual = [
        'hidden',
        'multiselect',
        'boolean',
        'date',
        'image',
        'price',
    ];
}
