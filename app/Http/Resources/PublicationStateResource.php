<?php

namespace App\Http\Resources;

use \App\Models\PublicationState;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicationStateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->coddiffusion,
            'is_published' => PublicationState::PUBLICATION_MAP[$this->coddiffusion],
        ];
    }
}
