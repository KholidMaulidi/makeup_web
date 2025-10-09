<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'no_hp' => $this->no_hp,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];

        if ($this->district) {
            $data['district'] = [
                'id' => $this->district->id,
                'name' => $this->district->name,
            ];
        }

        if ($this->district?->regency) {
            $data['regency'] = [
                'id' => $this->district->regency->id,
                'name' => $this->district->regency->name,
            ];
        }

        if ($this->district?->regency?->province) {
            $data['province'] = [
                'id' => $this->district->regency->province->id,
                'name' => $this->district->regency->province->name,
            ];
        }

        return $data;
    }
}
