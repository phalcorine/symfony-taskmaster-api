<?php

namespace App\Dto\Task;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UpdateTaskDto
{
    #[NotBlank]
    #[Length(min: 3, max: 100)]
    private ?string $title;

    #[NotBlank]
    #[Length(min: 3, max: 500)]
    private ?string $content;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return UpdateTaskDto
     */
    public function setTitle(?string $title): UpdateTaskDto
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return UpdateTaskDto
     */
    public function setContent(?string $content): UpdateTaskDto
    {
        $this->content = $content;
        return $this;
    }
}