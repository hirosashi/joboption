<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Core\Auth;
use App\Core\Clock;
use App\Core\Db;
use App\Core\View;

final class AdminHomeController extends Base
{
    public static function index(): void
    {
        Auth::requireLogin();
        View::render('admin/home', [
            'title' => 'ホーム',
            'published' => (int)Db::value("SELECT COUNT(*) FROM jobs WHERE status = 'published' AND (valid_until IS NULL OR valid_until >= ?)", [Clock::today()]),
            'expired' => (int)Db::value("SELECT COUNT(*) FROM jobs WHERE status = 'published' AND valid_until < ?", [Clock::today()]),
            'newApps' => (int)Db::value("SELECT COUNT(*) FROM applications WHERE status = 'new'"),
            'newRequests' => (int)Db::value("SELECT COUNT(*) FROM listing_requests WHERE status = 'new'"),
            'apps' => Db::rows('SELECT a.*, j.job_name, c.name AS company_name FROM applications a JOIN jobs j ON j.id = a.job_id JOIN companies c ON c.id = j.company_id ORDER BY a.id DESC LIMIT 5'),
        ], 'admin/layout');
    }
}
