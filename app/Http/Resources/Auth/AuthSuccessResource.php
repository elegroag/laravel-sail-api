<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthSuccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'token' => $this->resource['token'] ?? null,
            'user' => [
                'documento' => $this->resource['user']['documento'] ?? null,
                'nombre' => $this->resource['user']['nombre'] ?? null,
                'email' => $this->resource['user']['email'] ?? null,
                'tipo' => $this->resource['user']['tipo'] ?? null,
            ],
            'expires_in' => $this->resource['expires_in'] ?? 3600,
        ];
    }
}
