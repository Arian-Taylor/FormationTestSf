<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }

    public function testIndex()
    {
        /**
         * add all your fixtures classes that implement
         * Doctrine\Common\DataFixtures\FixtureInterface
         */
        $this->databaseTool->loadFixtures([UserFixtures::class]);

        // Recuperation users
        $users = $this->em->getRepository(User::class)->findAll();

        // Validation
        $this->assertCount(10, $users);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
