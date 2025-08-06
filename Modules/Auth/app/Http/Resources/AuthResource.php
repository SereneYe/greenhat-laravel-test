<?php

namespace Modules\Auth\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * @var string|null
     */
    private ?string $token;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  string|null  $token
     * @return void
     */
    public function __construct($resource, ?string $token = null)
    {
        parent::__construct($resource);
        $this->token = $token;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'token' => $this->when($this->token !== null, $this->token),
            'token_type' => $this->when($this->token !== null, 'Bearer'),
        ];
    }
}
