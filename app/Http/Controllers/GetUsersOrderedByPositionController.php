<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetUsersOrderedByPositionFormRequest;
use App\Http\Resources\GetUserOrderedByPositionResource;
use App\Models\User;
use App\Services\ActionFactoryInterface;
use App\Services\Actions\User\GetUsersOrderedByPosition\GetUsersOrderedByPositionAction;
use App\Services\Actions\User\GetUsersOrderedByPosition\GetUsersOrderedByPositionCommand;
use App\Services\Factories\User\UserActionsEnum;

class GetUsersOrderedByPositionController extends Controller
{
    private GetUsersOrderedByPositionAction $getUsersOrderedByPositionAction;

    public function __construct(ActionFactoryInterface $actionFactroy)
    {
        $this->getUsersOrderedByPositionAction = $actionFactroy->factory(UserActionsEnum::GET_USERS_ORDERED_BY_POSITION);
    }

    public function __invoke(User $user, GetUsersOrderedByPositionFormRequest $request)
    {
        $request->validated();

        $totalNumberOfRequiredUsersToShow = $request->query('n', 5);

        $numberOfRequiredUsersForEachChuck = floor($totalNumberOfRequiredUsersToShow / 2);

        $getUsersOrderedByPositionCommand = GetUsersOrderedByPositionCommand::factory($user, $numberOfRequiredUsersForEachChuck);

        $result = $this->getUsersOrderedByPositionAction->handle($getUsersOrderedByPositionCommand);

        return GetUserOrderedByPositionResource::collection($result);
    }
}
