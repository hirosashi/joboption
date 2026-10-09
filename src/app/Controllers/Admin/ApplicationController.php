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

final class ApplicationController extends Base
{
    private const SELECT = 'SELECT a.*, j.title AS job_title, j.job_name, c.name AS company_name
        FROM applications a JOIN jobs j ON j.id = a.job_id JOIN companies c ON c.id = j.company_id';

    /** @return array{0: string, 1: array<int, mixed>} */
    private static function filter(): array
    {
        $where = '1=1';
        $params = [];
        $status = App::param('status');
        if (isset(Master::APP_STATUS[$status])) {
            $where .= ' AND a.status = ?';
            $params[] = $status;
        }
        if (($jobId = App::intParam('job_id')) > 0) {
            $where .= ' AND a.job_id = ?';
            $params[] = $jobId;
        }
        return [$where, $params];
    }

    public static function index(): void
    {
        Auth::requireLogin();
        [$where, $params] = self::filter();
        $rows = Db::rows(self::SELECT . " WHERE $where ORDER BY a.id DESC", $params);
        View::render('admin/applications/index', ['title' => '応募', 'rows' => $rows, 'status' => App::param('status'), 'jobId' => App::intParam('job_id')], 'admin/layout');
    }

    public static function view(): void
    {
        Auth::requireLogin();
        $a = Db::row(self::SELECT . ' WHERE a.id = ?', [App::intParam('id')]);
        if ($a === null) {
            self::notFound();
        }
        View::render('admin/applications/view', ['title' => '応募の内容', 'a' => $a], 'admin/layout');
    }

    public static function save(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $id = App::intParam('id');
        $status = App::param('status');
        if (!isset(Master::APP_STATUS[$status])) {
            $status = 'new';
        }
        Db::exec('UPDATE applications SET status = ?, memo = ?, updated_at = ? WHERE id = ?', [$status, mb_substr(App::param('memo'), 0, 2000), Clock::nowStr(), $id]);
        Session::flash('ok', '保存しました。');
        App::redirect('/admin/applications/view?id=' . $id);
    }

    public static function csv(): void
    {
        Auth::requireLogin();
        [$where, $params] = self::filter();
        $rows = Db::rows(self::SELECT . " WHERE $where ORDER BY a.id", $params);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="applications_' . Clock::now()->format('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, ['応募No', '応募日時', '状況', '求人No', '求人', '企業', 'お名前', '電話番号', 'メール', 'ひとこと', 'メモ']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['id'], View::dt((string)$r['created_at']), Master::APP_STATUS[$r['status']] ?? $r['status'], $r['job_id'], $r['job_title'], $r['company_name'], $r['name'], $r['tel'], $r['email'], $r['message'], $r['memo']]);
        }
        fclose($out);
    }
}
