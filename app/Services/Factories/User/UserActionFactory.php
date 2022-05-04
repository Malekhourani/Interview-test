<?php 

namespace App\Services\Factories\User;

use App\Services\ActionFactoryInterface;
use App\Services\ActionInterface;
use App\Services\Actions\User\GetNUsers\GetNextNUsers\GetNextNUsersAction;
use App\Services\Actions\User\GetNUsers\GetPreviousNUsers\GetPreviousNUsersAction;
use App\Services\Actions\User\GetUserPosition\GetUserPositionAction;
use App\Services\Actions\User\GetUsersOrderedByPosition\GetUsersOrderedByPositionAction;
use Exception;
use Illuminate\Support\Facades\App;

class UserActionFactory implements ActionFactoryInterface
{
    /**
     * @param int $action
     */
    public function factory($action): ActionInterface
    {
        if($action == UserActionsEnum::GET_NEXT_N_USERS)
            return new GetNextNUsersAction();

        if($action == UserActionsEnum::GET_PREVIOUS_N_USERS)
            return new GetPreviousNUsersAction();

        if($action == UserActionsEnum::GET_USER_POSITION)
            return new GetUserPositionAction();

        if($action == UserActionsEnum::GET_USERS_ORDERED_BY_POSITION)
            return new GetUsersOrderedByPositionAction($this);

        throw new Exception('No Matching Action');
    }
}