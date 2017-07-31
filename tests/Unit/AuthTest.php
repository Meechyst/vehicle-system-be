<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;


class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     * @test
     */
    public function a_visitor_can_sign_up()
    {
        $user = factory(User::class)->raw();

        $response = $this->post('api/register', [
            'email' => $user['email'],
            'name' => $user['name'],
            'password' => 'secret'
        ]);


        $response->assertJson([
            'data' => [
                'email' => $user['email'],
                'name' => $user['name']
            ]
        ]);

        $response->assertHeader('authorization');

    }

    /**
     * @test
     */
    public function a_user_can_sign_in()
    {
        $user = factory(User::class)->create();

        $response = $this->postJson('api/login', [
            'name' => $user->name,
            'password' => 'secret'
        ]);


        $response->assertHeader('authorization');
        $response->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_logout()
    {
        $user = factory(User::class)->create();

        $this->authorize($user);

        $response = $this->get('api/logout', $this->headers());

        $response->assertStatus(204);
    }

    /**
     * @test
     */
    public function a_user_can_see_his_info()
    {
        $user = factory(User::class)->create();

        $this->authorize($user);

        $response = $this->get('api/user', $this->headers());

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }


}
