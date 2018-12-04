<?php

/**
 * @author Mygento Team
 * @copyright 2014-2018 Mygento (https://www.mygento.ru)
 * @package Mygento_Base
 */

namespace Mygento\Base\Model\Logger;

class Database extends \Monolog\Handler\AbstractProcessingHandler
{

    /**
     * @var \Mygento\Base\Model\EventRepository
     */
    private $eventRepository;

    /**
     * @var \Mygento\Base\Model\EventRepository
     */
    private $eventFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Mygento\Base\Model\EventRepository $eventRepository
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Mygento\Base\Model\EventFactory $eventFactory
     * @param int|string $level
     * @param bool $bubble
     */
    public function __construct(
        \Mygento\Base\Model\EventRepository $eventRepository,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Mygento\Base\Model\EventFactory $eventFactory,
        $level = \Monolog\Logger::DEBUG,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->eventRepository = $eventRepository;
        $this->serializer = $serializer;
        $this->eventFactory = $eventFactory;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        $event = $this->eventFactory->create();
        $event->setInstance(gethostname());
        $event->setLevel($record['level']);
        $event->setChannel($record['channel']);
        $event->setMessage($record['message']);

        //serialize
        $event->setContext($this->serialize($record['context']));
        $event->setExtra($this->serialize($record['extra']));

        $this->eventRepository->save($event);
    }

    /**
     * Serialize field
     *
     * @param mixed $field
     * @return string|null
     */
    private function serialize($field)
    {
        if (empty($field)) {
            return null;
        }
        return $this->serializer->serialize($field);
    }
}
