<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserList() as $data) {
            $user = new User($data['username'], $data['roles']);
            $encodedPassword = $this->passwordEncoder->encodePassword($user, $data['password']);
            $user->setPassword($encodedPassword);
            $this->userRepository->save($user);
        }

        $this->userRepository->flush();
    }

    /**
     * @return User[]
     */
    private function getUserList(): array
    {
        return [
            [
                'username' =>'foo',
                'roles' => [],
                'password' => 'BAR'
            ],
        ];
    }
}
