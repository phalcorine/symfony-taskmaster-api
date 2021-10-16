<?php

namespace App\Service;

use App\Dto\Task\CreateTaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Entity\Task;
use App\Entity\User;
use App\Exception\AppHttpException;
use App\Validation\ApiRequestValidationErrorModel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function create(int $userId, CreateTaskDto $dto)
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

        try {
            /** @var User $user */
            $user = $this->entityManager->getReference('App:User', $userId);

            $task = (new Task())
                ->setTitle($dto->getTitle())
                ->setContent($dto->getContent())
                ->setUser($user);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task;

        } catch (\Exception $exception) {
            throw new AppHttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }
    }

    public function update(int $taskId, UpdateTaskDto $dto)
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

        try {
            /** @var Task $task */
            $task = $this->entityManager->getReference('App:Task', $taskId);

            $task->setTitle($dto->getTitle() ?? $task->getTitle())
                ->setContent($dto->getContent() ?? $task->getContent());

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task;
        } catch (\Exception $exception) {
            throw new AppHttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }
    }

    public function delete(int $taskId)
    {
        try {

            /** @var Task $task */
            $task = $this->entityManager->getReference('App:Task', $taskId);

            $this->entityManager->remove($task);
            $this->entityManager->flush();

        } catch (\Exception $exception) {
            throw new AppHttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }
    }

    public function toggleCompletionStatus($taskId)
    {
        try {

            /** @var Task $task */
            $task = $this->entityManager->getReference('App:Task', $taskId);

            $task->setIsCompleted(!$task->getIsCompleted());

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $task->getIsCompleted();

        } catch (\Exception $exception) {
            throw new AppHttpException(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }
    }
}