<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Khill\Lavacharts\Lavacharts;

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

    return view('welcome', ['repos' => $repos]);
})->middleware('auth');

Route::get('repos/{repo}', function ($repo) {
    $username = auth()->user()->github_nickname;
    $url = "https://api.github.com/repos/{$username}/{$repo}/stats/commit_activity";
    $commits = Http::withBasicAuth(env('GITHUB_CLIENT_ID'), env('GITHUB_CLIENT_SECRET'))
        ->get($url)
        ->collect()
        // Get commits only from the last 90 days
        ->whereBetween('week', [now()->subDays(91)->timestamp, now()->timestamp])
        ->map(fn ($week) => $week['days'])
        ->collapse();

    $commits = cache()->remember(
        "repos.{$repo}",
        60,
        fn () => Http::withBasicAuth(env('GITHUB_CLIENT_ID'), env('GITHUB_CLIENT_SECRET'))
            ->get($url)
            ->collect()
            // Get commits only from the last 90 days
            ->whereBetween('week', [now()->subDays(91)->timestamp, now()->timestamp])
            ->map(fn ($week) => $week['days'])
            ->collapse()
    );

    $lava = new Lavacharts;
    $data = $lava->DataTable();
    $data->addDateColumn('Day')
        ->addNumberColumn('Commits');

    foreach ($commits as $index => $commit) {
        $data->addRow([now()->subDays(90 - (int)$index), $commit]);
    }

    $lava->AreaChart('Github Commits', $data, [
        'title' => 'Commits per Day',
        'elementId' => 'pop-div',
        'legend' => [
            'position' => 'in'
        ]
    ]);

    return view('chart', compact('lava'));
});

require __DIR__ . '/auth.php';
