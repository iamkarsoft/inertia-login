<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Socialite;

class SocialLoginController extends Controller {
	//
	public function redirect($service, Request $request) {
		return Socialite::driver($service)->redirect();
	}

	public function callback($service, Request $request) {
		$githubUser = Socialite::driver($service)->user();
		// dd($user);
		// $user->token
		$user = User::where('provider_id', $githubUser->getId())->first();

		if (!$user):
			$user = User::create([
				'email' => $githubUser->getEmail(),
				'name' => $githubUser->getName(),
				'provider_id' => $githubUser->getId(),
			]);
		endif;

		//login user
		auth()->login($user, true);
		return redirect('dashboard');
	}
}
