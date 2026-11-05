<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Master;

final class HomeController extends Base
{
    public static function index(): void
    {
        $latest = Jobs::search([], 1, 6);
        View::render('home/index', [
            'title' => '',
            'description' => 'お仕事55号は、シニア世代が経験をいかして活躍できるお仕事を集めた求人サイトです。週2日・短時間のお仕事も。会員登録なしで、お名前と連絡先だけでかんたんに応募できます。',
            'jsonLd' => (string)json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => (string)(App::config()['site']['name'] ?? 'お仕事55号'),
                'url' => App::absUrl('/'),
                'potentialAction' => ['@type' => 'SearchAction', 'target' => App::absUrl('/jobs') . '?q={search_term_string}', 'query-input' => 'required name=search_term_string'],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG),
            'jobs' => $latest['rows'],
            'total' => $latest['total'],
            'prefs' => Master::activePrefectures(),
            'categories' => Master::categories(),
            'tags' => Master::tags(),
        ]);
    }
}
