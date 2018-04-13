<?php
/**
 * @author Mygento
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
        'price'
    ];
}
