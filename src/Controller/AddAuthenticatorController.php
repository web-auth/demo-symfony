<?php

declare(strict_types=1);

namespace App\Controller;

use Assert\Assertion;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\Bundle\Security\Storage\SessionStorage;
use Webauthn\Bundle\Service\AuthenticatorRegistrationHelper;
use Webauthn\Bundle\Service\PublicKeyCredentialCreationOptionsFactory;
use Webauthn\PublicKeyCredentialLoader;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialUserEntity;

class AddAuthenticatorController extends AbstractController
{
    /**
     * @var AuthenticatorRegistrationHelper
     */
    private $helper;

    public function __construct(HttpMessageFactoryInterface $httpMessageFactory, ValidatorInterface $validator, SerializerInterface $serializer, PublicKeyCredentialCreationOptionsFactory $publicKeyCredentialCreationOptionsFactory, PublicKeyCredentialSourceRepository $publicKeyCredentialSourceRepository, PublicKeyCredentialLoader $publicKeyCredentialLoader, AuthenticatorAttestationResponseValidator $authenticatorAttestationResponseValidator, SessionStorage $optionsStorage)
    {
        $this->helper = new AuthenticatorRegistrationHelper(
            $publicKeyCredentialCreationOptionsFactory,
            $serializer,
            $validator,
            $publicKeyCredentialSourceRepository,
            $publicKeyCredentialLoader,
            $authenticatorAttestationResponseValidator,
            $optionsStorage,
            $httpMessageFactory
        );
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @Route(name="add_authenticator_options", path="/add/device/options", schemes={"https"}, methods={"POST"})
     */
    public function getOptions(Request $request): Response
    {
        try {
            $userEntity = $this->getUser();
            Assertion::isInstanceOf($userEntity, PublicKeyCredentialUserEntity::class, 'Invalid user');
            $publicKeyCredentialCreationOptions = $this->helper->generateOptions($userEntity, $request);

            return $this->json($publicKeyCredentialCreationOptions);
        } catch (Throwable $e) {
            return $this->json([
                'status' => 'failed',
                'errorMessage' => 'Invalid request',
            ], 400);
        }

    }

    /**
     * @param Request $request
     * @return Response
     * @Route(name="add_authenticator", path="/add/device", schemes={"https"}, methods={"POST"})
     */
    public function addAuthenticator(Request $request): Response
    {
        try {
            $userEntity = $this->getUser();
            Assertion::isInstanceOf($userEntity, PublicKeyCredentialUserEntity::class, 'Invalid user');
            $this->helper ->validateResponse($userEntity, $request);
            return $this->json([
                'status' => 'ok',
                'errorMessage' => '',
            ]);
        } catch (Throwable $e) {
            return $this->json([
                'status' => 'failed',
                'errorMessage' => 'Invalid request',
            ], 400);
        }
    }
}
