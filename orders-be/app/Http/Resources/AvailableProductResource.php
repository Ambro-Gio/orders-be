<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailableProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "ID" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "availableQuantity" => $this->stock->stock_quantity,
        ];
    }
}
