<?php

namespace App\Http\Controllers;

Use Str;
Use Hash;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;

class SessionsController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store()
    {
        
        // dd(request()->input());
        $credentials = request()->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:5|max:50'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->messages())->withInput();
        }

        if(!Cookie::get('jwt_token')){
            $params = request()->input();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post(env('API_AUTH_HOST').'/auth/login', [
                "email" => $params["email"],
                "password" => $params["password"],
            ])->json();
            
            if(!$response || $response["status"] == "error"){
                throw ValidationException::withMessages([
                    'error_response' => $response["message"]
                ]);
            }

            Cookie::queue("jwt_token", $response["data"]["access_token"], 60);
        }

        return redirect('/dashboard');

    }

    public function show(){
        request()->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            request()->only('email')
        );
    
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
        
    }

    public function update(){
        
        request()->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]); 
          
        // $status = Password::reset(
        //     request()->only('email', 'password', 'password_confirmation', 'token'),
        //     function ($user, $password) {
        //         $user->forceFill([
        //             'password' => ($password)
        //         ])->setRememberToken(Str::random(60));
    
        //         $user->save();
    
        //         event(new PasswordReset($user));
        //     }
        // );
    
        // return $status === Password::PASSWORD_RESET
        //             ? redirect()->route('login')->with('status', __($status))
        //             : back()->withErrors(['email' => [__($status)]]);
    }

    public function destroy()
    {
        // auth()->logout();
        Cookie::queue(Cookie::forget('jwt_token'));

        return redirect('/sign-in');
    }

}
