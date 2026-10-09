<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;

final class JobController extends Base
{
    public static function index(): void
    {
        $f = [
            'pref' => App::param('pref'),
            'cat' => App::intParam('cat'),
            'tag' => App::intParam('tag'),
            'q' => mb_substr(App::param('q'), 0, 50),
        ];
        $page = max(1, App::intParam('page', 1));
        $res = Jobs::search($f, $page);
        $categories = Master::categories();
        $tags = Master::tags();
        $label = array_filter([$f['pref'], $categories[$f['cat']] ?? '', $tags[$f['tag']] ?? '', $f['q']]);
        View::render('jobs/index', [
            'title' => ($label === [] ? 'お仕事一覧' : implode('・', $label) . 'のお仕事'),
            'f' => $f,
            'jobs' => $res['rows'],
            'total' => $res['total'],
            'page' => $page,
            'pages' => max(1, (int)ceil($res['total'] / Jobs::PER_PAGE)),
            'prefs' => Master::activePrefectures(),
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public static function show(int $id): void
    {
        $job = Jobs::findPublic($id);
        if ($job === null) {
            self::notFound();
        }
        View::render('jobs/show', [
            'title' => (string)$job['title'],
            'description' => mb_substr((string)($job['catch_copy'] ?: $job['description']), 0, 110),
            'job' => $job,
            'jsonLd' => Jobs::jsonLd($job),
        ]);
    }
}
