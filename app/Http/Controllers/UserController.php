<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display all users.
     * Route: /users - GET
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->response->item($users, new UserTransformer());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified user.
     * Route: /users/{id} - GET
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Request $request
     * @internal param User $user
     * @internal param int $id
     */
    public function show($id)
    {

        $user = User::where('id', '=', $id)->first();

        return $this->response->item($user, new UserTransformer());


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Check if user exist by comparing emails.
     * Route: /checkEmail?email={email} - GET
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEmail(Request $request){

        //Check if a username of a user exist on db
        //by comparing it with the one that came from url param
        $user = User::where('email', '=', $request->input('email'))->first();

        if($user){
            return response()->json(['message' => 'There\'s already a registered user with this email'], 204);
        } else {
            return response()->json(['message' => 'No email matched, good to go'], 404);
        }
    }

    /**
     * Check if user exist by comparing names.
     * Route: /checkEmail?name={name} - GET
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkName(Request $request){

        //same thing with checkEmail but with name of a user
        $user = User::where('name', '=', $request->input('name'))->first();

        if($user){
            return response()->json(['message' => 'There\'s already a registered user with this username'], 204);
        } else {
            return response()->json(['message' => 'Username does not exist in db, good to go.'], 404);
        }
    }
}
