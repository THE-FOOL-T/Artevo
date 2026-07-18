<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtifactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'origin_country' => $this->origin_country,
            'creation_date' => $this->creation_date,
            'creation_period' => $this->creation_period,
            'materials' => $this->materials,
            'dimensions' => $this->dimensions,
            'description' => $this->description,
            'primary_image_url' => $this->primaryImageUrl(),
            'is_verified' => $this->isVerified(),
            'views_count' => $this->views_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
