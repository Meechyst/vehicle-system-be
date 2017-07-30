<?php

namespace App\Http\Controllers;

use App\Transformers\VehicleTransformer;
use App\User;
use App\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //Using lazy-loading technique to fetch all vehicles.
        //Meaning no matter how many vehicles there are
        //only 2 queries will be executed to get all vehicles.
        $vehicles = Vehicle::with('user', 'vmodel', 'vmodel.brand', 'vmodel.type')->orderBy('created_at', 'DESC')->get();
        return $this->response->item($vehicles, new VehicleTransformer());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle = $vehicle->with('user', 'vmodel', 'vmodel.brand', 'vmodel.type')->first();
        return $this->response->item($vehicle, new VehicleTransformer());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehicle $vehicle)
    {


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehicle $vehicle)
    {

    }

    /**
     * Simply check for plate that came with requests exist on DB.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPlate(Request $request){

        $vehicle = Vehicle::where('plate', '=', $request->input('plate'))->first();

        if($vehicle){
            return response()->json(['message' => 'There\'s already a registered vehicle with this plate'], 204);
        } else
        {
            return response()->json(['message' => 'Vehicle plate did not match any of our records, can be used to register new one'], 404);
        }
    }

    /**
     * Currently logged-in user.
     *
     * @return User|null
     */
    public function getUser()
    {
        return $user = \Auth::user();
    }

    /**
     * Custom array of validation rules for both update and create actions.
     *
     * @return array
     */
    public function validationRules()
    {
        return $validationArr = [
            'nickname' => 'string|max:255',
            'brand' => 'string|max:80',
            'vehicleModel' => 'string|max:80',
            'modelYear' => 'integer|min:4|max:4',
            'type' => 'string|max:40',
            'color' => 'string|max:40',
            'active' => 'boolean'
        ];
    }

    /**
     * Custom array of url params for request only method.
     *
     * @return array
     */
    public function validationArray()
    {
        return $requestArr = ['plate', 'nickname', 'brand', 'vehicleModel', 'modelYear', 'type', 'color', 'active'];
    }
}
