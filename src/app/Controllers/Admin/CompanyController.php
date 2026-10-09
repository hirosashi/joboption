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
use App\Core\Validator;
use App\Core\View;

final class CompanyController extends Base
{
    private const FIELDS = ['name', 'contact_name', 'tel', 'email', 'zip', 'address', 'url', 'note'];

    public static function index(): void
    {
        Auth::requireLogin();
        $rows = Db::rows("SELECT c.*, (SELECT COUNT(*) FROM jobs j WHERE j.company_id = c.id AND j.status = 'published') AS job_count FROM companies c ORDER BY c.id DESC");
        View::render('admin/companies/index', ['title' => '企業', 'rows' => $rows], 'admin/layout');
    }

    public static function edit(): void
    {
        Auth::requireLogin();
        $id = App::intParam('id');
        $c = $id > 0 ? Db::row('SELECT * FROM companies WHERE id = ?', [$id]) : null;
        if ($id > 0 && $c === null) {
            self::notFound();
        }
        $c ??= array_fill_keys(self::FIELDS, '') + ['id' => 0];
        $jobs = $id > 0 ? Db::rows('SELECT id, title, status FROM jobs WHERE company_id = ? ORDER BY id DESC', [$id]) : [];
        View::render('admin/companies/edit', ['title' => $id ? '企業を直す' : '企業を登録', 'c' => $c, 'jobs' => $jobs], 'admin/layout');
    }

    public static function save(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $id = App::intParam('id');
        $v = new Validator($_POST);
        $v->required('name', '会社名')->maxLength('name', 200, '会社名');
        $errors = $v->errors();
        $email = trim((string)($_POST['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'メールアドレスの形式が正しくありません。';
        }
        if ($errors !== []) {
            Session::flash('error', implode(' ', $errors));
            App::redirect('/admin/companies/edit?id=' . $id);
        }
        $d = [];
        foreach (self::FIELDS as $k) {
            $d[$k] = Validator::toStr($_POST[$k] ?? '');
        }
        $now = Clock::nowStr();
        $d['updated_at'] = $now;
        if ($id > 0) {
            Db::exec('UPDATE companies SET ' . implode(',', array_map(static fn(string $c): string => "$c = ?", array_keys($d))) . ' WHERE id = ?', [...array_values($d), $id]);
        } else {
            $d['created_at'] = $now;
            Db::exec('INSERT INTO companies (' . implode(',', array_keys($d)) . ') VALUES (' . implode(',', array_fill(0, count($d), '?')) . ')', array_values($d));
            $id = Db::lastId();
        }
        Session::flash('ok', '保存しました。');
        App::redirect('/admin/companies/edit?id=' . $id);
    }
}
