<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MuseumResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'logo_url' => $this->logoUrl(),
            'cover_image_url' => $this->coverImageUrl(),
            'foundation_year' => $this->foundation_year,
            'website' => $this->website,
            'address' => [
                'street' => $this->address,
                'city' => $this->city,
                'country' => $this->country,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
            ],
            'opening_hours' => $this->opening_hours,
            'is_verified' => $this->isVerified(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
