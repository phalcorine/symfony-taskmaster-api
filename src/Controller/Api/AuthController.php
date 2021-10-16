<?php

namespace App\Controller\Api;

use App\Dto\Auth\SignupRequestDto;
use App\Exception\AppHttpException;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/auth', name: 'api.auth.')]
class AuthController extends AbstractController
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/signup', name: 'signup')]
    public function signup(Request $request)
    {
        $signupDto = (new SignupRequestDto())
            ->setFullName($request->get('fullName'))
            ->setEmail($request->get('email'))
            ->setPassword($request->get('password'));

        try {
            $this->authService->signup($signupDto);

            return $this->json([
                'message'   => 'Signup successful...'
            ], Response::HTTP_CREATED);
        } catch (AppHttpException $exception) {
            return $this->json([
                'message'   => $exception->getMessage(),
                'errors'    => $exception->getErrors()
            ], $exception->getStatusCode());
        }
    }
}