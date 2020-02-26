<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

class HomepageController extends AbstractController
{
    /**
     * @var PublicKeyCredentialSourceRepository
     */
    private $publicKeyCredentialSourceRepository;

    public function __construct(PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository)
    {
        $this->publicKeyCredentialSourceRepository = $publicKeyCredentialSourceRepository;
    }

    /**
     * @Route(name="homepage", path="/")
     */
    public function __invoke(): Response
    {
        $user = $this->getUser();
        $authenticatorList = $user instanceof PublicKeyCredentialUserEntity ? $this->publicKeyCredentialSourceRepository->findAllForUserEntity($user) : [];
        return $this->render(
            'homepage/homepage.html.twig',
            [
                'authenticatorList' => $authenticatorList,
            ]
            );
    }
}
