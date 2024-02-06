<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function login(Request $request)
    {
        $input = $request->all();

            $this->validate($request, [
                'email' => 'required',
                'password' => 'required',
                // 'g-recaptcha-response' => 'required'
            ]
       );


        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if(Auth::user()->role_id == 1){

                return redirect("/admin/dashboard");
            }else{
                if($request->id == 2) {
                    return redirect("/cart/checkout");
                }else {
                    return redirect("/user/profile");  
                }
                
            }

        }else{
            return redirect("/")
                ->with('error','Your credentials are incorrect.');
        }

    }

    protected function authenticated($request, $user)
    {

        if(Auth::user()->role_id == 1){

            return redirect("/admin/dashboard");
        }else{
            return redirect("/user/profile");
        }

    }
}
