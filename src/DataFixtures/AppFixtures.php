<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
         $user = new User();
         $user->setEmail("le-berre.jeremy@orange.fr");
         $user->setFirstname("Le Berre");
         $user->setLastname("Jérémy");
         $user->setRoles(['ROLE_ADMIN']);
         $password = $this->encoder->encodePassword($user, "Sephirot@29");
         $user->setPassword($password);
         $manager->persist($user);

        $manager->flush();
    }
}
