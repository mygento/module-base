<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\Base\Api\EventRepositoryInterface;

abstract class Event extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_Base::event';

    public function __construct(
        protected readonly EventRepositoryInterface $repository,
        protected readonly Registry $coreRegistry,
        Action\Context $context,
    ) {
        parent::__construct($context);
    }
}
