<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Transformers\VehicleTransformer;
use App\Type;
use App\User;
use App\Vehicle;
use App\Vmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserVehicleController extends Controller
{
    /**
     * Fetch all vehicles of a user.
     * Route: users/{id}/vehicles - GET
     *
     * @param $userId
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     */
    public function index($userId)
    {
        $loggedUser = $this->getUser();

        $urlParamUser = User::with('vehicles')
            ->where('id', '=', $userId)->first();

        //if no user found with given parameter, abort
        if ($urlParamUser) {

            //get dem vehicles
            $vehicles = Vehicle::with('user', 'vmodel.brand', 'vmodel.type')
                ->where('user_id', '=', $userId)
                ->orderBy('created_at', 'DESC')
                ->get();

            //if logged in user visiting someone else's profile and they have at least one vehicle, proceed.
            //using count on vehicles because laravel returns a collection even if db row is empty
            if ($urlParamUser->id != $loggedUser->id && count($vehicles) > 0) {
                //return it/them
                return $this->response->item($vehicles, new VehicleTransformer());
                //if logged in user viewing his/her profile
            } else if ($urlParamUser->id == $loggedUser->id) {
                //and he/she has at least one vehicle
                if (count($vehicles) > 0) {

                    return $this->response->item($vehicles, new VehicleTransformer());
                } else {
                    return response()->json(['message' => 'You have no registered vehicle',
                        'messageCode' => '1']);
                }
            } else {
                //if he/she's viewing a profile, whether it's his/her or someone else's profile
                //and it has no vehicle associated with it
                return response()->json(['message' => 'No vehicle found for this user',
                    'messageCode' => '2']);
            }
        } else {
            return response()->json(['message' => 'No User Found',
                'messageCode' => '3']);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Associate a newly created vehicle and store it in storage.
     * Route: users/{id}/vehicles - POST
     *
     * @param  \Illuminate\Http\Request $request
     * @param $userId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        $response = array('response' => '', 'success' => false);

        //Request from validation
        $validator = Validator::make($request->only($this->validationArray()),
            ['plate' => 'required|min:5|max:15|unique:vehicles',
                $this->validationRules()]);

        //return laravel's validation message
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return $response;
        } else { //process the request

            $user = $this->getUser();

            //This id check is not actually needed since we're passing
            // user's id from authenticated user.
            //Just making sure for security reasons
            if ($user->id == $userId) {

                //right below we're checking if a record with same values exist on database
                //for fields: type, brand and vmodel. If so we reference the existing one's id instead of creating new one
                //it's pretty straight forward but if it still seems blurry see line 256

                $typeRequest = $request->get('type');
                $typeExist = Type::where('name', '=', trim($typeRequest))->first();
                if ($typeExist != null) {
                    //using existing data
                    $type = $typeExist;
                } else {
                    $type = new Type();
                    $type->name = $typeRequest;
                    $type->save();
                }

                $brandRequest = $request->get('brand');
                $brandExist = Brand::where('name', '=', trim($brandRequest))->first();
                if ($brandExist != null) {
                    $brand = $brandExist;
                } else {
                    $brand = new Brand();
                    $brand->name = $brandRequest;
                    $brand->save();
                }

                $vmodelNameRequest = $request->get('vehicleModel');
                $vmodelYearRequest = $request->get('modelYear');
                //check if there's a vmodel record with given data
                $vmodelExist = Vmodel::where('year', '=', $vmodelYearRequest)
                    ->where('name', '=', $vmodelNameRequest)
                    ->where('brand_id', '=', $brand->id)
                    ->where('type_id', '=', $type->id)
                    ->first();
                if ($vmodelExist != null) {
                    $vmodel = $vmodelExist;
                } else {
                    $vmodel = new Vmodel();
                    $vmodel->name = $vmodelNameRequest;
                    $vmodel->year = $vmodelYearRequest;
                    $vmodel->type_id = $type->id;
                    $vmodel->brand_id = $brand->id;
                    $vmodel->save();
                }

                //Create the vehicle with form data and associate it with user
                $user->vehicles()->create([
                    'plate' => $request->get('plate'),
                    'nickname' => trim($request->get('nickname')),
                    'color' => trim($request->get('color')),
                    'active' => $request->get('active'),
                    'vmodel_id' => ($vmodel->id)
                ]);

                //get the latest created vehicle of the user (which we just created)
                $vehicle = $user->vehicles()->orderBy('created_at', 'DESC')->first();

                return $this->response->item($vehicle, new VehicleTransformer());
            } else {
                return response()->json(['message' => 'You are unauthorized to perform this action',
                        'messageDetails' => 'You can\'t register a vehicle with someone else\'s ID']
                    , 401);
            }
        }
        return $response;
    }

    /**
     * Display the specified vehicle of a user.
     * Route:  users/{user}/vehicles/{vehicle} - GET
     *
     * @param $userId
     * @param $vehicleId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function show($userId, $vehicleId)
    {
        //Simple dynamic query generated by url params.
        $vehicle = Vehicle::with('user', 'vmodel.brand', 'vmodel.type')
            ->where('id', '=', $vehicleId)->first();
        //Abort if no vehicle found with given id
        if ($vehicle) {
            //Check if user id from params owns the vehicle
            $vehicle->where('user_id', '=', $userId)->first();
            if ($vehicle->user_id == $userId) {
                return $this->response->item($vehicle, new VehicleTransformer());
            } else {
                return response()->json(['message' => 'This vehicle belongs to someone else']);
            }
        } else {
            return response()->json(['message' => 'No vehicle found']);
        }
    }

    /**
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified vehicle of a user in storage.
     * Route:  /api/users/{user}/vehicles/{vehicle} - PUT
     *
     * @param  \Illuminate\Http\Request $request
     * @param $userId
     * @param $vehicleId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $userId, $vehicleId)

    {
        $response = array('response' => '', 'message' => 'Validation didn\'t pass');

        //Request from validation
        $validator = Validator::make($request->only($this->validationArray()),
            //Passing vehicle id because we don't want updating action to
            // conflict with unique plate in case user don't update the plate.
                ['plate' => 'required|string|min:7|max:8|unique:vehicles,plate,' . $vehicleId, $this->validationRules()]);

        //return laravel's validation message
        if ($validator->fails()) {
            $response['response'] = $validator->messages();
            return $response;
            //process the request
        } else {

            $user = $this->getUser();

            $vehicle = Vehicle::with('user', 'vmodel')
                ->where('id', '=', $vehicleId)->first();

            //check if logged in user owns the page he's viewing
            if ($user->id == $userId) {

                //Proceed only if logged in user owns the vehicle
                if ($vehicle->user_id == $user->id) {
                    //get user's brand choice of car from request
                    $brandRequest = $request->get('brand');
                    //check if there's a same brand
                    $brandExist = Brand::where('name', '=', trim($brandRequest))->first();
                    //There is
                    if ($brandExist != null) {

                        //we shouldn't update any of the existing brands or types or even vmodel table even though this is an update action
                        //the point of this is that, as we get more inputs for car brands, our list of car brands gets richer
                        //instead of providing a list that user can choose from, we're making users fill the list for themselves.
                        //Also we prevent data duplication by checking unique input. that's one of the main reasons we use separated tables.

                        //meaning we pass existing brand's id to user's vmodel table for this update action
                        $brand = $brandExist;
                        //there isn't
                    } else {
                        //no record of that input, we simply create a new one and pass the id.
                        $brand = new Brand();
                        $brand->name = $brandRequest;
                        $brand->save();
                    }

                    //same thing but with type table (see: 256)
                    $typeRequest = $request->get('type');
                    $typeExist = Type::where('name', '=', trim($typeRequest))->first();
                    if ($typeExist != null) {
                        $type = $typeExist;
                    } else {
                        $type = new Type();
                        $type->name = $typeRequest;
                        $type->save();
                    }

                    //ideology is same here so identical code with create method
                    $vmodelNameRequest = $request->get('vehicleModel');
                    $vmodelYearRequest = $request->get('modelYear');
                    //check if there's a vmodel record with given data
                    $vmodelExist = Vmodel::where('year', '=', $vmodelYearRequest)
                        ->where('name', '=', $vmodelNameRequest)
                        ->where('brand_id', '=', $brand->id)
                        ->where('type_id', '=', $type->id)
                        ->first();
                    if ($vmodelExist != null) {
                        $vmodel = $vmodelExist;
                    } else {
                        $vmodel = new Vmodel();
                        $vmodel->name = $vmodelNameRequest;
                        $vmodel->year = $vmodelYearRequest;
                        $vmodel->type_id = $type->id;
                        $vmodel->brand_id = $brand->id;
                        $vmodel->save();
                    }

                    //update the vehicle with new
                    $vehicle->update([
                        'plate' => $request->get('plate'),
                        'nickname' => trim($request->get('nickname')),
                        'color' => trim($request->get('color')),
                        'active' => $request->get('active'),
                        'vmodel_id' => $vmodel->id,
                        'user_id' => $user->id
                    ]);

                    //Grabbing user's latest updated vehicle for fe response.
                    $vehicle = $user->vehicles()->with('user', 'vmodel.brand', 'vmodel.type')
                        ->orderBy('updated_at', 'DESC')->firstOrFail();

                    return $this->response->item($vehicle, new VehicleTransformer());
                    //logged in doesn't own the vehicle
                } else {
                    return response()->json(['message' => 'You are unauthorized to perform this action',
                        'messageDetail' => 'You can\'t edit someone else\'s vehicle'], 401);
                }
                //logged in user is viewing another user's page
            } else {
                return response()->json(['message' => 'You are unauthorized to perform this action',
                    'messageDetail' => 'You can\'t edit someone else\'s profile'], 401);
            }
        }
    }

    /**
     * Remove the specified vehicle of a user from storage.
     * Route:  /api/users/{user}/vehicles/{vehicle} - DELETE
     *
     * @param $userId
     * @param $vehicleId
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function destroy($userId, $vehicleId)
    {
        $user = $this->getUser();

        //Because of Foreign keys that reference the vehicle id are not onDelete('cascade')
        //When we delete the vehicle, we do not delete its relative data such as model name
        $vehicle = Vehicle::where('id', '=', $vehicleId)->first();

        //check if the user owns the page he/she's viewing.
        if ($user->id == $userId) {
            //check if the user owns the vehicle
            if ($vehicle->user_id == $user->id) {
                $vehicle->delete();
                return $this->response->item($vehicle, new VehicleTransformer());

                //user is viewing someone else's vehicle
            } else {
                return response()->json(['message' => 'You are unauthorized to perform this action',
                    'messageDetail' => 'You can\'t delete someone else\'s vehicle'], 401);
            }
            //user is viewing someone else's page
        } else {
            return response()->json(['message' => 'You are unauthorized to perform this action',
                'messageDetail' => 'You can\'t delete anything on someone else\'s profile'], 401);
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
            'nickname' => 'max:150',
            'brand' => 'required|max:80',
            'vehicleModel' => 'required|max:80',
            'modelYear' => 'required|max:4',
            'type' => 'required|max:40',
            'color' => 'max:40',
        ];
    }

    /**
     * Custom array of url params for request only method.
     *
     * @return array
     */
    public function validationArray()
    {
        return $requestArr = ['plate', 'nickname', 'vehicleModel', 'brand', 'modelYear', 'type', 'color', 'active'];
    }


}
