<?php
declare(strict_types=1);

namespace App\Core;

/** 運営管理者のログイン */
final class Auth
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_MINUTES = 15;

    /** @return array<string, mixed>|null */
    public static function user(): ?array
    {
        $id = Session::get('admin_id');
        if (!is_int($id)) {
            return null;
        }
        static $cache = null;
        if ($cache === null || ($cache['id'] ?? null) !== $id) {
            $cache = Db::row('SELECT id, login_id, name FROM admins WHERE id = ? AND is_active = 1', [$id]);
        }
        return $cache;
    }

    public static function requireLogin(): void
    {
        if (self::user() === null) {
            Session::set('after_login', App::currentPath());
            App::redirect('/admin/login');
        }
    }

    /** @return string|null エラーメッセージ（成功時 null） */
    public static function attempt(string $loginId, string $password): ?string
    {
        $la = Db::row('SELECT attempts, locked_until FROM login_attempts WHERE login_id = ?', [$loginId]);
        if ($la !== null && $la['locked_until'] !== null && (string)$la['locked_until'] > Clock::nowStr()) {
            return 'ログインが一時的にロックされています。しばらく待ってからやり直してください。';
        }
        $u = Db::row('SELECT * FROM admins WHERE login_id = ? AND is_active = 1', [$loginId]);
        if ($u === null || !password_verify($password, (string)$u['password_hash'])) {
            $attempts = ($la === null ? 0 : (int)$la['attempts']) + 1;
            $locked = null;
            if ($attempts >= self::MAX_ATTEMPTS) {
                $locked = Clock::now()->modify('+' . self::LOCK_MINUTES . ' minutes')->format('Y-m-d H:i:s');
                $attempts = 0;
            }
            Db::exec('DELETE FROM login_attempts WHERE login_id = ?', [$loginId]);
            Db::exec('INSERT INTO login_attempts (login_id, attempts, locked_until, updated_at) VALUES (?,?,?,?)', [$loginId, $attempts, $locked, Clock::nowStr()]);
            return 'IDまたはパスワードが違います。';
        }
        Db::exec('DELETE FROM login_attempts WHERE login_id = ?', [$loginId]);
        Session::regenerate();
        Session::set('admin_id', (int)$u['id']);
        Db::exec('UPDATE admins SET last_login_at = ? WHERE id = ?', [Clock::nowStr(), (int)$u['id']]);
        return null;
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}
