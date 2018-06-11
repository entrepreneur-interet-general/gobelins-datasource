<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
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
            'id' => $this->codaut,
            'name' => $this->aut,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'year_of_birth' => $this->yearOfBirth,
            'year_of_death' => $this->yearOfDeath,
            'occupation' => $this->occupation,
        ];
    }
}
