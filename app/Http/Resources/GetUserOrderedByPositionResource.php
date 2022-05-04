<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetUserOrderedByPositionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
          'id' => $this['id'],
          'username' => $this['username'],
          'image_url' => $this['image_url'],
          'position' => $this['position'],
          'karma_score' => $this['karma_score']
        ];
    }
}
