<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepository as PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

final class PublicKeyCredentialUserEntityRepository implements PublicKeyCredentialUserEntityRepositoryInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        return $this->findPublicKeyCredentialUserEntity([
            'username' => $username,
        ]);
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        return $this->findPublicKeyCredentialUserEntity([
            'webauthnId' => $userHandle,
        ]);
    }

    public function createUserEntity(string $username, string $displayName, ?string $icon): PublicKeyCredentialUserEntity
    {
        return new PublicKeyCredentialUserEntity(
            $username,
            uniqid('', true),
            $displayName,
            $icon
        );
    }

    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
        $user = new User(
            $userEntity->getName(),
            [],
            $userEntity->getDisplayName(),
            $userEntity->getId()
        );
        $user->setIcon($userEntity->getIcon());

        $encodedPassword = $this->passwordEncoder->encodePassword($user, random_bytes(64));
        $user->setPassword($encodedPassword);

        $this->userRepository->save($user);
    }

    private function findPublicKeyCredentialUserEntity(array $criteria): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy($criteria);
        if ($user === null || $user->getWebauthnId()) {
            return null;
        }

        return new PublicKeyCredentialUserEntity(
            $user->getUsername(),
            $user->getWebauthnId(),
            $user->getUsername()
        );
    }
}