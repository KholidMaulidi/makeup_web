<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
                return [
                    'id' => $requestPackage->package->id,
                    'package_name' => $requestPackage->package->package_name,
                    'quantity' => $requestPackage->quantity,
                ];
            }),
            'date' => $this->request->date->format('Y-m-d'),
            'start_time' => $this->request->start_time->format('H:i'),
            'end_time' => $this->request->end_time->format('H:i'),
            'visit_type' => $this->request->visit_type,
            'postage' => $this->request->postage,
            'total_price' => $this->request->total_price,
            'payment_status' => $this->payment_status,
            'payment_proof' => $this->payment_proof
        ];
    }
}
