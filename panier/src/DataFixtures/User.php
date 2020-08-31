<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class User extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new \App\Entity\User();
        $user->setEmail('admin@admin.fr');
        $user->setNom('admin');
        $user->setPrenom('admin');
        $password = $this->encoder->encodePassword($user, "admin");
        $user->setPassword($password);
        $user->setPhone('0109080705');
        $user->setRoles(["ROLE_ADMIN"]);

        $manager->persist($user);
        $manager->flush();
    }
}
