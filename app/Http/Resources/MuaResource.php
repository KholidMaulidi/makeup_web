<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MuaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $rate = $this->reviews->avg('rate');
        $minprice = $this->packages->min('price');
        $countReviews = $this->reviews->count();
        return [
            'mua' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'avatar' => $this->avatar,
                'makeup_artist_profile' => [
                    'description' => $this->makeupArtistProfile->description,
                    'address' => $this->makeupArtistProfile->address,
                    'city' => $this->makeupArtistProfile->city,
                ],
                'rate' => $rate,
                'start_from' => $minprice,
                'count_reviews' => $countReviews,
            ],
        ];
    }
}
