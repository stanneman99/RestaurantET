<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

  /**
  * Handle an authentication attempt.
  *
  * @param  \Illuminate\Http\Request $request
  *
  * @return Response
  */
  public function authenticate(Request $request)
  {
    $credentials = $request->only('customer_nr', 'password');
    // Auth::attempt(['email' => $request->email, 'password' => $request->password, 'active' => 1])
    if (Auth::attempt($credentials)) {
      // Authentication passed...
      return redirect()->route('home');
    } else {
      dd(Auth::attempt($credentials));
    }
    // return True;
  }

  public function login(Request $request)
  {
    if ($this->hasTooManyLoginAttempts($request)) {
      // $this->fireLockoutEvent($request);
      $this->clearLoginAttempts($request);
      return redirect()->route('password.request')->with('error', 'Too many login attempts');
    }
    $this->validateLogin($request);

    // $credentials = $request->only('customer_nr', 'password', 'status');
    // $meme = Auth::attempt(['customer_nr' => $request->customer_nr, 'password' => $request->password]);
    if (Auth::attempt(['customer_nr' => $request->customer_nr, 'password' => $request->password, 'status' => 0])) {
      // Authentication passed...
      $this->clearLoginAttempts($request);
      return redirect()->route('home');
    } elseif(Auth::attempt(['customer_nr' => $request->customer_nr, 'password' => $request->password, 'status' => 1])) {
      $this->clearLoginAttempts($request);
      Auth::logout();
      return redirect()->back()->with('error', 'Uw account is niet verifieerd of is geblokkeerd. Neem contact op met de administrator.');
    }
    else{
      $this->incrementLoginAttempts($request);
      return $this->sendFailedLoginResponse($request);
    }
  }

  public function username(){
    return 'customer_nr';
  }

  public function logout(Request $request) {
    Auth::logout();
    return redirect('/');
  }

  public function showLoginForm()
  {
    return view('auth.login');
  }

  protected function hasTooManyLoginAttempts(Request $request)
{
    $maxLoginAttempts = 3;

    return $this->limiter()->tooManyAttempts(
        $this->throttleKey($request), $maxLoginAttempts
    );
}

  use AuthenticatesUsers;

  /**
  * Where to redirect users after login.
  *
  * @var string
  */
  protected $redirectTo = '/';

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
}
