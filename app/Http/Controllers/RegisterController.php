<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function create()
    {
        return view('register.create');
    }

    public function store(){
        $credentials = request()->only('name', 'phone_number','email', 'password');
        $validator = Validator::make($credentials, [
            'name' => 'required|max:255',
            'phone_number' => 'required|numeric',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:5|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->messages())->withInput();
        }

        $params = request()->input();
        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post(env('API_AUTH_HOST').'/auth/signup', [
            "full_name" => $params["name"],
            "phone" => $params["phone_number"],
            "level" => "user",
            "email" => $params["email"],
            "password" => $params["password"],
        ])->json();

        if(!$response || $response["status"] == "error"){
            throw ValidationException::withMessages([
                'error_response' => $response["message"]
            ]);
        }
        
        return redirect('/');
    } 
}
