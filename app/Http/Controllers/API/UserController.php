<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Rules\Password;
use PhpParser\Node\Stmt\Return_;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function fetch(Request $request)
    {
        $id = $request->user()->id;

        $data = User::with('user_locations')->find($id);
        return ResponseFormatter::success($data, 'Data profile user berhasil diambil');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Invalid Credentials');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Authenticated');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'phone_number' => ['required', 'string', 'max:255',],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password]
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);

            $user = User::where('email', $request->email)->first();

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error,
            ], 'Authentication Failed', 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $user->update($data);
        return ResponseFormatter::success($user, 'Profile Updated');
    }

    public function uploadPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($validator->fails()) {
            return ResponseFormatter::error([
                'error' => $validator->errors()
            ], 'Upload photo failed', 401);
        }
        if ($request->file('file')) {
            $file = $request->file->store('assets/user', 'public');
            //simpan ke database
            $user = Auth::user();
            $user->profile_photo_path = $file;
            $user->update();
            return ResponseFormatter::success([$file], 'File successfully uploaded');
        }
    }



    public function address_list(Request $request)
    {

        $userlocation = UserLocation::where('user_id', $request->user()->id)->get();
        return ResponseFormatter::success($userlocation, 'Address List', 200);
    }


    public function add_new_address(Request $request,)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'phone_number' => 'required',
            'address' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => "Error with the address"], 403);
        }


        $address = [
            'user_id' => $request->user()->id,
            'address_type' => $request->address_type,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now()
        ];
        UserLocation::create($address);
        return ResponseFormatter::success($address, 'location successfully added', 200);
    }
    public function update_address(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'address_type' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $validator->errors(),
            ], 403);
        }
        $address = [
            'user_id' => $request->user()->id,
            'address_type' => $request->address_type,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $userlocation = UserLocation::where('id', $id)->update($address);


        return ResponseFormatter::success($address, 'update location succsess', 200);
    }

    public function address_delete(Request $request, $id)
    {
        UserLocation::where('user_id', $id)->delete();
        return ResponseFormatter::success('user location deleted', 200);
    }
    public function geocode_api(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required',
            'lng' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return ResponseFormatter::error($validator, 403);
        }

        $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $request->lat . ',' . $request->lng . '&key=' . 'AIzaSyDQm7fskSlerL2C1_1ODi4-49MMQanF63Y');
        return $response->json();
    }
}
