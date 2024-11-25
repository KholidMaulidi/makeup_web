<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryTransactionResource extends JsonResource
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
                'id' => $this->request->user->id,
                'name' => $this->request->user->name,
            ],
            'mua' => [
                'id' => $this->request->mua->id,
                'name' => $this->request->mua->name,
            ],
            'packages' => $this->request->requestPackages->map(function ($requestPackage) {
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
            'date' => $this->request->date->format('Y-m-d'),
            'start_time' => $this->request->start_time->format('H:i'),
            'end_time' => $this->request->end_time->format('H:i'),
            'visit_type' => $this->request->visit_type,
            'distance' => $this->request->distance,
            'postage' => $this->request->postage,
            'total_price' => $this->request->total_price,
            'status' => $this->status,
        ];
    }
}
