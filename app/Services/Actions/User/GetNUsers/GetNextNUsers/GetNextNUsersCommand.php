<?php 

namespace App\Services\Actions\User\GetNUsers\GetNextNUsers;

use App\Models\User;

class GetNextNUsersCommand 
{
    public User $user;
    public int $startPosition;
    public int $numberOfNextNUsers;

    public static function factory(User &$user, int &$startPosition, int &$numberOfNextNUsers): static
    {
        $command = new static;
        $command->user = $user;
        $command->startPosition = $startPosition;
        $command->numberOfNextNUsers = $numberOfNextNUsers;

        return $command;
    }
}