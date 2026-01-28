<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthErrorResource extends JsonResource
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
            'success' => false,
            'message' => $this->resource['message'] ?? 'Error de autenticación',
            'code' => $this->resource['code'] ?? 401,
            'errors' => $this->resource['errors'] ?? null,
        ];
    }
}
