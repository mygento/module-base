<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model;

use Magento\Framework\Api\SearchResults;
use Mygento\Base\Api\Data\EventSearchResultsInterface;

class EventSearchResults extends SearchResults implements EventSearchResultsInterface {}
