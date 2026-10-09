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

final class AdminAuthController extends Base
{
    public static function loginForm(): void
    {
        if (Auth::user() !== null) {
            App::redirect('/admin');
        }
        View::render('admin/login', ['title' => 'ログイン'], 'admin/layout');
    }

    public static function login(): void
    {
        Csrf::verify();
        $loginId = App::param('login_id');
        $password = (string)($_POST['password'] ?? '');
        $err = ($loginId === '' || $password === '') ? 'IDとパスワードを入力してください。' : Auth::attempt($loginId, $password);
        if ($err !== null) {
            Session::flash('error', $err);
            App::redirect('/admin/login');
        }
        $after = (string)(Session::get('after_login') ?? '/admin');
        Session::remove('after_login');
        App::redirect(str_starts_with($after, '/admin') && $after !== '/admin/login' ? $after : '/admin');
    }

    public static function logout(): void
    {
        Csrf::verify();
        Auth::logout();
        App::redirect('/admin/login');
    }

    public static function passwordForm(): void
    {
        Auth::requireLogin();
        View::render('admin/password', ['title' => 'パスワード変更'], 'admin/layout');
    }

    public static function password(): void
    {
        Auth::requireLogin();
        Csrf::verify();
        $u = Auth::user();
        $full = Db::row('SELECT password_hash FROM admins WHERE id = ?', [(int)$u['id']]);
        $new = (string)($_POST['new'] ?? '');
        if ($full === null || !password_verify((string)($_POST['current'] ?? ''), (string)$full['password_hash'])) {
            Session::flash('error', '現在のパスワードが違います。');
            App::redirect('/admin/password');
        }
        if (strlen($new) < 8 || $new !== (string)($_POST['new2'] ?? '')) {
            Session::flash('error', '新しいパスワードは8文字以上で、確認用と一致させてください。');
            App::redirect('/admin/password');
        }
        Db::exec('UPDATE admins SET password_hash = ?, updated_at = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), Clock::nowStr(), (int)$u['id']]);
        Session::flash('ok', 'パスワードを変更しました。');
        App::redirect('/admin');
    }
}
