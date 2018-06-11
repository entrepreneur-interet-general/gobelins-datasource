<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorshipResource extends JsonResource
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
            'author_nature' => $this->nature(),
            'relevant_detail' => $this->parcon, // parcon = Partie concernée
            'author' => new AuthorResource($this->whenLoaded('author')),
        ];
    }
}
