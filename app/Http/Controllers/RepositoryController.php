<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Support\Facades\Http;
use Khill\Lavacharts\Lavacharts;

class RepositoryController extends Controller
{
    public function __construct()
    {
        $this->http = Http::withBasicAuth(env('GITHUB_CLIENT_ID'), env('GITHUB_CLIENT_SECRET'));
    }
    public function index()
    {
        $username = auth()->user()->github_nickname;
        // Query only repositories that had commits on the last 90 days
        $since = now()->subDays(90);
        $url = "https://api.github.com/users/{$username}/repos?since={$since}&per_page=100";

        $repositories = $this->fetchRepositories($url);

        return view('repositories.index', ['repositories' => $repositories]);
    }

    public function show($repository)
    {
        $username = auth()->user()->github_nickname;
        $url = "https://api.github.com/repos/{$username}/{$repository}/stats/commit_activity";

        $commits = $this->fetchCommints($url, $repository);

        $lava = new Lavacharts;
        $data = $lava->DataTable();
        $data->addDateColumn('Day')
            ->addNumberColumn('Commits');

        foreach ($commits as $index => $commit) {
            $data->addRow([now()->subDays(90 - (int)$index), $commit]);
        }

        $lava->AreaChart('Commits', $data, [
            'title' => 'Commits per Day',
            'elementId' => 'chart',
            'legend' => [
                'position' => 'in'
            ]
        ]);

        return view('repositories.show', compact('lava'));
    }

    private function fetchRepositories($url)
    {
        return $this->http
            ->get($url)
            ->collect()
            ->map(fn ($repository) => (object)['name' => $repository['name']]);
    }

    private function fetchCommints($url, $repositoryName)
    {
        $repositoryExists = Repository::where([
            ['user_id', auth()->user()->id],
            ['name', $repositoryName]
        ])->exists();

        if (!$repositoryExists) {

            $commits = $this->http
                ->get($url)
                ->collect()
                // Get commits only from the last 90 days
                ->whereBetween('week', [now()->subDays(91)->timestamp, now()->timestamp])
                ->map(fn ($week) => $week['days'])
                ->collapse()
                ->toArray();

            if ($commits === []) {
                $this->fetchCommints($url, $repositoryName);
            }

            Repository::create([
                'name' => $repositoryName,
                'commits' => $commits,
                'user_id' => auth()->user()->id,
            ]);
        } else {
            $repository = Repository::where([
                ['user_id', auth()->user()->id],
                ['name', $repositoryName]
            ])->first(['commits']);

            $commits = $repository->commits;
        }

        return $commits;
    }
}
