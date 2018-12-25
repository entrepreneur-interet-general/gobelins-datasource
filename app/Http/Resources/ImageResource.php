<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
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
            'path' => $this->cleanPath,
            'is_poster' => boolval($this->photoprinc),
            'is_published' => boolval($this->publiable),
            'is_prime_quality' => boolval($this->comm_qualite),
            'is_documentation_quality' => boolval($this->comm_document),
            'has_privacy_issue' => boolval($this->comm_securite),
            'has_marking' => boolval($this->comm_marquage),
            'is_reviewed' => ! boolval($this->comm_adefinir),
            'copyright' => $this->copyright,
        ];
    }
}
