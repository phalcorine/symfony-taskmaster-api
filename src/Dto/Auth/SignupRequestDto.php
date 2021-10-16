<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class SignupRequestDto
{
    #[Length(min: 3, max: 100, normalizer: 'trim')]
    private ?string $fullName;

    #[Email]
    private ?string $email;

    #[NotCompromisedPassword]
    private ?string $password;

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param string|null $fullName
     * @return SignupRequestDto
     */
    public function setFullName(?string $fullName): SignupRequestDto
    {
        $this->fullName = $fullName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return SignupRequestDto
     */
    public function setEmail(?string $email): SignupRequestDto
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return SignupRequestDto
     */
    public function setPassword(?string $password): SignupRequestDto
    {
        $this->password = $password;
        return $this;
    }
}