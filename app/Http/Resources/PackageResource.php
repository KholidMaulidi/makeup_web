<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'package_name' => $this->package_name,
            'description' => $this->description,
            'price' => $this->price,
            'details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'item_name' => $detail->item_name,
                    'description' => $detail->description,
                ];
            }),
        ];
    }
}
