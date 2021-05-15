<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('pl_PL');
        $emails = [
            ['email' => 'lookasziebice@gmail.com', 'roles' => 'ROLE_ADMIN'],
            ['email' => 'admin@admin.pl', 'roles' => 'ROLE_ADMIN'],
            ['email' => 'user@user.pl', 'roles' => 'ROLE_USER'],
        ];

        foreach ($emails as $email) {
            $user = new User();
            $user->setEmail($email['email']);
            $user->setPhoneNumber($faker->phoneNumber);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $password = $this->encoder->encodePassword($user, 'Pa$$word');
            $user->setPassword($password);
            $user->setRoles([$email['roles']]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
