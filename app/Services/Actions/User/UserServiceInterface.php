<?php 

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Collection;

interface UserServiceInterface
{
    function getUsersOrderedByPosition(User $user, int $numberOfRequiredUsersForEachChuck): array;
}