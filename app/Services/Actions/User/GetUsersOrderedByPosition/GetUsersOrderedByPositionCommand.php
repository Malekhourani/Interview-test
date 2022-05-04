<?php

namespace App\Services\Actions\User\GetUsersOrderedByPosition;

use App\Models\User;

class GetUsersOrderedByPositionCommand 
{
    public User $user;
    public int $numberOfRequiredUsersForEachChuck;

    public static function factory(User &$user, int &$numberOfRequiredUsersForEachChuck)
    {
        $command = new static;
        $command->user = $user;
        $command->numberOfRequiredUsersForEachChuck = $numberOfRequiredUsersForEachChuck;
        
        return $command;
    }
}