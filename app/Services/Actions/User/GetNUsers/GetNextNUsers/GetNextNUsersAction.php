<?php

namespace App\Services\Actions\User\GetNUsers\GetNextNUsers;

use App\Services\ActionInterface;
use App\Services\Actions\User\GetNUsers\GetNUsersResult;
use Illuminate\Support\Facades\DB;

class GetNextNUsersAction implements ActionInterface
{
    /**
     * @param GetNextNUsersCommand $command
     * @return array
     */
    public function handle(&$command)
    {
        $user = $command->user;

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
                                LIMIT $command->numberOfNextNUsers
                            ) as t1, 
                            (
                                SELECT @rank := $command->startPosition
                            ) AS t2
                            ORDER BY karma_score DESC")
            ->get();

        return GetNUsersResult::formatResult($data, $command->numberOfNextNUsers, $command->startPosition);
    }
}
