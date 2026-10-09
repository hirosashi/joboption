<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Clock;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\Session;
use App\Core\View;
use App\Services\Mailer;

final class PageController extends Base
{
    private const REQUEST_FIELDS = ['company_name', 'contact_name', 'tel', 'email', 'message'];

    public static function companies(): void
    {
        $draft = Session::get('listing_request');
        View::render('pages/companies', [
            'title' => '掲載をご希望の企業様へ',
            'v' => is_array($draft) ? $draft : array_fill_keys(self::REQUEST_FIELDS, ''),
            'errors' => Session::get('listing_errors') ?? [],
        ]);
        Session::remove('listing_errors');
    }

    public static function companiesSend(): void
    {
        Csrf::verify();
        if (self::isBot()) {
            App::redirect('/for-companies');
        }
        $v = self::postStrings(self::REQUEST_FIELDS);
        $v['tel'] = self::normalizeTel($v['tel']);
        $errors = [];
        if ($v['company_name'] === '' || mb_strlen($v['company_name']) > 100) {
            $errors[] = '会社名・店舗名を入力してください（100文字以内）。';
        }
        if ($v['contact_name'] === '' || mb_strlen($v['contact_name']) > 50) {
            $errors[] = 'ご担当者名を入力してください（50文字以内）。';
        }
        if ($v['tel'] === '' && $v['email'] === '') {
            $errors[] = '電話番号かメールアドレスのどちらかを入力してください。';
        }
        if ($v['tel'] !== '' && !self::validTel($v['tel'])) {
            $errors[] = '電話番号を正しく入力してください。';
        }
        if ($v['email'] !== '' && !filter_var($v['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'メールアドレスを正しく入力してください。';
        }
        if (mb_strlen($v['message']) > 2000) {
            $errors[] = 'ご相談内容は2000文字以内で入力してください。';
        }
        if ($errors !== []) {
            Session::set('listing_request', $v);
            Session::set('listing_errors', $errors);
            App::redirect('/for-companies#form');
        }
        $now = Clock::nowStr();
        Db::exec(
            'INSERT INTO listing_requests (company_name, contact_name, tel, email, message, status, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?)',
            [$v['company_name'], $v['contact_name'], $v['tel'] ?: null, $v['email'] ?: null, $v['message'] ?: null, 'new', $now, $now]
        );
        Session::remove('listing_request');
        Session::set('listing_sent', true);
        $body = "会社名：{$v['company_name']}\nご担当者：{$v['contact_name']}\n電話番号：{$v['tel']}\nメール：{$v['email']}\nご相談内容：\n{$v['message']}\n";
        Mailer::send(Mailer::adminTo(), '【' . self::siteName() . '】掲載のお申し込みがありました', $body);
        App::redirect('/for-companies/done');
    }

    public static function companiesDone(): void
    {
        if (Session::get('listing_sent') !== true) {
            App::redirect('/for-companies');
        }
        View::render('pages/companies_done', ['title' => 'お申し込みを受け付けました', 'noindex' => true]);
    }

    public static function about(): void
    {
        View::render('pages/about', ['title' => '運営者情報']);
    }

    public static function terms(): void
    {
        View::render('pages/terms', ['title' => '利用規約']);
    }

    public static function privacy(): void
    {
        View::render('pages/privacy', ['title' => 'プライバシーポリシー']);
    }

    public static function robots(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        if (!empty(App::config()['noindex'])) {
            echo "User-agent: *\nDisallow: /\n";
            return;
        }
        echo "User-agent: *\nDisallow: " . App::url('/admin') . "\nSitemap: " . Mailer::siteUrl() . "sitemap.xml\n";
    }

    public static function sitemap(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        $base = rtrim(Mailer::siteUrl(), '/');
        $rows = Db::rows("SELECT id, updated_at FROM jobs WHERE status = 'published' AND (valid_until IS NULL OR valid_until >= ?) ORDER BY id", [Clock::today()]);
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (['/', '/jobs', '/for-companies'] as $p) {
            echo '<url><loc>' . View::e($base . ($p === '/' ? '/' : $p)) . "</loc></url>\n";
        }
        foreach ($rows as $r) {
            echo '<url><loc>' . View::e($base . '/jobs/' . $r['id']) . '</loc><lastmod>' . View::e(substr((string)$r['updated_at'], 0, 10)) . "</lastmod></url>\n";
        }
        echo "</urlset>\n";
    }
}
