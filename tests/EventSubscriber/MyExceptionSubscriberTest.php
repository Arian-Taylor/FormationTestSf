<?php
namespace App\Test\EventSubscriber;

use App\EventSubscriber\MyExceptionSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class MyExceptionSubscriberTest extends TestCase {

	/**
	 * testing if ExceptionEvent existed event of MyExceptionSubscriber 
	 */
	public function testEventSubscription () {
	    $this->assertArrayHasKey(
	    	ExceptionEvent::class, 
	    	MyExceptionSubscriber::getSubscribedEvents()
	    );
	} 

	public function testOnExceptionSendEmail() {

		// On mock les services dont dépend la classe
		$mailer = $this->getMockBuilder(\Swift_Mailer::class)
			->disableOriginalConstructor()
			->getMock();

		// On appel une seul fois la methode send
		$mailer->expects($this->once())
			->method('send');

		// dispatch
		$this->dispatch($mailer);
	}

	public function testOnExceptionSendEmailToTheAdmin() {
		// On mock les services dont dépend la classe
		$mailer = $this->getMockBuilder(\Swift_Mailer::class)
			->disableOriginalConstructor()
			->getMock();

		// on appel une seul fois la methode send with callback
		$mailer->expects($this->once())
			->method('send')
			->with(
				$this->callback(
					function(\Swift_Message $message) {
						return 
							array_key_exists("from@domain.com", $message->getFrom()) &&
							array_key_exists("to@domain.com", $message->getTo());
					}
				)
			);

		// dispatch
		$this->dispatch($mailer);
	}


	public function testOnExceptionSendEmailWithTheTrace() {
		// On mock les services dont dépend la classe
		$mailer = $this->getMockBuilder(\Swift_Mailer::class)
			->disableOriginalConstructor()
			->getMock();

		// on appel une seul fois la methode send with callback
		$mailer->expects($this->once())
			->method('send')
			->with(
				$this->callback(
					function(\Swift_Message $message) {
						return 
							strpos($message->getBody(), "MyExceptionSubscriberTest") && 
							strpos($message->getBody(), "Hello World");
					}
				)
			);

		// dispatch
		$this->dispatch($mailer);
	}

	private function dispatch($mailer) {
		// On crée notre my_subscriber
		$my_subscriber = new MyExceptionSubscriber(
			$mailer,
			"from@domain.com",
			"to@domain.com"
		);

		// On crée notre évènement
		$kernel = $this->getMockBuilder(KernelInterface::class)->getMock();
		$event = new ExceptionEvent(
			$kernel, 
			new Request(), 
			1, 
			new \Exception('Hello World')
		);

		// On dispatch notre évènement en ajoutant notre my_subscriber dans le dispatcher.
		$dispatcher = new EventDispatcher();
		$dispatcher->addSubscriber($my_subscriber);
		$dispatcher->dispatch($event);

	}

}