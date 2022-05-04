<?php

namespace App\Services\Actions\User\GetNUsers;

use Illuminate\Support\Collection;

class GetNUsersResult
{
    public static function formatResult(Collection &$data, int &$numberOfRequiredUsers, int &$startPosition)
    {
        if ($data->isEmpty())
            return self::returnRequestedDataWithMetadata($data->toArray(), $numberOfRequiredUsers, $startPosition);

        $data = $data->map(function ($value, $key) {
            return (array) $value;
        })
            ->toArray();

        return self::returnRequestedDataWithMetadata($data, $numberOfRequiredUsers, $startPosition);
    }

    private static function returnRequestedDataWithMetadata($data, int &$requiredNumberOfItems, int &$startOffset = null)
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