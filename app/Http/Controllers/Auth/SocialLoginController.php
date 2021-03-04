<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Socialite;

class SocialLoginController extends Controller {

	public function __construct() {
		$this->middleware(['social', 'guest']);
	}
	//
	public function redirect($service, Request $request) {
		return Socialite::driver($service)->redirect();
	}

	public function callback($service, Request $request) {
		$serviceUser = Socialite::driver($service)->user();
		// dd($user);
		// $user->token
		// $user = User::where('provider_id', $serviceUser->getId())->first();

		$user = $this->getExistingUser($serviceUser, $service);

		// create user after being redirected
		if (!$user):
			$user = User::create([
				'email' => $serviceUser->getEmail(),
				'name' => $serviceUser->getName(),
				'provider_id' => $serviceUser->getId(),
			]);
		endif;

		// check if user has social account
		if ($this->needsToCreateSocial($user, $service)):
			$user->social()->create([
				'social_id' => $serviceUser->getId(),
				'service' => $service,
			]);
		endif;

		//login user
		auth()->login($user, true);
		return redirect()->intended();
	}

	/*
		    * Create social account record
	*/
	protected function needsToCreateSocial($user, $service) {
		return !$user->hasSocialLinked($service);
	}

	/*
		    * check if user has email else
		    * check if is using a service
	*/
	protected function getExistingUser($serviceUser, $service) {
		return User::where('email', $serviceUser->getEmail())->orWhereHas('social', function ($q) use ($serviceUser, $service) {
			$q->where('social_id', $serviceUser->getId())->where('service', $service);
		})->first();
	}
}
