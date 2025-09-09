<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Logger;

use Magento\Framework\Serialize\SerializerInterface;
use Monolog\Level;
use Monolog\LogRecord;
use Mygento\Base\Api\Data\EventInterfaceFactory;
use Mygento\Base\Api\EventRepositoryInterface;

class Database extends \Monolog\Handler\AbstractProcessingHandler
{
    public function __construct(
        private EventRepositoryInterface $eventRepository,
        private SerializerInterface $serializer,
        private EventInterfaceFactory $eventFactory,
        Level $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
        $this->eventRepository = $eventRepository;
        $this->serializer = $serializer;
        $this->eventFactory = $eventFactory;
    }

    /**
     * Writes the record down to the log of the implementing handler
     */
    protected function write(LogRecord $record): void
    {
        $event = $this->eventFactory->create();
        $event->setInstance(gethostname());
        $event->setLevel($record->level);
        $event->setChannel($record->channel);
        $event->setMessage($record->message);

        //serialize
        $event->setContext($this->serialize($record->context));
        $event->setExtra($this->serialize($record->extra));

        $this->eventRepository->save($event);
    }

    /**
     * Serialize field
     *
     * @param mixed $field
     * @return bool|string|null
     */
    private function serialize($field)
    {
        if (empty($field)) {
            return null;
        }

        return $this->serializer->serialize($field);
    }
}
