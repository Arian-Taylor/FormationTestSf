<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10 ; $i++) {
            $user = (new User())
                ->setEmail("user$i@domaine.mg")
                ->setPassword("0000");

            $manager->persist($user);
        }

        $manager->flush();
    }
}
