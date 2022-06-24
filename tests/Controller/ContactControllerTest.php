<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testRenderExactPageContact(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame("Page | contact");
        $this->assertSelectorTextContains('h1', 'Page contact');
    }

    public function testSubmitInvalidUsernameContact(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/contact', [
            "username" => "",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]);

        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid username');
    }

    public function testSubmitInvalidUserEmailContact(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/contact', [
            "username" => "Janne Doe",
            "useremail" => "janneDoe",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]);

        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid useremail');
    }

    public function testSubmitInvalidSubjectContact(): void
    {
        $client = static::createClient();

        $crawler = $client->request('POST', '/contact', [
            "username" => "Janne Doe",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "",
            "message" => "bla bla bla"
        ]);

        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid subject');
    }

    public function testSubmitSendMailContact(): void
    {
        $client = static::createClient();

        // enable the profiler
        $client->enableProfiler();

        $crawler = $client->request('POST', '/contact', [
            "username" => "Janne Doe",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]);

        // get swiftmailer collector
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        // list of messages
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('sujet de contact', $message->getSubject());
        $this->assertSame('app@app.com', key($message->getFrom()));
        $this->assertSame('contact@app.com', key($message->getTo()));
    }
}
