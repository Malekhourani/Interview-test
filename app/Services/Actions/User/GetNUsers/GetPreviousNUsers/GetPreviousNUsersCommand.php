<?php 

namespace App\Services\Actions\User\GetNUsers\GetPreviousNUsers;

use App\Models\User;

class GetPreviousNUsersCommand
{
    public User $user;
    public int $startPosition; 
    public int $numberOfPreviousNUsers;

    public static function factory(User &$user, int &$startPosition, int &$numberOfPreviousNUsers)
    {
        $command = new static;
        $command->user = $user;
        $command->startPosition = $startPosition;
        $command->numberOfPreviousNUsers = $numberOfPreviousNUsers;

        return $command;
    }
}