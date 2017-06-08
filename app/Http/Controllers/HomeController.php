<?php namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller
{
	public function index()
	{
		if (Auth::check()) {
			return redirect('dashboard/news');
		}
		return redirect('login');
	}

	public function login()
	{
		if (Auth::check()) {
			return redirect('dashboard');
		}
		return view('auth.login');
	}

	public function doLogin(Request $r)
	{
		if (Auth::attempt([
			'username'=>$r->get('username'),
			'password' =>$r->get('password')])
			) {
			return redirect()->intended('/');
		}
		return redirect('login')
			->with('error', 'Invalid credentials');
	}

	public function doLogout()
	{
		Auth::logout();

		return redirect('login');
	}
}
