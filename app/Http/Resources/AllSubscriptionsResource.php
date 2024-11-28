<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllSubscriptionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'subscription_id' => $this->id,
            'user' => [
                'user_id' => $this->user->id,
                'user_name' => $this->user->name,
                'user_email' => $this->user->email,
            ],
            'magazine' => [
                'magazine_id' => $this->magazine->id,
                'magazine_name' => $this->magazine->name,
                'monthly_price' => $this->magazine->monthly_price,
                'yearly_price' => $this->magazine->yearly_price,
            ],
            'subscription_type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ];
    }
}
