<?php

namespace App\Event;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;

class CronRunningEvent implements EventSubscriberInterface
{
    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if ($event->isWorkerIdle()) {
            $event->getWorker()->stop();
        }
    }

    /**
     * @return array<string>
     */
    public static function getSubscribedEvents()
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}