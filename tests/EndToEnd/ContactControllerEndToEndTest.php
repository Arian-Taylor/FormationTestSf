<?php

namespace App\Tests\EndToEnd;

use Symfony\Component\Panther\PantherTestCase;

class ContactEndToEndTest extends PantherTestCase
{
    public function testInvalidUsernameContactPage(): void
    {
        // creation de client qui utilisera Panther
        $client = static::createPantherClient([
            "hostname" => "127.0.0.1",
            "port" => "9999"
        ]);

        // ouvrir la page contact
        $crawler = $client->request('GET', '/contact');

        // Validation de la page
        $this->assertPageTitleSame("Page | contact");
        $this->assertSelectorTextContains('h1', 'Page contact');

        // Submit form contact
        $form = $crawler->selectButton("Valider")->form([
            "username" => "",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]) ;

        $client->submit($form) ;

        // Validation d'erreur
        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid username');
    }

    public function testInvalidEmailContactPage(): void
    {
        // creation de client qui utilisera Panther
        $client = static::createPantherClient([
            "hostname" => "127.0.0.1",
            "port" => "9999"
        ]);

        // ouvrir la page contact
        $crawler = $client->request('GET', '/contact');

        // Validation de la page
        $this->assertPageTitleSame("Page | contact");
        $this->assertSelectorTextContains('h1', 'Page contact');

        // Submit form contact
        $form = $crawler->selectButton("Valider")->form([
            "username" => "Janne Doe",
            "useremail" => "janneDoe",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]) ;

        $client->submit($form) ;

        // Validation d'erreur
        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid useremail');
    }

    public function testInvalidSubjectContactPage(): void
    {
        // creation de client qui utilisera Panther
        $client = static::createPantherClient([
            "hostname" => "127.0.0.1",
            "port" => "9999"
        ]);

        // ouvrir la page contact
        $crawler = $client->request('GET', '/contact');

        // Validation de la page
        $this->assertPageTitleSame("Page | contact");
        $this->assertSelectorTextContains('h1', 'Page contact');

        // Submit form contact
        $form = $crawler->selectButton("Valider")->form([
            "username" => "Janne Doe",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "",
            "message" => "bla bla bla"
        ]) ;

        $client->submit($form) ;

        // Validation d'erreur
        $this->assertSelectorTextContains('.invalid-feedback', 'Invalid subject');
    }

    public function testSendMailContactPage(): void
    {
        // creation de client qui utilisera Panther
        $client = static::createPantherClient([
            "hostname" => "127.0.0.1",
            "port" => "9999"
        ]);

        // ouvrir la page contact
        $crawler = $client->request('GET', '/contact');

        // Validation de la page
        $this->assertPageTitleSame("Page | contact");
        $this->assertSelectorTextContains('h1', 'Page contact');

        // Submit form contact
        $form = $crawler->selectButton("Valider")->form([
            "username" => "Janne Doe",
            "useremail" => "janneDoe@domaine.com",
            "subject" => "sujet de contact",
            "message" => "bla bla bla"
        ]) ;

        $client->submit($form) ;

        // Validation d'erreur
        $this->assertSelectorTextContains('.flash-succes', 'An email sended!');
    }
}
