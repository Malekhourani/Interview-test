<?php 

namespace App\Services\Actions\User\GetUserPosition;

use App\Models\User;
use App\Services\ActionInterface;

class GetUserPositionAction implements ActionInterface
{
    /**
     * @param User $command
     * @return int $userPosition
     */
    public function handle(&$command)
    {
        return User::whereKeyNot($command->id)
            ->where('karma_score', '>=', $command->karma_score)
            ->select('id')
            ->take(5)
            ->count() + 1;
    }
}