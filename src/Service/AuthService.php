<?php

namespace App\Service;

use App\Dto\Auth\SignupRequestDto;
use App\Entity\User;
use App\Exception\AppHttpException;
use App\Repository\UserRepository;
use App\Validation\ApiRequestValidationErrorModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function signup(SignupRequestDto $dto)
    {
        $violationList = $this->validator->validate($dto);
        if(count($violationList) > 0) {
            $errorResponse = new ApiRequestValidationErrorModel($violationList);

            throw new AppHttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                $errorResponse->getMessage(),
                $errorResponse->getErrors()
            );
        }

        $user = $this->userRepository->findOneBy([
            'email'     => $dto->getEmail()
        ]);
        if($user != null) {
            throw new AppHttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                "A user with the specified email address already exists..."
            );
        }

        try {
            // Create the user
            $user = new User();
            $user->setFullName($dto->getFullName())
                ->setEmail($dto->getEmail())
                ->setPassword($this->passwordHasher->hashPassword($user, $dto->getPassword()));
            $this->entityManager->persist($user);
            $this->entityManager->flush();

        } catch (\Exception $exception) {
            throw new AppHttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }

    }
}