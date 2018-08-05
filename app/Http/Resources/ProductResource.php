<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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

            // Identification
            'inventory_id' => $this->id,
            'inventory_root' => $this->numinv1,
            'inventory_number' => $this->numinv2,
            'inventory_suffix' => $this->numinv3,
            'legacy_inventory_numbers' =>
                LegacyInventoryNumberResource::collection($this->whenLoaded('legacyInventoryNumbers')),

            // Physical properties
            'height_or_thickness' => $this->hauepa == 0 ? null : $this->hauepa,
            'length_or_diameter' => $this->londia == 0 ? null : $this->londia,
            'depth_or_width' => $this->prolar == 0 ? null : $this->prolar,
            'materials' => MaterialResource::collection($this->whenLoaded('materials')),
            'upholstery' => UpholsteryResource::collection($this->whenLoaded('upholstery')),
            
            // History
            'conception_year' => $this->conception_year,

            // Provenance
            'acquisition_origin' => $this->proori,
            'acquisition_date' => $this->prodat,
             
            // Classification monuments historiques.
            'listed_as_historic_monument' => ($this->clatyp == 'MH'),
            'listed_on' => $this->cladat,
            
            // Taxonomies
            'product_type' => new ProductTypeResource($this->whenLoaded('productType')),
            'product_style' => new ProductStyleResource($this->whenLoaded('productStyle')),
            'category' => $this->cat,
            'denomination' => $this->den,
            'title_or_designation' => $this->titapp,
            'period' => new PeriodResource($this->whenLoaded('period')),
            
            // Content
            'description' => $this->description,
            'bibliography' => $this->bibliography,

            // Authorships and authors
            'authorships' => AuthorshipResource::collection($this->whenLoaded('authorships')),

            // Media
            'images' => ImageResource::collection($this->whenLoaded('images')),

        ];
    }
}
