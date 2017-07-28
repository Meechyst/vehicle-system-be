<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    /**
     * Perform a register action.
     * Route: /register - POST
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function register(Request $request)
    {
        //Register form validation
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:4',
        ]);
        //Store the user in db
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);
        //Create the token using JWT from user's data
        $token = JWTAuth::fromUser($user);

        //add the token in user obj
        $user->token = 'Bearer ' . $token;

        //Return the user and his/her token
        return $this->response->item($user, new UserTransformer())->withHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * Perform a login action.
     * Route: /login - POST
     *
     * @param Request $request
     * @return \Dingo\Api\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //Form validation
        $this->validate($request, [
            'name' => 'required|max:255',
            'password' => 'required|min:4',
        ]);

        //Grab credentials from the request
        $credentials = $request->only('name', 'password');

        try {
            //Attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'These credentials do not match our records.'], 401);
            }
        } catch (JWTException $e) {
            //Something went wrong while attempting to encode the token
            return response()->json(['message' => 'Authorization failed, please refresh the page and try again.'], 500);
        }
        //Get the user
        $user = User::where('name', '=', $request->get('name'))->first();

        //add the token in user obj
        $user->token = 'Bearer ' . $token;

        return $this->response->item($user, new UserTransformer())->withHeader('Authorization', 'Bearer ' . $token);
    }

    /**
     * Perform a logout action.
     * Route: /logout - GET
     *
     * @return \Dingo\Api\Http\Response
     */
    public function logout()
    {
        //Simply delete/null the token from storage.
        $token = \JWTAuth::getToken();
        \JWTAuth::invalidate($token);
        return $this->response->noContent();
    }

    /**
     * Currently logged in user.
     * Route: /user - GET
     *
     * @return \Dingo\Api\Http\Response
     */
    public function user()
    {
        return $this->item(\Auth::user(), new UserTransformer());
    }

    /**
     * No fucking idea.
     * Route: /token - GET
     *
     * @return \Dingo\Api\Http\Response
     */
    public function refreshToken()
    {
        return $this->response->noContent();
    }
}
