<?php

namespace Tests\Unit\Domain\User\DataTransfer;

use App\Domain\User\DataTransfer\UserDataTransfer;
use App\Domain\User\Models\User;
use Tests\TestCase;

class UserDataTransferTest extends TestCase
{
    public function testMustReturnUserDataTransfer()
    {
        $user =  User::factory()->make();
        $userDataTransfer = new UserDataTransfer($user);

        $this->assertInstanceOf(UserDataTransfer::class, $userDataTransfer);
        $this->assertEquals($user->id, $userDataTransfer->id);
        $this->assertEquals($user->name, $userDataTransfer->name);
        $this->assertEquals($user->email, $userDataTransfer->email);
    }

    public function testMustReturnResponseDataForUser()
    {
        $user =  User::factory()->make();
        $userDataTransfer = new UserDataTransfer($user);

        $this->assertEquals($user->toArray(), $userDataTransfer->fromResponse());
    }
}
