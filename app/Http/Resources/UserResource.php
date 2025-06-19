<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            'token' => $this->when($this->token ?? false, $this->token),
            "role_id" => $this->when($this->role_id ?? false, $this->role_id),
            "status" => $this->when($this->status ?? false, $this->status),
            "role" => new RoleResource($this->whenLoaded('role')),
        ];
    }
}
