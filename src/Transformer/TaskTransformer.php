<?php

namespace App\Transformer;

use App\Entity\Task;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract
{
    #[Pure]
    #[ArrayShape(['id' => "int|null", 'title' => "null|string", 'content' => "null|string", 'isCompleted' => "bool|null", 'createdAt' => "mixed", 'updatedAt' => "mixed"])]
    public function transform(Task $task)
    {
        return [
            'id'            => $task->getId(),
            'title'         => $task->getTitle(),
            'content'       => $task->getContent(),
            'isCompleted'   => $task->getIsCompleted(),
            'createdAt'     => $task->getCreatedAt(),
            'updatedAt'     => $task->getUpdatedAt()
        ];
    }

    public static function getCollectionResourceAsArray(iterable $tasks)
    {
        $resource = new Collection($tasks, new TaskTransformer());

        $fractal = new Manager();

        return $fractal->createData($resource)->toArray();
    }

    public static function getItemResourceAsArray(Task $task)
    {
        $resource = new Item($task, new TaskTransformer());

        $fractal = new Manager();

        return $fractal->createData($resource)->toArray();
    }
}