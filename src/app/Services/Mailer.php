<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\App;
use App\Core\Clock;

/** 通知メール。config の mail.enabled=false の間は storage/logs/mail.log に書くだけ */
final class Mailer
{
    public static function send(string $to, string $subject, string $body): bool
    {
        $to = trim($to);
        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $cfg = App::config()['mail'] ?? [];
        $siteName = (string)(App::config()['site']['name'] ?? 'お仕事55号');
        $body .= "\n\n----\n" . $siteName . "\n" . self::siteUrl() . "\n";
        if (empty($cfg['enabled'])) {
            $log = dirname(__DIR__, 2) . '/storage/logs/mail.log';
            @file_put_contents($log, '[' . Clock::nowStr() . "] To: {$to}\nSubject: {$subject}\n{$body}\n\n", FILE_APPEND);
            return true;
        }
        mb_language('Japanese');
        mb_internal_encoding('UTF-8');
        $from = (string)($cfg['from'] ?? '');
        $headers = 'From: ' . mb_encode_mimeheader($siteName) . ' <' . $from . '>';
        return mb_send_mail($to, $subject, $body, $headers, '-f' . $from);
    }

    public static function adminTo(): string
    {
        return (string)(App::config()['mail']['admin_to'] ?? '');
    }

    public static function siteUrl(): string
    {
        return rtrim((string)(App::config()['site']['url'] ?? ''), '/') . '/';
    }
}
