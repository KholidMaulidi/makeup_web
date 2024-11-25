<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'image' => $this->image ? url('storage/images/packages/' . $this->image) : null,
            'description' => $this->description,
            'price' => $this->price,
            'service' => [
                'id' => $this->service->id,
                'service_name' => $this->service->service_name, 
            ],
            'details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'item_name' => $detail->item_name,
                ];
            }),
        ];
    }
}
