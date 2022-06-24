<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class MyExceptionSubscriber implements EventSubscriberInterface
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    private $from;
    private $to;

    public function __construct(
        \Swift_Mailer $mailer, 
        string $from,
        string $to
    )
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->to = $to;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => "onException",
        ];
    }

    public function onException (ExceptionEvent $event) 
    {
        $message = (new \Swift_Message("xxx"))
            ->setFrom($this->from)
            ->setTo($this->to)
            ->setBody("
                {$event->getRequest()->getRequestUri()}

                {$event->getThrowable()->getMessage()}

                {$event->getThrowable()->getTraceAsString()}
            ");
        $this->mailer->send($message);
    }
}
