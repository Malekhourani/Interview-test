<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\User\UserServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    public function getUsersOrderedByPosition(User $user, int $numberOfRequiredUsersForEachChuck): array
    {
        DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ UNCOMMITTED');

        DB::beginTransaction();

        $startRank = $this->getUserRank($user);

        $requestedUserInfo = [
            'id' => $user->id,
            'username' => $user->username,
            'position' => $startRank,
            'karma_score' => $user->karma_score,
            'image_url' => $user->image->url
        ];

        if ($numberOfRequiredUsersForEachChuck == 0) return collect([$requestedUserInfo]);

        $nextUsers = $this->getNextNUsers($user, $startRank, $numberOfRequiredUsersForEachChuck);

        $numberOfRequiredUsersForEachChuck += $nextUsers['numberOfRemainingItems'];

        $previousUsers = $this->getPreviousNUsers($user, $startRank, $numberOfRequiredUsersForEachChuck);

        if ($previousUsers['numberOfRemainingItems'] != 0 && $nextUsers['numberOfRemainingItems'] == 0) {
            $restOfUsers = $this->getNextNUsers($user, $previousUsers['theNewOffset'], $previousUsers['numberOfRemainingItems']);
            array_push($nextUsers['users'], $restOfUsers['users']);
        }

        DB::commit();

        return array_merge($nextUsers['users'], [$requestedUserInfo], $previousUsers['users']);
    }

    private function getUserRank(User &$user): int
    {
        return User::whereKeyNot($user->id)
            ->where('karma_score', '>=', $user->karma_score)
            ->select('id')
            ->take(5)
            ->count() + 1;
    }

    private function getNextNUsers(User &$user, int &$startPosition, int &$numberOfNextNUsers = 2): array
    {
        $data = DB::table('users')
            ->selectRaw('t1.id, t1.username, t1.url as image_url, t1.karma_score, @rank := @rank - 1 AS position')
            ->fromRaw("( 
                                SELECT users.id, users.username, users.karma_score, images.url 
                                FROM `users` 
                                LEFT JOIN images 
                                    ON images.id = users.image_id 
                                WHERE users.karma_score >= $user->karma_score 
                                AND users.id <> $user->id 
                                ORDER BY karma_score ASC
                                LIMIT $numberOfNextNUsers
                            ) as t1, 
                            (
                                SELECT @rank := $startPosition
                            ) AS t2
                            ORDER BY karma_score DESC")
            ->get();

        return $this->formatResult($data, $numberOfNextNUsers, $startPosition);
    }

    private function getPreviousNUsers(User &$user, int &$startPosition, int &$numberOfPreviousNUsers = 2): array
    {
        $data = DB::table('users')->selectRaw('t1.id, t1.username, t1.url as image_url, t1.karma_score, @rank := @rank + 1 AS position')
            ->fromRaw("( 
                                SELECT users.id, users.username, users.karma_score, images.url 
                                FROM `users`
                                LEFT JOIN images    
                                    ON images.id = users.image_id
                                WHERE users.karma_score < $user->karma_score
                                AND users.id <> $user->id  
                                ORDER BY karma_score DESC
                                LIMIT $numberOfPreviousNUsers
                            ) as t1, 
                            (
                                SELECT @rank := $startPosition
                            ) AS t2")
            ->get();

        return $this->formatResult($data, $numberOfPreviousNUsers, $startPosition);
    }

    private function formatResult(Collection &$data, int &$numberOfRequiredUsers, int &$startPosition)
    {
        if ($data->isEmpty())
            return $this->returnRequestedDataWithMetadata($data->toArray(), $numberOfPreviousNUsers, $startPosition);

        $data = $data->map(function ($value, $key) {
            return (array) $value;
        })
            ->toArray();

        return $this->returnRequestedDataWithMetadata($data, $numberOfPreviousNUsers, $startPosition);
    }

    private function returnRequestedDataWithMetadata(&$data, int &$requiredNumberOfItems, int &$startOffset = null)
    {
        $numberOfItems = count($data);

        $result = [
            'users' => $data,
            'numberOfRemainingItems' => $requiredNumberOfItems - $numberOfItems,
        ];

        if ($numberOfItems == 0 && $startOffset) $result['theNewOffset'] = $startOffset;

        else $result['theNewOffset'] = $data[$numberOfItems - 1]['position'] + 1;

        return $result;
    }
}
