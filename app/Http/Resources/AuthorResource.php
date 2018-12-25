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
            'first_name' => $this->prenom,
            'last_name' => $this->nomfamille,
            'date_of_birth' => $this->datnaiss,
            'year_of_birth' => $this->yearOfBirth,
            'date_of_death' => $this->datdeces,
            'year_of_death' => $this->yearOfDeath,
            'occupation' => $this->metier,
            'birthplace' => $this->villenaiss,
            'deathplace' => $this->villedeces,
            'isni_uri' => $this->numisni,
        ];
    }
}
