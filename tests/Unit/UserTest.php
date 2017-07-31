<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    /**
     * @test
     */
    function a_user_can_see_all_of_the_other_users(){

        $user = factory(User::class)->create();

        $otherUsers = factory(User::class, 3)->create();


        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        $response = $this->get('/api/users', $headers);

        $response->assertStatus(200);

    }

    /**
     * @test
     */
    function a_user_can_see_a_spesific_user(){

        $user = factory(User::class)->create();

        $otherUser = factory(User::class)->create();


        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        $response = $this->get('/api/users/'. $otherUser->id, $headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $otherUser->id,
            'name' => $otherUser->name,
            'email' => $otherUser->email
        ]);
    }
}
