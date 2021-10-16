<?php

namespace App\Controller\Api;

use App\Dto\Task\CreateTaskDto;
use App\Dto\Task\UpdateTaskDto;
use App\Exception\AppHttpException;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use App\Transformer\TaskTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tasks', name: 'api.tasks.')]
class TasksController extends AbstractController
{
    private TaskRepository $taskRepository;
    private TaskService $taskService;

    public function __construct(
        TaskRepository $taskRepository,
        TaskService $taskService
    )
    {
        $this->taskRepository = $taskRepository;
        $this->taskService = $taskService;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[Route('/list', name: 'list', methods: ['GET'])]
    public function list()
    {
        $tasks = $this->taskRepository->findBy([
            'user'  => $this->getUser()
        ], [
            'updatedAt' => 'DESC'
        ]);

        $response = TaskTransformer::getCollectionResourceAsArray($tasks);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'single', methods: ['GET'])]
    public function single($id)
    {
        $task = $this->taskRepository->findOneBy([
            'user'      => $this->getUser(),
            'id'        => $id
        ]);
        if(is_null($task)) {
            return $this->json([
                'message'   => 'A task with the specified criteria was not found...'
            ], Response::HTTP_NOT_FOUND);
        }

        $response = TaskTransformer::getItemResourceAsArray($task);

        return $this->json($response, Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request)
    {
        $dto = (new CreateTaskDto())
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

        try {

            $task = $this->taskService->create($this->getUser()->getId(), $dto);

            $response = TaskTransformer::getItemResourceAsArray($task);

            return $this->json($response, Response::HTTP_CREATED);

        } catch (AppHttpException $exception) {
            return $this->json([
                'message'   => $exception->getMessage(),
                'errors'    => $exception->getErrors()
            ], $exception->getStatusCode());
        }
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update($id, Request $request)
    {
        $task = $this->taskRepository->findOneBy([
            'id'    => $id,
            'user'  => $this->getUser()
        ]);
        if(is_null($task)) {
            return $this->json([
                'message'   => 'A task with the specified criteria was not found...'
            ], Response::HTTP_NOT_FOUND);
        }

        $dto = (new UpdateTaskDto())
            ->setTitle($request->get('title'))
            ->setContent($request->get('content'));

        try {

            $task = $this->taskService->update($task->getId(), $dto);

            $response = TaskTransformer::getItemResourceAsArray($task);

            return $this->json($response, Response::HTTP_CREATED);

        } catch (AppHttpException $exception) {
            return $this->json([
                'message'   => $exception->getMessage(),
                'errors'    => $exception->getErrors()
            ], $exception->getStatusCode());
        }
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete($id)
    {
        $task = $this->taskRepository->findOneBy([
            'id'    => $id,
            'user'  => $this->getUser()
        ]);
        if(is_null($task)) {
            return $this->json([
                'message'   => 'A task with the specified criteria was not found...'
            ], Response::HTTP_NOT_FOUND);
        }

        try {

            $this->taskService->delete($id);

            return $this->json([
                'message'   => 'Task deleted successfully...'
            ], Response::HTTP_OK);

        } catch (AppHttpException $exception) {
            return $this->json([
                'message'   => $exception->getMessage(),
                'errors'    => $exception->getErrors()
            ], $exception->getStatusCode());
        }
    }

    #[Route('/{id}/toggle-status', name: 'toggle_completion_status', methods: ['GET'])]
    public function toggleCompletedStatus($id)
    {
        $task = $this->taskRepository->findOneBy([
            'id'    => $id,
            'user'  => $this->getUser()
        ]);
        if(is_null($task)) {
            return $this->json([
                'message'   => 'A task with the specified criteria was not found...'
            ], Response::HTTP_NOT_FOUND);
        }

        try {

            $status = $this->taskService->toggleCompletionStatus($id);

            return $this->json([
                'message'   => 'Completion status updated successfully...',
                'status'    => $status
            ], Response::HTTP_OK);

        } catch (AppHttpException $exception) {
            return $this->json([
                'message'   => $exception->getMessage(),
                'errors'    => $exception->getErrors()
            ], $exception->getStatusCode());
        }
    }
}