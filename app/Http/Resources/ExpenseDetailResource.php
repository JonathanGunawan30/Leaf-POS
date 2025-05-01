<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseDetailResource extends JsonResource
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
            'description' => $this->description ?? '-',
            'note' => $this->note ?? '-',
            'expense_date' => $this->expense_date,
            'amount' => $this->amount,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                "email" => $this->user->email,
                'role' => [
                    'id' => $this->user->role->id,
                    'name' => $this->user->role->name,
                ]
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name
            ],
        ];
    }
}
