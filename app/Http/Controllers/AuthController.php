<?php

namespace App\Http\Controllers;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request) {

        try {
            $validated = $this->validate($request, [
                'name' => 'required',   
                'username' => 'required|max:255|unique:users,username',
                'email' => 'required|max:255|unique:users,email',
                'password' => 'required',
                'address' => 'required',
            ]);
        
            $user = new User();
            $user->name = $validated['name'];
            $user->username = $validated['username'];
            $user->email = $validated['email'];
            $user->address = $validated['address'];
            $user->password = Hash::make($validated['password']);
            
            if (!$user->save()) {
                throw new \Exception("Failed to save user"); // Melempar eksepsi jika gagal menyimpan user
            }
        
            $statusCode = 201;
            $data = $user;
            $message = "User registered successfully";
            $status = "Success";
        
            $response = Helper::jsonResponse(['data' => $data, 'message' => $message, 'status' => $status], $statusCode);
            return $response;
        } catch (\Exception $e) {
            // Penanganan kesalahan
            $statusCode = $e->getCode(); // Mendapatkan status eksepsi
            if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode >= 600) {
                $statusCode = 500; // Gunakan kode status default jika kode tidak valid
            }
            $errorMessage = $e->getMessage(); // Mendapatkan pesan error eksepsi
            $status = "Failed";
        
            $response = Helper::jsonResponse(['message' => $errorMessage, 'status' => $status], $statusCode);
            return $response;
        }
    }

    public function login(Request $request) {
        try {
            $validated = $this->validate($request, [
                'email' => 'required|exists:users,email',
                'password' => 'required'
            ]);
    
            $user = User::where('email', $validated['email'])->first();
            if(!Hash::check($validated['password'], $user->password)) {
                // return abort(401, "email or password not valid");
                throw new \Exception("Email or password is wrong, please check again", 401);
            }
    
            $payload = [
                'iat' => intval(microtime(true)),
                'exp' => intval(microtime(true)) + (60 * 60 * 1000),
                'uid' => $user->id
            ];
            
            $secretKey = env("JWT_SECRET_KEY");
            $token = JWT::encode($payload, $secretKey, 'HS256');
            // return response()->json(['access_token' => $token]);

            $code = 200;
            $data['access_token'] = $token;
            $message = "User logged in successfully";
            $status = "Success";
        
            $response = Helper::jsonResponse(['data' => $data, 'message' => $message, 'status' => $status], $code);
            return $response;
        } catch (\Exception $e) {
            // Penanganan kesalahan
            $statusCode = $e->getCode(); // Mendapatkan status eksepsi
            if (!is_numeric($statusCode) || $statusCode < 100 || $statusCode >= 600) {
                $statusCode = 500; // Gunakan kode status default jika kode tidak valid
            }
            $errorMessage = $e->getMessage(); // Mendapatkan pesan error eksepsi
            $status = "Failed";
        
            $response = Helper::jsonResponse(['message' => $errorMessage, 'status' => $status], $statusCode);
            return $response;
        }

    }

}
