<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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
    $username = auth()->user()->github_nickname;
    // Query only repositories that had commits on the last 90 days
    $since = now()->subDays(90);
    $url = "https://api.github.com/users/{$username}/repos?since={$since}&per_page=100";
    $repos = Http::withBasicAuth(env('GITHUB_CLIENT_ID'), env('GITHUB_CLIENT_SECRET'))
        ->get($url)
        ->collect()
        ->map(fn ($repo) => (object)['name' => $repo['name']]);

    return $repos;

    return view('welcome', ['repos' => $repos]);
})->middleware('auth');

Route::get('repos/{repo}', function ($repo) {
    $username = auth()->user()->github_nickname;
    // Query only commits on the last 90 days
    $since = now()->subDays(90);
    $url = "https://api.github.com/repos/{$username}/{$repo}/commits?since={$since}&per_page=100";
    $commits = Http::withBasicAuth(env('GITHUB_CLIENT_ID'), env('GITHUB_CLIENT_SECRET'))
        ->get($url)
        ->collect();

    return $commits;
});

require __DIR__ . '/auth.php';
