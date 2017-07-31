<?php

namespace Tests;

use App\User;

trait TestHelper
{
    public function authorize(User $user)
    {
        $token = 'Bearer ' . \JWTAuth::fromUser($user);
        $headers = [
            'Authorization' => $token
        ];

        $this->headers = $headers;

        return $this;
    }

    public function headers()
    {
        return $this->headers;
    }
}