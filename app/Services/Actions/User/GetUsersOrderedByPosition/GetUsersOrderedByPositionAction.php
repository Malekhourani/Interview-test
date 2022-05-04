<?php

namespace App\Services\Actions\User\GetUsersOrderedByPosition;

use App\Models\User;
use App\Services\ActionFactoryInterface;
use App\Services\ActionInterface;
use App\Services\Actions\User\GetNUsers\GetNextNUsers\GetNextNUsersCommand;
use App\Services\Actions\User\GetNUsers\GetPreviousNUsers\GetPreviousNUsersCommand;
use App\Services\Factories\User\UserActionsEnum;
use Illuminate\Support\Facades\DB;

class GetUsersOrderedByPositionAction implements ActionInterface
{
    private ActionFactoryInterface $actionFactory;

    public function __construct(ActionFactoryInterface $actionFactory)
    {
        $this->actionFactory = $actionFactory;
    }

    /**
     * @param GetUsersOrderedByPositionCommand $command
     * @return 
     */
    public function handle(&$command)
    {
        DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED');

        DB::beginTransaction();


        $startPosition = $this->getStartPosition($command->user);

        $requestedUserInfo = [
            'id' => $command->user->id,
            'username' => $command->user->username,
            'position' => $startPosition,
            'karma_score' => $command->user->karma_score,
            'image_url' => $command->user->image->url
        ];

        if ($command->numberOfRequiredUsersForEachChuck == 0) return collect([$requestedUserInfo]);

        $nextUsers = $this->getNextNUsers($command, $startPosition);


        $command->numberOfRequiredUsersForEachChuck += $nextUsers['numberOfRemainingItems'];


        $previousUsers = $this->getPreviousNUsers($command, $startPosition);

        if ($previousUsers['numberOfRemainingItems'] != 0 && $nextUsers['numberOfRemainingItems'] == 0) {

            $restOfUsers = $this->getRestOfUsers($command, $previousUsers);

            array_push($nextUsers['users'], $restOfUsers['users']);
        }


        DB::commit();

        return array_merge($nextUsers['users'], [$requestedUserInfo], $previousUsers['users']);
    }

    private function getStartPosition(User &$user)
    {
        return $this->actionFactory->factory(UserActionsEnum::GET_USER_POSITION)
            ->handle($user);
    }

    private function getNextNUsers(GetUsersOrderedByPositionCommand &$command, int &$startPosition)
    {
        $getNextNUsersCommand = GetNextNUsersCommand::factory($command->user, $startPosition, $command->numberOfRequiredUsersForEachChuck);

        return $this->actionFactory->factory(UserActionsEnum::GET_NEXT_N_USERS)
            ->handle($getNextNUsersCommand);
    }

    private function getPreviousNUsers(GetUsersOrderedByPositionCommand &$command, int &$startPosition)
    {
        $getPreviousNUsersCommand = GetPreviousNUsersCommand::factory($command->user, $startPosition, $command->numberOfRequiredUsersForEachChuck);

        return $this->actionFactory->factory(UserActionsEnum::GET_PREVIOUS_N_USERS)
            ->handle($getPreviousNUsersCommand);
    }

    private function getRestOfUsers(GetUsersOrderedByPositionCommand &$command, $previousUsers)
    {
        $getTheRestNUsersCommand = GetNextNUsersCommand::factory($command->user, $previousUsers['theNewOffset'], $previousUsers['numberOfRemainingItems']);

        return $this->actionFactory->factory(UserActionsEnum::GET_NEXT_N_USERS)
                ->handle($getTheRestNUsersCommand);
    }
}
