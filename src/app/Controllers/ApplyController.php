<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Clock;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Session;
use App\Core\View;
use App\Services\Jobs;
use App\Services\Mailer;

/** 応募（会員登録なし）: 入力 → 確認 → 送信 → 完了 */
final class ApplyController extends Base
{
    private const FIELDS = ['name', 'tel', 'email', 'message'];

    /** @return array<string, mixed> */
    private static function job(int $id): array
    {
        $job = Jobs::findPublic($id);
        if ($job === null) {
            self::notFound();
        }
        return $job;
    }

    public static function form(int $id): void
    {
        $job = self::job($id);
        $draft = Session::get('apply_' . $id);
        View::render('apply/form', [
            'title' => '応募する｜' . $job['job_name'],
            'job' => $job,
            'v' => is_array($draft) ? $draft : array_fill_keys(self::FIELDS, ''),
            'errors' => Session::get('apply_errors_' . $id) ?? [],
            'noindex' => true,
        ]);
        Session::remove('apply_errors_' . $id);
    }

    public static function confirm(int $id): void
    {
        $job = self::job($id);
        Csrf::verify();
        if (self::isBot()) {
            App::redirect('/jobs/' . $id);
        }
        $v = self::postStrings(self::FIELDS);
        $v['tel'] = self::normalizeTel($v['tel']);
        $errors = [];
        if ($v['name'] === '') {
            $errors[] = 'お名前を入力してください。';
        } elseif (mb_strlen($v['name']) > 50) {
            $errors[] = 'お名前は50文字以内で入力してください。';
        }
        if ($v['tel'] === '' && $v['email'] === '') {
            $errors[] = '電話番号かメールアドレスのどちらかを入力してください。';
        }
        if ($v['tel'] !== '' && !self::validTel($v['tel'])) {
            $errors[] = '電話番号を正しく入力してください（例 090-1234-5678）。';
        }
        if ($v['email'] !== '' && !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'メールアドレスを正しく入力してください。';
        }
        if (mb_strlen($v['message']) > 1000) {
            $errors[] = 'ひとことは1000文字以内で入力してください。';
        }
        Session::set('apply_' . $id, $v);
        Session::remove('apply_ok_' . $id);
        if ($errors !== []) {
            Session::set('apply_errors_' . $id, $errors);
            App::redirect('/jobs/' . $id . '/apply');
        }
        Session::set('apply_ok_' . $id, true);
        View::render('apply/confirm', ['title' => '入力内容の確認', 'job' => $job, 'v' => $v, 'noindex' => true]);
    }

    public static function send(int $id): void
    {
        $job = self::job($id);
        Csrf::verify();
        $v = Session::get('apply_' . $id);
        if (!is_array($v) || Session::get('apply_ok_' . $id) !== true) {
            App::redirect('/jobs/' . $id . '/apply');
        }
        $now = Clock::nowStr();
        Db::exec(
            'INSERT INTO applications (job_id, name, tel, email, message, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',
            [$id, $v['name'], $v['tel'] ?: null, $v['email'] ?: null, $v['message'] ?: null, 'new', $now, $now]
        );
        $appId = Db::lastId();
        Session::remove('apply_' . $id);
        Session::remove('apply_ok_' . $id);
        Session::set('applied_' . $id, true);

        $summary = "求人：{$job['title']}（No.{$id}）\n会社：{$job['company_name']}\n\nお名前：{$v['name']}\n電話番号：{$v['tel']}\nメール：{$v['email']}\nひとこと：\n{$v['message']}\n";
        $subject = '【' . self::siteName() . '】応募がありました（' . $job['job_name'] . '）';
        Mailer::send((string)($job['company_email'] ?? ''), $subject, "求人に応募がありました。応募者へご連絡をお願いします。\n\n" . $summary);
        Mailer::send(Mailer::adminTo(), $subject . ' 応募No.' . $appId, $summary);
        if ($v['email'] !== '') {
            Mailer::send($v['email'], '【' . self::siteName() . '】ご応募ありがとうございます', "{$v['name']} 様\n\nご応募ありがとうございます。以下の内容で受け付けました。\n担当者から数日以内にご連絡します。\n\n" . $summary);
        }
        App::redirect('/jobs/' . $id . '/apply/done');
    }

    public static function done(int $id): void
    {
        $job = self::job($id);
        if (Session::get('applied_' . $id) !== true) {
            App::redirect('/jobs/' . $id);
        }
        View::render('apply/done', ['title' => '応募が完了しました', 'job' => $job, 'noindex' => true]);
    }
}
