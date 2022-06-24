<?php

namespace App\Tests\Entity;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserEntityTest extends KernelTestCase
{

    protected $em;
    protected $validator;
    protected $databaseTool;

    public function setUp(): void
    {
        parent::setUp();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function getEntity(): User 
    {
        return (new User())
            ->setEmail("userA1@domaine.mg")
            ->setPassword("0000");
    }

    public function assertHasErrors(
        User $user, 
        int $number_error = 0,
        string $message = ""
    ) 
    {
        /**
         * Deprecated since sf 5.2
         * $errors = self::getContainer()->get("validator")->validate($user);
         */
        $errors = $this->validator->validate($user);
        $this->assertCount($number_error, $errors, $message);
    }

    public function testValidEntity() 
    {
        // number_error error 0
        $this->assertHasErrors(
            $this->getEntity(), 
            0
        );
    }

    public function testNotBlankPassword()
    {
        // number_error error 1
        $this->assertHasErrors(
            $this->getEntity()->setPassword(""), 
            1,
        );
    }

    public function testUniqueEmail()
    {
        // load fixture
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        // number_error error 1
        $this->assertHasErrors(
            $this->getEntity()->setEmail("user1@domaine.mg"), 
            1,
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
