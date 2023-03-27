<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMailreset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->failedResponse();
        }
        $token = Str::random(40);
        $user->remembre_token = $token;
        $user->save();
        Mail::to($request->email)->send(new SendMailreset($token, $request->email, $user->name));
        return $this->successResponse($token);
    }

    public function failedResponse()
    {
        return response()->json([
            'error' => "Email was not found in the Database"
        ], 404);
    }

    public function successResponse($token)
    {
        return response()->json([
            'message' => "Reset email link sent successfully, please check your inbox",
            'token'  => $token
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        if ($request->isMethod('post')) {
            $request->validate([
                'password' => 'required|min:8',
                'confirm_password' => 'required|min:8|same:password',
                'token' => 'required|string'
            ]);
            $user = User::where('remembre_token', $request->token)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'statuts' => 'success',
                    'message' => 'your password has been updated successfuly',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'you do not have permession to access into this page'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'method not allowd'
            ], 405);
        }
    }

    public function closeAccount(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Profile deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'faild to complete this request',
            ], 401);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    // pour l'admin
    public function editUser(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if ($user) {
            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'user has been updated successfuly',
                'user'   => $user
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'user not found',
            ], 404);
        }
    }

    public function getAllUsers()
    {
        $userauth=auth()->user();
        if(!$userauth->hasPermissionTo('user-list')){
            return response()->json([
                'status' => 'error',
                'message' => 'You dont have permission to show user'
            ], 403);
        }
        $user = user::all(['id', 'name', 'email']);
        $numberUsers = $user->count();
        if ($numberUsers > 0) {
            return response()->json([
                'message' => 'users',
                'users' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'there is no user',
            ]);
        }
    }
    
}
