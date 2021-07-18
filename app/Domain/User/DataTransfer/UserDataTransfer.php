<?php

namespace App\Domain\User\DataTransfer;

use App\Core\DataTransfers\DataTransfer;
use App\Domain\User\Models\User;
use PharIo\Manifest\Email;

class UserDataTransfer extends DataTransfer
{
    protected int $id;
    protected string $name;
    protected Email $email;
    protected string $document;
    protected int $user_type_id;

    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = new Email($user->email);
        $this->document = $user->document;
        $this->user_type_id = $user->user_type_id;
    }

    public function fromResponse(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->getEmail(),
            'user_type_id' => $this->user_type_id,
            'document' => $this->document,
        ];
    }

    protected function getEmail(): string
    {
        return $this->email->asString();
    }
}
