<?php

namespace App\Domain\User\DataTransfer;

use App\Core\DataTransfers\DataTransfer;
use PharIo\Manifest\Email;

class CreateUserDataTransfer extends DataTransfer
{
    protected string $name;
    protected Email $email;
    protected string $document;
    protected int $user_type_id;
    protected string $password;

    public function __construct(
        string $name,
        Email $email,
        string $document,
        int $user_type_id,
        string $password
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->user_type_id = $user_type_id;
        $this->password = $password;
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['name'],
            new Email($data['email']),
            $data['document'],
            $data['user_type_id'],
            $data['password'],
        );
    }

    protected function getEmail(): string
    {
        return $this->email->asString();
    }
}
