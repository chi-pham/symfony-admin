<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\ChangePassword;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotBlank()
     */
    public string $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public string $password = '';

    public function __construct(string $id, string $password)
    {
        $this->id = $id;
        $this->password = $password;
    }
}
