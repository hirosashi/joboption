<?php
declare(strict_types=1);

namespace App\Controllers;

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
            'jobs' => $latest['rows'],
            'total' => $latest['total'],
            'prefs' => Master::activePrefectures(),
            'categories' => Master::categories(),
            'tags' => Master::tags(),
        ]);
    }
}
