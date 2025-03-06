<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return Socialite::driver('keycloak')->redirect();
})->name('login.keycloak');

Route::get('/callback/keycloak', function () {
    $user = Socialite::driver('keycloak')->user();
    if ($user) {
        $q = User::where('user_code', $user->user['preferred_username'])->first();
        if ($q) {
            Auth::login($q);
            return redirect()->route('dashboard');
        }
        echo 'User not found';
        // return redirect()->route('login.keycloak', ['error' => 'User not found']);
    }
    return redirect()->route('login.keycloak', ['error' => 'User not found']);
});
Route::get('/dashboard', function () {
    echo 'Dashboard';
})->middleware(['auth'])->name('dashboard');

Route::get('/logout', function () {
    // Auth::logout();
    return redirect('http://localhost:8080/auth/realms/PCRU-SSO/protocol/openid-connect/logout?redirect_uri=' . urlencode(env('APP_URL') . '/login'));
    return redirect()->route('login.keycloak');
});
