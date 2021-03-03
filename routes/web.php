<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
	return Inertia::render('Welcome', [
		'canLogin' => Route::has('login'),
		'canRegister' => Route::has('register'),
		'laravelVersion' => Application::VERSION,
		'phpVersion' => PHP_VERSION,
	]);
});

Route::get('/dashboard', function () {
	return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/login/github', function () {
	return Socialite::driver('github')->redirect();
});

Route::get('/login/github/callback', function () {
	$githubUser = Socialite::driver('github')->user();
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
});

require __DIR__ . '/auth.php';
