<?php

namespace Tests\Unit;

use App\Brand;
use App\Type;
use App\User;
use App\Vehicle;
use App\Vmodel;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VehicleTest extends TestCase
{
    /**
     *
     * @test
     */
    function a_user_can_create_a_vehicle()
    {
        $user = factory(User::class)->create();

        $type = factory(Type::class)->create();

        $brand = factory(Brand::class)->create();

        $vmodel = factory(Vmodel::class)->create();

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        $response = $this->post('api/users/' . $user->id . '/vehicles', [
            'plate' => '35MNM35',
            'type' => $type->name,
            'brand' => $brand->name,
            'vehicleModel' => $vmodel->name,
            'modelYear' => rand(1901, 2017),
            'nickname' => 'Free Bird',
            'color' => 'Cyan',
            'active' => 1,
        ], $headers);

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'plate' => '35MNM35',
            'nickname' => 'Free Bird',
            'color' => 'Cyan',
            'active' => 1,
            'user_id' => $user->id
        ]);

    }

    /**
     * @test
     */
    function a_user_can_see_all_the_vehicles_he_owns()
    {


        $user = factory(User::class)->create();

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        factory(Type::class)->create();

        factory(Brand::class)->create();

        factory(Vmodel::class)->create();

        factory(Vehicle::class, 3)->create();
        $response = $this->get('api/users/' . $user->id . '/vehicles', $headers);

        $response->assertStatus(200);

        $vehicles = $user->vehicles()->get()->pluck('plate')->toArray();
        $response->assertJsonFragment([
            'plate' => $vehicles[0],
            'plate' => $vehicles[1],
            'plate' => $vehicles[2]
        ]);

    }

    /**
     * @test
     */
    function a_user_can_see_a_spesific_vehicle_of_a_spesfic_user()
    {

        $user = factory(User::class)->create();

        $anotherUser = factory(User::class)->create();

        $token1 = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);
        $token2 = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($anotherUser);

        $headers1 = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token1
        ];
        $headers2 = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token2
        ];

        $type = factory(Type::class)->create();

        $brand = factory(Brand::class)->create();

        $vmodel = factory(Vmodel::class)->create();

        $response = $this->post('api/users/' . $anotherUser->id . '/vehicles', [
            'plate' => '35MNM35',
            'type' => $type->name,
            'brand' => $brand->name,
            'vehicleModel' => $vmodel->name,
            'modelYear' => rand(1901, 2017),
            'nickname' => 'Free Bird',
            'color' => 'Cyan',
            'active' => 1,
        ], $headers2);

        $response->assertStatus(200);
        $vehicleId = $anotherUser->vehicles()->first()->id;
        $response = $this->get('api/users/' . $anotherUser->id . '/vehicles/' . $vehicleId, $headers1);

        $response->assertStatus(200);

    }

    /**
     * @test
     */
    function a_user_can_update_his_own_vehicle(){

        $user = factory(User::class)->create();

        $type = factory(Type::class)->create();

        $brand = factory(Brand::class)->create();

        $vmodel = factory(Vmodel::class)->create();

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        $response1 = $this->post('api/users/' . $user->id . '/vehicles', [
            'plate' => '35MNM35',
            'type' => $type->name,
            'brand' => $brand->name,
            'vehicleModel' => $vmodel->name,
            'modelYear' => rand(1901, 2017),
            'nickname' => 'Free Bird',
            'color' => 'Cyan',
            'active' => 1,
        ], $headers);

        $response1->assertStatus(200);

        $vehicleId = $user->vehicles()->first()->id;

        $response2 = $this->put('api/users/' . $user->id . '/vehicles/' . $vehicleId, [
            'plate' => '35MNM35',
            'type' => $type->name,
            'brand' => $brand->name,
            'vehicleModel' => $vmodel->name,
            'modelYear' => rand(1901, 2017),
            'nickname' => 'Ghost Rider',
            'color' => 'Black',
            'active' => 0,
        ], $headers);

        $response2->assertStatus(200);

    }

    /**
     * @test
     */
    function a_user_can_delete_his_own_vehicle(){

        $user = factory(User::class)->create();

        $type = factory(Type::class)->create();

        $brand = factory(Brand::class)->create();

        $vmodel = factory(Vmodel::class)->create();

        $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

        $headers = [
            'Accept' => 'application/json',
            'AUTHORIZATION' => 'Bearer' . $token
        ];

        $response = $this->post('api/users/' . $user->id . '/vehicles', [
            'plate' => '35MNM35',
            'type' => $type->name,
            'brand' => $brand->name,
            'vehicleModel' => $vmodel->name,
            'modelYear' => rand(1901, 2017),
            'nickname' => 'Free Bird',
            'color' => 'Cyan',
            'active' => 1,
        ], $headers);

        $response->assertStatus(200);

        $vehicleId = $user->vehicles()->first()->id;

        $response = $this->delete('api/users/' . $user->id . '/vehicles/' .$vehicleId,[],$headers);

        $response->assertStatus(200);

    }
}
