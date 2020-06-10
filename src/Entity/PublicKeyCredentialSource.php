<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webauthn\PublicKeyCredentialSource as BasePublicKeyCredentialSource;

/**
 * @ORM\Table(name="public_key_credential_sources")
 * @ORM\Entity(repositoryClass="App\Repository\PublicKeyCredentialSourceRepository")
 */
class PublicKeyCredentialSource extends BasePublicKeyCredentialSource
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): string
    {
        return $this->id;
    }
}