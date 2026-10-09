<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Core\App;
use App\Core\Auth;
use App\Core\Clock;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Session;
use App\Core\View;
use App\Services\Master;

/** 企業からの掲載申込 */
final class RequestController extends Base
{
    public static function index(): void
    {
        Auth::requireLogin();
        View::render('admin/requests', ['title' => '掲載申込', 'rows' => Db::rows('SELECT * FROM listing_requests ORDER BY id DESC')], 'admin/layout');
    }

    public static function save(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $status = App::param('status');
        if (isset(Master::REQUEST_STATUS[$status])) {
            Db::exec('UPDATE listing_requests SET status = ?, updated_at = ? WHERE id = ?', [$status, Clock::nowStr(), App::intParam('id')]);
            Session::flash('ok', '更新しました。');
        }
        App::redirect('/admin/requests');
    }
}
