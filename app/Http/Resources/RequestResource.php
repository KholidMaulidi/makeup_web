<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'mua' => [
                'id' => $this->mua->id,
                'name' => $this->mua->name,
            ],
            'packages' => $this->requestPackages->map(function ($requestPackage) {
                $packageDetails = json_decode($requestPackage->package_details);
                return [
                    'id' => $packageDetails->id, 
                    'package_name' => $packageDetails->package_name,
                    'price' => $packageDetails->price,
                    'image' => $packageDetails->image ? url('storage/images/packages/' . $packageDetails->image) : null,
                    'quantity' => $requestPackage->quantity,
                    'total_per_package' => $packageDetails->total_per_package,
                ];
            }),
            'date' => $this->date->format('Y-m-d'),
            'start_time' => $this->start_time->format('H:i'),
            'end_time' => $this->end_time->format('H:i'),
            'visit_type' => $this->visit_type,
            'distance' => $this->distance,
            'postage' => $this->postage,
            'total_price' => $this->total_price,
            'status' => $this->status,
            // 'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
